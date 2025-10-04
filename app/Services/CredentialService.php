<?php

namespace App\Services;

use App\Libraries\ImageUploader;
use App\Models\Credential;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CredentialService
{
    public function getAllCredentials(): Collection
    {
        return Credential::with('tenant')->orderBy('city_name')->get();
    }

    public function getCreationFormData(): array
    {
        $usedTenantIds = Credential::pluck('tenant_id')->all();

        $availableTenants = Tenant::whereNotIn('id', $usedTenantIds)
            ->orderBy('id')
            ->get()
            ->map(fn($tenant) => [
                'id' => $tenant->id,
                'city_name' => $tenant->data['city_name'] ?? $tenant->id,
            ]);

        return [
            'tenants' => $availableTenants,
        ];
    }

    public function createCredential(array $data): Credential
    {
        if (isset($data['city_shield']) && $data['city_shield'] instanceof UploadedFile) {
            $data['city_shield'] = ImageUploader::handleImageUpload($data['city_shield'], 'shields');
        } else {
            unset($data['city_shield']);
        }

        return Credential::create($data);
    }

    public function getCredentialForEdit(int $id): array
    {
        $credential = Credential::with('tenant')->findOrFail($id);
        return [
            'credential' => $credential,
        ];
    }

    public function updateCredential(int $id, array $data): Credential
    {
        $credential = Credential::findOrFail($id);

        if (isset($data['city_shield']) && $data['city_shield'] instanceof UploadedFile) {
            if ($credential->city_shield) {
                Storage::disk('public')->delete($credential->city_shield);
            }

            $data['city_shield'] = ImageUploader::handleImageUpload($data['city_shield'], 'shields');
        } else {
            unset($data['city_shield']);
        }

        if (empty($data['key'])) {
            unset($data['key']);
        }

        $credential->update($data);
        return $credential;
    }

    public function destroyCredential(int $id): void
    {
        $credential = Credential::findOrFail($id);
        if ($credential->city_shield) {
            Storage::disk('public')->delete($credential->city_shield);
        }
        $credential->delete();
    }
}
