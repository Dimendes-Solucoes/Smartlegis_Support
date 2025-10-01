<?php

namespace App\Services;

use App\Models\Credential;
use Illuminate\Database\Eloquent\Collection;

class CredentialService
{
    public function getAllCredentials(): Collection
    {
        return Credential::with('tenant')
            ->orderBy('city_name')
            ->get();
    }
    
    public function destroyCredential(int $id): void
    {
        $credential = Credential::findOrFail($id);
        $credential->delete();
    }
}