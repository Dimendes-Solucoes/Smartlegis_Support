<?php

namespace App\Services;

use App\Models\Tenancy\QuestionOrder;
use App\Models\Tenancy\QuestionOrderUsers;

class QuestionOrderService
{
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
