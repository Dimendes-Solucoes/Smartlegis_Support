<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\DocumentConversionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentConversionController extends Controller
{
    public function __construct(
        protected DocumentConversionService $service
    ) {}

    public function convert(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:doc,docx,pdf|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $extension = strtolower($file->getClientOriginalExtension());

            $html = match ($extension) {
                'docx', 'doc' => $this->service->convertWordToHtml($file),
                'pdf' => $this->service->convertPdfToHtml($file),
                default => throw new \Exception('Formato de arquivo não suportado'),
            };

            return response()->json([
                'success' => true,
                'html' => $html,
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao converter documento: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erro ao converter o documento: ' . $e->getMessage(),
            ], 422);
        }
    }
}
