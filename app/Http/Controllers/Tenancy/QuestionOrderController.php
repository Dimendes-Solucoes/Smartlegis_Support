<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Models\Tenancy\QuestionOrder;
use App\Models\Tenancy\QuestionOrderUsers;
use App\Services\QuestionOrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuestionOrderController extends Controller
{
    public function __construct(protected QuestionOrderService $service) {}

    public function index(Request $request)
    {
        return Inertia::render('Tenancy/QuestionOrders/Index', [
            'questionOrders' => $this->service->getAllQuestionOrders($request),
            'filters' => $request->only(['sort', 'direction', 'search']),
        ]);
    }

    public function edit(int $id)
    {
        $questionOrder = QuestionOrder::findOrFail($id);
        
        return Inertia::render('Tenancy/QuestionOrders/EditMember', [
            'questionOrderData' => $this->service->getQuestionOrderDetails($questionOrder),
        ]);
    }

    public function removeUser(int $id)
    {
        $user = QuestionOrderUsers::findOrFail($id);
        $this->service->removeUserFromQuestionOrder($user);

        return back()->with('success', 'Inscrição removida com sucesso!');
    }
}