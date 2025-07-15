<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Services\TenantService;

class EnsureTenantSelected
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    public function handle(Request $request, Closure $next): Response
    {
        DB::statement("SET search_path TO public");

        $userId = auth()->id();

        if (!$userId) {
            Session::flash('error', 'Você precisa estar logado para acessar esta área.');
            return redirect()->route('login');
        }

        $tenant = $this->tenantService->getCurrentTenant();

        if (!$tenant) {
            Session::flash('error', 'Por favor, selecione um tenant para continuar.');
            return redirect()->route('tenant.settings');
        }

        $dbName = $tenant->data['tenancy_db_name'] ?? null;

        if ($tenant && !empty($dbName)) {
            DB::statement("SET search_path TO {$dbName}");
        } else {
            Session::flash('error', 'O tenant selecionado é inválido ou não possui um banco de dados associado. Por favor, selecione outro.');
            Session::forget(['selected_tenant_id', 'tenant_db_name']);
            return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
