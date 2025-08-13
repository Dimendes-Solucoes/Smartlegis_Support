<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\TribuneService;

class TribuneController extends Controller
{
    public function __construct(
        protected TribuneService $service
    ) {}

    public function removeUser(int $id)
    {
        $this->service->removeUserFromTribune($id);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }
}
