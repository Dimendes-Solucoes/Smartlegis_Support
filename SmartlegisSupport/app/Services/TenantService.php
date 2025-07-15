<?php

namespace App\Services;

use App\Models\SelectedTenant;
use App\Models\Tenant;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class TenantService
{
    public function getCurrentTenant(): ?Tenant
    {
        static $tenantInstance = null;
        if ($tenantInstance) {
            return $tenantInstance;
        }

        $tenantId = Session::get('selected_tenant_id');

        if ($tenantId) {
            $tenantInstance = Tenant::find($tenantId);
            if (!$tenantInstance) {
                Session::forget(['selected_tenant_id', 'tenant_db_name']);
                $tenantInstance = $this->getLastSelectedTenantForUser();
            }
        } else {
            $tenantInstance = $this->getLastSelectedTenantForUser();
        }

        if ($tenantInstance) {
            $this->setTenantInSession($tenantInstance);
        }

        return $tenantInstance;
    }

    protected function getLastSelectedTenantForUser(): ?Tenant
    {
        $userId = Auth::id();

        if (!$userId) {
            return null;
        }

        $selectedTenantRecord = SelectedTenant::where('user_id', $userId)
            ->latest()
            ->first();

        return $selectedTenantRecord ? $selectedTenantRecord->tenant : null;
    }

    public function setTenantInSession(Tenant $tenant): void
    {
        $dbName = $tenant->data['tenancy_db_name'] ?? null;

        if ($tenant && !empty($dbName)) {
            Session::put('selected_tenant_id', $tenant->id);
            Session::put('tenant_db_name', $dbName);
        } else {
            Session::forget(['selected_tenant_id', 'tenant_db_name']);
        }
    }

    public function getCurrentTenantId(): ?string
    {
        return Session::get('selected_tenant_id');
    }

    public function getCurrentTenantDbName(): ?string
    {
        return Session::get('tenant_db_name');
    }
}
