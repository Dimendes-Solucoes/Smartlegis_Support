<?php

namespace App\Http\Controllers;

use App\Services\CredentialService;
use Inertia\Inertia;

class CredentialController extends Controller
{
    public function __construct(protected CredentialService $service) {}

    public function index()
    {
        return Inertia::render('Credentials/Index', [
            'credentials' => $this->service->getAllCredentials(),
        ]);
    }
    
    public function destroy(int $id)
    {
        $this->service->destroyCredential($id);
        return back()->with('success', 'Credencial movida para a lixeira!');
    }
}