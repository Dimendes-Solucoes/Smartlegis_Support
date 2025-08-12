<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Models\Tenancy\QuorumUsers;
use App\Services\QuorumService;

class QuorumController extends Controller
{
    public function __construct(
        protected QuorumService $service
    ) {}

    public function removeUser(int $id)
    {
        $quorumUser = QuorumUsers::findOrFail($id);
        $this->service->removeUserFromQuorum($quorumUser);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }
}
