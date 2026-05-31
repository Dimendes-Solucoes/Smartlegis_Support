<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\MaintenanceService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class MaintenanceController extends Controller
{
    public function __construct(
        private readonly MaintenanceService $service,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Tenancy/Maintenance/Index');
    }

    public function refreshAll(): RedirectResponse
    {
        $this->service->refreshAll();
        return back()->with('success', 'Sinal de atualização enviado para todas as páginas.');
    }

    public function refreshTv(): RedirectResponse
    {
        $this->service->refreshTv();
        return back()->with('success', 'Sinal de atualização enviado para a TV.');
    }
}
