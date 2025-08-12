<?php

namespace App\Services;

use App\Models\Tenancy\QuestionOrder;
use App\Models\Tenancy\QuestionOrderUsers;

class QuestionOrderService
{
    public function findBySessionId(int $session_id): array
    {
        $question_order = QuestionOrder::whereHas('quorum', fn($q) => $q->where('session_id', $session_id))->first();
        $question_order->load('quorum.session');

        $users = $question_order->questionOrderUsers()->with('user')->get();

        return [
            'question_order' => $question_order,
            'users' => $users,
        ];
    }

    public function removeUserFromQuestionOrder(int $id): void
    {
        $question_order_user = QuestionOrderUsers::findOrFail($id);
        $question_order_user->delete();
    }
}
