<?php

namespace App\Services;

use App\Libraries\StorageCustom;
use App\Models\Tenancy\CategoryParty;
use App\Models\Tenancy\User;
use App\Models\Tenancy\UserCategory;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService
{
    public function getRegularUsers()
    {
        return User::whereNotIn('user_category_id', UserCategory::LEGISLATIVO)
            ->with('party', 'category')
            ->orderBy('user_category_id')
            ->orderBy('name')
            ->when(true, function ($query) {
                $query->where(function ($q) {
                    $q->where('user_category_id', '!=', UserCategory::PREFEITURA)
                        ->orWhere(function ($q2) {
                            $q2->where('user_category_id', UserCategory::PREFEITURA)
                                ->where('status_user', User::USUARIO_ENVIO_DOCUMENTO);
                        });
                });
            })
            ->get();
    }

    public function getCreationFormData(): array
    {
        $categories = UserCategory::whereNotIn('id', UserCategory::LEGISLATIVO)
            ->orderBy('name')
            ->get();

        return [
            'categories' => $categories
        ];
    }

    public function getReplaceMayorFormData(): array
    {
        $categories = UserCategory::whereIn('id', [UserCategory::PREFEITURA])->get();
        $mayors = User::where('user_category_id', UserCategory::PREFEITURA)->where('status_user', User::USUARIO_INVISIVEL)->get();

        return [
            'categories' => $categories,
            'mayors' => $mayors
        ];
    }

    public function getUserForEdit(int $userId): array
    {
        $user = User::findOrFail($userId);
        $user->load('category', 'party');

        $categories = UserCategory::whereNotIn('id', UserCategory::LEGISLATIVO)
            ->orderBy('name')
            ->get();

        $parties = CategoryParty::orderBy('name_party')->get();

        return [
            'user' => $user,
            'categories' => $categories,
            'parties' => $parties,
        ];
    }

    public function createUser(array $data): User
    {
        try {
            DB::beginTransaction();

            $userData = $this->prepareUserData($data);

            $user = User::create($userData);

            $this->updateMayor($user);

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
    }

    public function updateUser(User $user, array $data): User
    {
        $userData = $this->prepareUpdateUserData($user, $data);

        $user->update($userData);

        return $user;
    }

    public function updateMayor(User $user)
    {
        try {
            User::where('user_category_id', UserCategory::PREFEITURA)
                ->where('status_user', User::USUARIO_ENVIO_DOCUMENTO)
                ->where('id', '!=', $user->id)
                ->update(['status_user' => User::USUARIO_INVISIVEL]);

            $user->status_user = User::USUARIO_ENVIO_DOCUMENTO;
            $user->user_category_id = UserCategory::PREFEITURA;
            $user->save();
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar prefeito: " . $e->getMessage());
        }
    }

    private function prepareUserData(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        $data['path_image'] = $this->handleImageUpload(Arr::get($data, 'path_image'));
        $data['status_user'] = $this->determineUserStatus(Arr::get($data, 'category_id'));
        $data['user_category_id'] = Arr::get($data, 'category_id');

        $fieldsToRemove = [
            'password_confirmation',
            'category_id',
            'party_id',
            '_token'
        ];

        return Arr::except($data, $fieldsToRemove);
    }

    private function prepareUpdateUserData(User $user, array $data): array
    {
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (isset($data['path_image']) && $data['path_image'] instanceof UploadedFile) {
            if ($user->path_image) {
                StorageCustom::delete($user->path_image);
            }
            $data['path_image'] = $this->handleImageUpload(Arr::get($data, 'path_image'));
        } elseif (Arr::get($data, 'path_image') === null && !empty($user->path_image) && !Arr::get($data, 'existing_path_image')) {
            StorageCustom::delete($user->path_image);
            $data['path_image'] = null;
        } elseif (Arr::get($data, 'path_image') === null && !empty(Arr::get($data, 'existing_path_image'))) {
            $data['path_image'] = $user->path_image;
        } else {
            $data['path_image'] = null;
        }

        $data['status_user'] = $this->determineUserStatus(Arr::get($data, 'category_id', $user->user_category_id));
        $data['user_category_id'] = Arr::get($data, 'category_id');

        $fieldsToRemove = [
            'password_confirmation',
            'category_id',
            'party_id',
            '_token',
            'existing_path_image'
        ];

        return Arr::except($data, $fieldsToRemove);
    }

    private function handleImageUpload(?UploadedFile $imageFile): ?string
    {
        if ($imageFile instanceof UploadedFile) {
            $extension = $imageFile->getClientOriginalExtension();
            $file_name = Str::random(40) . '.' . $extension;

            return StorageCustom::putFileAs('imagens_user', $imageFile, $file_name);
        }

        return null;
    }

    private function determineUserStatus(?int $categoryId): int
    {
        if ($categoryId === UserCategory::SECRETARIO || $categoryId === UserCategory::PREFEITURA) {
            return User::USUARIO_ENVIO_DOCUMENTO;
        }

        return User::USUARIO_INVISIVEL;
    }
}
