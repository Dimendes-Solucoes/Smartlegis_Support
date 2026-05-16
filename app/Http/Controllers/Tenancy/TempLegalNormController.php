<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Http\Requests\LegalNorm\TempLegalNormBatchRequest;
use App\Http\Requests\LegalNorm\TempLegalNormUpdateRequest;
use App\Http\Requests\LegalNorm\TempLegalNormUploadRequest;
use App\Models\Tenancy\NormSubject;
use App\Models\Tenancy\NormType;
use App\Services\TempLegalNormService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;

class TempLegalNormController extends Controller
{
    public function __construct(
        protected TempLegalNormService $service,
    ) {}

    public function index()
    {
        return Inertia::render('Tenancy/LegalNorms/Index', [
            'norms'        => $this->service->list(),
            'normTypes'    => NormType::select('id', 'name', 'abbreviation')->orderBy('name')->get(),
            'normSubjects' => NormSubject::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    public function upload(TempLegalNormUploadRequest $request): JsonResponse
    {
        $norm = $this->service->uploadAndExtract($request->file('attachment'));

        return response()->json([
            'message' => 'Arquivo processado com sucesso.',
            'data'    => $norm->load(['normType', 'normSubject']),
        ], 201);
    }

    public function update(TempLegalNormUpdateRequest $request, int $id): JsonResponse
    {
        $updated = $this->service->update($id, $request->validated());

        return response()->json([
            'message' => 'Norma atualizada.',
            'data'    => $updated,
        ]);
    }

    public function confirm(int $id): JsonResponse
    {
        $norm = $this->service->confirm($id);

        return response()->json([
            'message' => 'Norma confirmada com sucesso.',
            'data'    => $norm,
        ]);
    }

    public function confirmBatch(TempLegalNormBatchRequest $request): JsonResponse
    {
        $result = $this->service->confirmBatch(
            $request->validated('ids', [])
        );

        return response()->json([
            'message'   => 'Lote processado.',
            'confirmed' => count($result['confirmed']),
            'errors'    => $result['errors'],
        ]);
    }

    public function discard(int $id): JsonResponse
    {
        $this->service->discard($id);

        return response()->json(['message' => 'Norma descartada.']);
    }

    public function discardBatch(TempLegalNormBatchRequest $request): JsonResponse
    {
        $total = $this->service->discardBatch(
            $request->validated('ids', [])
        );

        return response()->json([
            'message' => "{$total} norma(s) descartada(s).",
            'total'   => $total,
        ]);
    }
}
