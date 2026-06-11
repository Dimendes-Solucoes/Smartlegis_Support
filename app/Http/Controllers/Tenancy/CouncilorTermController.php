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
            'mandate_id' => 'required|exists:mandates,id',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after:start_date',
        ]);

        try {
            $this->service->store($councilorId, $validated);
        } catch (Exception $e) {
            return redirect()->route('councilors.terms.index', $councilorId)
                ->withErrors(['mandate_id' => $e->getMessage()]);
        }

        return redirect()->route('councilors.terms.index', $councilorId)
            ->with('success', 'Mandato adicionado com sucesso!');
    }

    public function update(Request $request, int $councilorId, int $termId)
    {
        $validated = $request->validate([
            'mandate_id' => 'required|exists:mandates,id',
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after:start_date',
        ]);

        try {
            $this->service->update($termId, $validated);
        } catch (Exception $e) {
            return redirect()->route('councilors.terms.index', $councilorId)
                ->withErrors(['mandate_id' => $e->getMessage()]);
        }

        return redirect()->route('councilors.terms.index', $councilorId)
            ->with('success', 'Mandato atualizado com sucesso!');
    }

    public function destroy(int $councilorId, int $termId)
    {
        $this->service->delete($termId);

        return redirect()->route('councilors.terms.index', $councilorId)
            ->with('success', 'Mandato removido com sucesso!');
    }
}
