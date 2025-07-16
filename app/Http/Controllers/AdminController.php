<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreRequest;
use App\Http\Requests\Admin\AdminUpdateRequest;
use App\Services\AdminService;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function __construct(
        protected AdminService $service
    ) {}

    public function index()
    {
        $admins = $this->service->list();

        return Inertia::render('Admin/Index', [
            'admins' => $admins,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/CreateAdmin');
    }

    public function store(AdminStoreRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.index')->with('success', 'Administrador criado com sucesso!');
    }

    public function edit(int $id)
    {
        $admin = $this->service->find($id);

        return Inertia::render('Admin/UpdateAdmin', [
            'admin' => $admin,
        ]);
    }

    public function update(AdminUpdateRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->route('admin.index')->with('success', 'Administrador criado com sucesso!');
    }

    public function destroy(int $id)
    {
        $this->service->delete($id);

        return redirect()->route('admin.index')->with('success', 'Administrador deletado com sucesso!');
    }
}
