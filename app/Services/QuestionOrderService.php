<?php

namespace App\Services;

use App\Models\Tenancy\QuestionOrder;
use App\Models\Tenancy\QuestionOrderUsers;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class QuestionOrderService
{
    public function getAllQuestionOrders(Request $request): LengthAwarePaginator
    {
        $questionOrders = QuestionOrder::with('quorum.session')
            ->whereHas('quorum.session')
            ->latest('id')
            ->paginate(15);

        return $questionOrders->through(fn (QuestionOrder $qo) => [
            'id' => $qo->id,
            'status' => $qo->status,
            'session_name' => $qo->quorum->session->name ?? 'Sessão não encontrada',
        ]);
    }

    public function getQuestionOrderDetails(QuestionOrder $qo): array
    {
        $qo->load('quorum.session');
        $users = $qo->questionOrderUsers()->with('user')->get();

        return [
            'question_order' => $qo,
            'users' => $users,
        ];
    }

    public function removeUserFromQuestionOrder(QuestionOrderUsers $user): void
    {
        $user->delete();
    }

    public function destroyQuestionOrder(QuestionOrder $qo): void
    {
        $qo->delete();
    }
}