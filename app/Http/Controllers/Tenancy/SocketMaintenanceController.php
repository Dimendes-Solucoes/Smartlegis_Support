<?php

namespace App\Http\Controllers\Tenancy;

use App\Helpers\SocketHelper;
use App\Http\Controllers\Controller;
use App\Models\Reference\Sockets;
use Inertia\Inertia;

class SocketMaintenanceController extends Controller
{
    public function index()
    {
        return Inertia::render('Tenancy/Maintenance/Index');
    }

    public function refreshAll()
    {
        SocketHelper::send(Sockets::RefreshAllPages);

        return redirect()->back()->with('success', 'Sinal de atualização enviado para todas as páginas.');
    }

    public function refreshTv()
    {
        SocketHelper::send(Sockets::RefreshTVPage);

        return redirect()->back()->with('success', 'Sinal de atualização enviado para a TV.');
    }
}
