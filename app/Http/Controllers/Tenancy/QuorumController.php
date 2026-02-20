<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\QuorumService;

class QuorumController extends Controller
{
    public function __construct(
        protected QuorumService $service
    ) {}

    public function removeUser(int $id)
    {
        $this->service->removeUserFromQuorum($id);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }
}
