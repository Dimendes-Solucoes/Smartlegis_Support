<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\LegalNormConversionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LegalNormConversionController extends Controller
{
    public function __construct(
        protected LegalNormConversionService $service,
    ) {}

    public function availableSessions(Request $request): JsonResponse
    {
        return response()->json($this->service->availableSessions($request));
    }

    public function availableCommissions(): JsonResponse
    {
        return response()->json($this->service->availableCommissions());
    }

    public function convertToSessionDocument(Request $request, int $id): JsonResponse
    {
        $request->validate(['session_id' => 'required|integer']);

        try {
            $this->service->convertToSessionDocument($id, $request->integer('session_id'));
            return response()->json(['message' => 'Norma convertida e enviada para a sessão com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function convertToCommissionDocument(Request $request, int $id): JsonResponse
    {
        $request->validate(['commission_id' => 'required|integer']);

        try {
            $this->service->convertToCommissionDocument($id, $request->integer('commission_id'));
            return response()->json(['message' => 'Norma convertida e enviada para a comissão com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->destroy($id);
            return response()->json(['message' => 'Norma excluída com sucesso!']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
