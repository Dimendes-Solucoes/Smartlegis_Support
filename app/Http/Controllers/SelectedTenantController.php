<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tenants\TenantChangeRequest;
use App\Services\SelectedTenantService;
use Inertia\Inertia;

class SelectedTenantController extends Controller
{
    public function __construct(
        protected SelectedTenantService $service
    ) {}

    public function settings()
    {
        $tenants = $this->service->getFormattedTenantsForSelect();

        return Inertia::render('Settings/Index', [
            'tenants' => $tenants
        ]);
    }

    public function change(TenantChangeRequest $request)
    {
        $tenant = $this->service->setSelectedTenant($request->input('tenant_id'));
        $city = tenant_city_name($tenant);

        return redirect()->back()
            ->with('success', "A cidade de $city foi selecionada");
    }
}
