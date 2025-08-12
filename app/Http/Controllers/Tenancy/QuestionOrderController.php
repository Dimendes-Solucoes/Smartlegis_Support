<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Models\Tenancy\QuestionOrder;
use App\Models\Tenancy\QuestionOrderUsers;
use App\Services\QuestionOrderService;

class QuestionOrderController extends Controller
{
    public function __construct(
        protected QuestionOrderService $service
    ) {}

    public function removeUser(int $id)
    {
        $user = QuestionOrderUsers::findOrFail($id);
        $this->service->removeUserFromQuestionOrder($user);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }

    public function destroy(int $id)
    {
        $questionOrder = QuestionOrder::findOrFail($id);
        $this->service->destroyQuestionOrder($questionOrder);

        return back()->with('success', 'Questão de Ordem excluida com sucesso!');
    }
}
