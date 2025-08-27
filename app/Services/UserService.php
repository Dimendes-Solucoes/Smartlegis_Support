<?php

namespace App\Services;

use App\Libraries\ImageUploader;
use App\Libraries\StorageCustom;
use App\Models\Tenancy\CategoryParty;
use App\Models\Tenancy\User;
use App\Models\Tenancy\UserCategory;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

            if ($user->category_id == UserCategory::PREFEITURA) {
                $this->updateMayor($user);
            }

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

        if ($user->user_category_id !== $userData['user_category_id']) {
            $this->canChangeCategory($user);
        }

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

    public function delete(int $id)
    {
        $userToDelete = User::findOrFail($id);

        if ($userToDelete->category->id == UserCategory::PREFEITURA) {
            throw new Exception("Não é possível deletar usuários prefeitura");
        }

        $this->canChangeCategory($userToDelete);

        $userToDelete->delete();

        return true;
    }

    private function prepareUserData(array $data): array
    {
        $data['password'] = Hash::make($data['password']);

        $data['path_image'] = ImageUploader::handleImageUpload(Arr::get($data, 'path_image'));
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
            $data['path_image'] = ImageUploader::handleImageUpload(Arr::get($data, 'path_image'));
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

    private function determineUserStatus(?int $categoryId): int
    {
        if ($categoryId === UserCategory::SECRETARIO || $categoryId === UserCategory::PREFEITURA) {
            return User::USUARIO_ENVIO_DOCUMENTO;
        }

        return User::USUARIO_INVISIVEL;
    }

    private function canChangeCategory($user)
    {
        if ($user->category->id == UserCategory::SECRETARIO) {
            $otherSecretarios = User::where('id', '!=', $user->id)
                ->where('user_category_id', UserCategory::SECRETARIO)
                ->where('status_user', User::USUARIO_ENVIO_DOCUMENTO)
                ->count();

            if ($otherSecretarios == 0) {
                throw new Exception("É necessário ter outro usuário secretário para este poder ser excluído");
            }
        }

        $usersInSameCategory = User::where('id', '!=', $user->id)
            ->where('user_category_id', $user->category->id)
            ->count();

        if ($usersInSameCategory == 0) {
            throw new Exception("É necessário ter outro usuário dessa categoria para que este possa ser excluído");
        }
    }
}
