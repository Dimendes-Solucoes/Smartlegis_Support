<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\LegalNorm\LegalNormUpdateRequest;
use App\Models\Tenancy\LegalNorm;
use App\Models\Tenancy\NormSubject;
use App\Models\Tenancy\NormType;
use App\Services\LegalNormService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class LegalNormController extends Controller
{
    public function __construct(
        protected LegalNormService $service,
    ) {}

    public function index(Request $request)
    {
        return Inertia::render('Tenancy/LegalNorms/Processed', [
            'norms'        => $this->service->list($request->query('search')),
            'normTypes'    => NormType::select('id', 'name', 'abbreviation')->orderBy('name')->get(),
            'normSubjects' => NormSubject::select('id', 'name')->orderBy('name')->get(),
            'filters'      => ['search' => $request->query('search', '')],
        ]);
    }

    public function update(LegalNormUpdateRequest $request, int $id): JsonResponse
    {
        $updated = $this->service->update($id, $request->validated());

        return response()->json([
            'message' => 'Norma atualizada.',
            'data'    => $updated,
        ]);
    }

    public function reprocess(int $id): JsonResponse
    {
        $norm = LegalNorm::findOrFail($id);

        try {
            $extracted = $this->service->reprocess($norm);

            return response()->json([
                'message' => 'Reprocessamento concluído.',
                'data'    => $extracted,
            ]);
        } catch (\Throwable $e) {
            Log::error('LegalNormController::reprocess falhou', [
                'id'    => $id,
                'error' => $e->getMessage(),
            ]);

            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
