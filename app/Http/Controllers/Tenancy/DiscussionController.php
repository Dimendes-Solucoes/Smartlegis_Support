<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\DiscussionService;

class DiscussionController extends Controller
{
    public function __construct(
        protected DiscussionService $service
    ) {}

    public function removeUser(int $id)
    {
        $this->service->removeUserFromDiscussion($id);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }
}
