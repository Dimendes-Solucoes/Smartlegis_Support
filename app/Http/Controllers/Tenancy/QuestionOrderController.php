<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\QuestionOrderService;

class QuestionOrderController extends Controller
{
    public function __construct(
        protected QuestionOrderService $service
    ) {}

    public function removeUser(int $id)
    {
        $this->service->removeUserFromQuestionOrder($id);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }
}
