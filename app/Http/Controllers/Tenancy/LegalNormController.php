<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\LegalNorm\LegalNormUpdateRequest;
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
        $year          = $request->query('year') ? (int) $request->query('year') : null;
        $normTypeId    = $request->query('norm_type_id') ? (int) $request->query('norm_type_id') : null;
        $normSubjectId = $request->query('norm_subject_id') ? (int) $request->query('norm_subject_id') : null;

        return Inertia::render('Tenancy/LegalNorms/Processed', [
            'norms'           => $this->service->list($request->query('search'), $year, $normTypeId, $normSubjectId),
            'normTypes'       => $this->service->normTypesForFilter(),
            'normSubjects'    => $this->service->normSubjectsForFilter(),
            'allNormTypes'    => $this->service->allNormTypes(),
            'allNormSubjects' => $this->service->allNormSubjects(),
            'availableYears'  => $this->service->availableYears(),
            'filters'         => [
                'search'          => $request->query('search', ''),
                'year'            => $year,
                'norm_type_id'    => $normTypeId,
                'norm_subject_id' => $normSubjectId,
            ],
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
        try {
            $extracted = $this->service->reprocess($id);

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
