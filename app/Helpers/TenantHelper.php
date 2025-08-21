<?php

use App\Models\Tenant;
use App\Services\TenantService;

if (!function_exists('current_tenant_id')) {
    function current_tenant_id(): ?string
    {
        return app(TenantService::class)->getCurrentTenantId();
    }
}

if (!function_exists('current_tenant_db_name')) {
    function current_tenant_db_name(): ?string
    {
        return app(TenantService::class)->getCurrentTenantDbName();
    }
}

if (!function_exists('current_tenant')) {
    function current_tenant(): ?Tenant
    {
        return app(TenantService::class)->getCurrentTenant();
    }
}

if (!function_exists('tenant_city_name')) {
    function tenant_city_name(Tenant $tenant)
    {
        return $tenant->credentials->first()->city_name ?? 'N/A';
    }
}
