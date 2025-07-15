<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\Timers\TimerUpdateRequest;
use App\Services\TimerService;
use Inertia\Inertia;

class TimerController extends Controller
{
    public function __construct(
        protected TimerService $service
    ) {}

    public function index()
    {
        $timers = $this->service->list();

        return Inertia::render('Tenancy/Timers/Index', [
            'timers' => $timers,
        ]);
    }

    public function edit(int $id)
    {
        $timer = $this->service->find($id);

        return Inertia::render('Tenancy/Timers/EditTimer', [
            'timer' => $timer,
        ]);
    }

    public function update(TimerUpdateRequest $request, int $id)
    {
        $this->service->update($id, $request->validated());

        return redirect()->route('timers.index')->with('success', 'Usuário atualizado com sucesso!');
    }
}