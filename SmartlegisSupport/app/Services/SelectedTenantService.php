<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\SelectedTenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SelectedTenantService
{
    public function getFormattedTenantsForSelect()
    {
        $tenants = Tenant::with('credentials')->get();

        $formattedTenants = $tenants->map(function ($tenant) {
            return [
                'id' => $tenant->id,
                'name' => $tenant->data['tenancy_db_name'],
                'city' => optional($tenant->credentials->first())->city_name
            ];
        })
            ->sortBy('city')
            ->values();

        return $formattedTenants;
    }

    public function setSelectedTenant(string $tenantId): Tenant
    {
        if (!Auth::check()) {
            throw new \Exception('Usuário não autenticado.', 401);
        }

        $userId = Auth::id();

        $tenant = Tenant::findOrFail($tenantId);

        $lastSelectedTenant = SelectedTenant::where('user_id', $userId)
            ->latest()
            ->first();

        if ($lastSelectedTenant && $lastSelectedTenant->tenant_id === $tenant->id) {
            return $tenant;
        }

        SelectedTenant::create([
            'user_id' => $userId,
            'tenant_id' => $tenant->id
        ]);

        Session::put('selected_tenant_id', $tenant->id);
        Session::put('tenant_db_name', $tenant->data['tenancy_db_name']);

        return $tenant;
    }
}
