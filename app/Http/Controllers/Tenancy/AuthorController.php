<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\AuthorService;

class AuthorController extends Controller
{
    public function __construct(
        protected AuthorService $service
    ) {}

    public function destroy(int $id)
    {
        $this->service->destroy($id);
        return back()->with('success', 'Autor do documentos deletado com sucesso');
    }
}
