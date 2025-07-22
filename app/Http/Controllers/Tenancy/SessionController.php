<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\SessionService; 
use Inertia\Inertia;

class SessionController extends Controller
{
    public function __construct(
        protected SessionService $service
    ) {}

    public function index()
    {
        $sessions = $this->service->getAllSessions();
        return Inertia::render('Tenancy/Sessions/Index', [
            'sessions' => $sessions,
        ]);
    }
}