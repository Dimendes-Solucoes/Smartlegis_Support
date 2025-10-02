<?php

namespace App\Services;

use App\Libraries\ImageUploader;
use App\Models\Credential;
use App\Models\Tenant;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CredentialService
{
    public function getAllCredentials(): Collection
    {
        return Credential::with('tenant')->orderBy('city_name')->get();
    }

    public function getCreationFormData(): array
    {
        return [
            'tenants' => Tenant::whereDoesntHave('credential')->orderBy('city_name')->get(['id', 'city_name']),
        ];
    }

    public function createCredential(array $data): Credential
    {
        try {
            DB::beginTransaction();
            $credentialData = $this->prepareCredentialData($data);
            $credential = Credential::create($credentialData);
            DB::commit();
            return $credential;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao criar credencial: " . $e->getMessage());
        }
    }

    public function getCredentialForEdit(int $id): array
    {
        $credential = Credential::with('tenant')->findOrFail($id);
        return [
            'credential' => $credential,
            'tenants' => Tenant::all(['id', 'city_name']),
        ];
    }

    public function updateCredential(int $id, array $data): Credential
    {
        try {
            DB::beginTransaction();
            $credential = Credential::findOrFail($id);
            $credentialData = $this->prepareCredentialData($data, $credential);
            $credential->update($credentialData);
            DB::commit();
            return $credential;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao atualizar credencial: " . $e->getMessage());
        }
    }

    public function destroyCredential(int $id): void
    {
        $credential = Credential::findOrFail($id);
        if ($credential->city_shield) {
            Storage::disk('public')->delete($credential->city_shield);
        }
        $credential->delete();
    }

    private function prepareCredentialData(array $data, ?Credential $existingCredential = null): array
    {
        if (isset($data['city_shield']) && $data['city_shield'] instanceof UploadedFile) {
            if ($existingCredential && $existingCredential->city_shield) {
                Storage::disk('public')->delete($existingCredential->city_shield);
            }
            
            $tenantId = $data['tenant_id'] ?? $existingCredential->tenant_id;
            $tenant = Tenant::find($tenantId);
            $path = "tenants/{$tenant->getTenantKey()}/shields";
            $data['city_shield'] = ImageUploader::handleImageUpload($data['city_shield'], $path);
        } else {
            unset($data['city_shield']);
        }
        
        if ($existingCredential && empty($data['key'])) {
            unset($data['key']);
        }

        return $data;
    }
}