<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\CouncilorTermService;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CouncilorTermController extends Controller
{
    public function __construct(
        protected CouncilorTermService $service
    ) {}

    public function index(int $councilorId)
    {
        $data = $this->service->getIndexData($councilorId);

        return Inertia::render('Tenancy/Councilors/Terms/Index', ['data' => $data]);
    }

    public function store(Request $request, int $councilorId)
    {
        $validated = $request->validate([
            'legislature_id' => 'required|exists:legislatures,id',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after:start_date',
        ]);

        try {
            $this->service->store($councilorId, $validated);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['legislature_id' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Mandato adicionado com sucesso!');
    }

    public function update(Request $request, int $termId)
    {
        $validated = $request->validate([
            'legislature_id' => 'required|exists:legislatures,id',
            'start_date'     => 'required|date',
            'end_date'       => 'nullable|date|after:start_date',
        ]);

        try {
            $this->service->update($termId, $validated);
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['legislature_id' => $e->getMessage()]);
        }

        return redirect()->back()->with('success', 'Mandato atualizado com sucesso!');
    }

    public function destroy(int $termId)
    {
        $this->service->delete($termId);

        return redirect()->back()->with('success', 'Mandato removido com sucesso!');
    }
}
