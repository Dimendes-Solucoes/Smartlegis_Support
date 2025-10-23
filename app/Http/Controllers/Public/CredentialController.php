<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Credentials\CredentialStoreRequest;
use App\Http\Requests\Credentials\CredentialUpdateRequest;
use App\Services\CredentialService;
use Inertia\Inertia;

class CredentialController extends Controller
{
    public function __construct(
        protected CredentialService $service
    ) {}

    public function index()
    {
        return Inertia::render('Public/Credentials/Index', [
            'credentials' => $this->service->getAllCredentials(),
            'can' => [
                'delete_credential' => auth()->user()->is_root,
            ]
        ]);
    }

    public function create()
    {
        return Inertia::render('Public/Credentials/CreateCredential', [
            'formData' => $this->service->getCreationFormData(),
            'can' => [
                'manage_key' => auth()->user()->is_root,
            ]
        ]);
    }

    public function store(CredentialStoreRequest $request)
    {
        $this->service->createCredential($request->validated());
        return redirect()->route('credentials.index')->with('success', 'Credencial criada com sucesso!');
    }

    public function edit(int $id)
    {
        return Inertia::render('Public/Credentials/EditCredential', [
            'data' => $this->service->getCredentialForEdit($id),
            'can' => [
                'manage_key' => auth()->user()->is_root,
            ]
        ]);
    }

    public function update(CredentialUpdateRequest $request, int $id)
    {
        $this->service->updateCredential($id, $request->validated());
        return redirect()->route('credentials.index')->with('success', 'Credencial atualizada com sucesso!');
    }

    public function destroy(int $id)
    {
        $this->service->destroyCredential($id);
        return back()->with('success', 'Credencial excluída com sucesso!');
    }
}
