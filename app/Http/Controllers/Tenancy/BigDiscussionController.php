<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\BigDiscussionService;

class BigDiscussionController extends Controller
{
    public function __construct(
        protected BigDiscussionService $service
    ) {}

    public function removeUser(int $id)
    {
        $this->service->removeUserFromDiscussion($id);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }
}
