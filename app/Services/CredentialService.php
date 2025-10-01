<?php

namespace App\Services;

use App\Libraries\ImageUploader;
use App\Models\Credential;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class CredentialService
{
    public function getAllCredentials(): Collection
    {
        return Credential::with('tenant')
            ->orderBy('city_name')
            ->get();
    }
    
    public function createCredential(array $data): Credential
    {
        if (isset($data['city_shield']) && $data['city_shield'] instanceof UploadedFile) {
            $tenant = Tenant::find($data['tenant_id']);
            $path = "tenants/{$tenant->getTenantKey()}/shields";
            $data['city_shield'] = ImageUploader::handleImageUpload($data['city_shield'], $path);
        }
        return Credential::create($data);
    }
    
    public function updateCredential(int $id, array $data): Credential
    {
        $credential = Credential::findOrFail($id);
        if (isset($data['city_shield']) && $data['city_shield'] instanceof UploadedFile) {
            if ($credential->city_shield) {
                Storage::disk('public')->delete($credential->city_shield);
            }
            $tenant = Tenant::find($credential->tenant_id);
            $path = "tenants/{$tenant->getTenantKey()}/shields";
            $data['city_shield'] = ImageUploader::handleImageUpload($data['city_shield'], $path);
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