<?php

namespace App\Services;

use App\Libraries\ClicksignApi;
use App\Libraries\StorageCustom;
use App\Models\Tenancy\CategoryParty;
use App\Models\Tenancy\User;
use App\Models\Tenancy\UserCategory;
use App\Models\Tenancy\UserTerm;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CouncilorService
{
    public function list(bool $showInactive = false)
    {
        return User::query() 
            ->whereIn('user_category_id', UserCategory::LEGISLATIVO)
            ->with('party', 'category')
            ->when(!$showInactive, function ($query) {
                $query->where('status_user', User::USUARIO_VEREADOR);
            })
            ->orderBy('user_category_id', 'desc')
            ->orderBy('status_user', 'asc')
            ->orderBy('name')
            ->get();
    }

    public function getCreationFormData(): array
    {
        $categories = UserCategory::whereIn('id', UserCategory::LEGISLATIVO)
            ->orderBy('name')
            ->get();

        $parties = CategoryParty::orderBy('name_party')->get();

        return [
            'categories' => $categories,
            'parties' => $parties
        ];
    }

    public function createCouncilor(array $data): User
    {
        try {
            DB::beginTransaction();

            $userData = $this->prepareUserData($data);

            $user = User::create($userData);

            $this->createTerm($user);

            if ($user->user_category_id == UserCategory::PRESIDENTE) {
                $this->demotePreviousPresident($user);
            }

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao criar usuário: " . $e->getMessage());
        }
    }

    public function getUserForEdit(int $id)
    {
        $user = User::findOrFail($id);
        $user->load('category', 'party');

        $categories = UserCategory::whereIn('id', UserCategory::LEGISLATIVO)
            ->orderBy('name')
            ->get();

        $parties = CategoryParty::orderBy('name_party')->get();

        return [
            'user' => $user,
            'categories' => $categories,
            'parties' => $parties
        ];
    }

    public function updateUser(int $id, array $data): User
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);
            $category_id = $user->user_category_id;

            $userData = $this->prepareUserData($data, $user);

            $user->update($userData);

            if ($category_id != UserCategory::PRESIDENTE && $data['category_id'] == UserCategory::PRESIDENTE) {
                $this->demotePreviousPresident($user);
            }

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao atualizar vereador: " . $e->getMessage());
        }
    }

    public function changeStatus(int $id): User
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            if ($user->user_category_id == UserCategory::PRESIDENTE) {
                throw new Exception("O presidente não pode ser inativado");
            }

            $this->endTerms($user);

            if ($user->status_user == User::USUARIO_VEREADOR) {
                $user->status_user = User::USUARIO_INVISIVEL;
            } else {
                $user->status_user = User::USUARIO_VEREADOR;
                $this->createTerm($user);
            }

            $user->save();

            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao atualizar status do vereador: " . $e->getMessage());
        }
    }

    private function createTerm(User $user)
    {
        UserTerm::create([
            'user_id' => $user->id,
            'start_date' => date('Y-m-d')
        ]);
    }

    private function endTerms(User $user)
    {
        UserTerm::where('user_id', $user->id)
            ->whereNull('end_date')
            ->update([
                'end_date' => date('Y-m-d')
            ]);
    }

    private function demotePreviousPresident(User $user)
    {
        try {
            User::where('user_category_id', UserCategory::PRESIDENTE)
                ->where('id', '!=', $user->id)
                ->update(['user_category_id' => UserCategory::VEREADOR]);
        } catch (Exception $e) {
            throw new Exception("Erro ao atualizar presidente: " . $e->getMessage());
        }
    }

    private function prepareUserData(array $data, ?User $existingUser = null): array
    {
        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } elseif ($existingUser && !isset($data['password'])) {
            unset($data['password']);
        }

        $data['status_user'] = User::USUARIO_VEREADOR;
        $data['user_category_id'] = $data['category_id'] ?? null;
        $data['category_party_id'] = $data['party_id'] ?? null;
        $data['status_lider'] = $data['is_leader'] ?? false;

        if (isset($data['path_image']) && !empty($data['path_image'])) {
            $data['path_image'] = $this->handleImageUpload($data['path_image']);
        } else {
            unset($data['path_image']);
        }

        if (isset($data['email']) || isset($data['name'])) {
            if (!$existingUser) {
                $data['signer_key_clicksign'] = $this->createSignerKey($data['email'], $data['name']);
            } elseif ($existingUser && $existingUser) {
                $emailChanged = isset($data['email']) && $data['email'] !== $existingUser->email;
                $nameChanged = isset($data['name']) && $data['name'] !== $existingUser->name;

                if ($emailChanged || $nameChanged) {
                    $data['signer_key_clicksign'] = $this->createSignerKey($data['email'], $data['name']);
                }
            }
        }

        $fieldsToRemove = [
            'password_confirmation',
            'category_id',
            'party_id',
            'is_leader'
        ];

        return Arr::except($data, $fieldsToRemove);
    }

    private function handleImageUpload(?UploadedFile $imageFile): ?string
    {
        if ($imageFile instanceof UploadedFile) {
            $extension = $imageFile->getClientOriginalExtension();
            $file_name = Str::random(40) . '.' . $extension;

            return "/" . StorageCustom::putFileAs('imagens_user', $imageFile, $file_name);
        }

        return null;
    }

    private function createSignerKey(string $email, string $name): string
    {
        $data = ClicksignApi::createSigner($email, $name);
        return $data['signer']['key'] ?? '';
    }
}
