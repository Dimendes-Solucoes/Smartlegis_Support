<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transcriptions\TranscriptionConvertRequest;
use App\Http\Requests\Transcriptions\TranscriptionFindExternalRequest;
use App\Http\Requests\Transcriptions\TranscriptionGeneratePresignedUrlRequest;
use App\Services\TranscriptionService;

class TranscriptionController extends Controller
{
    public function __construct(
        protected TranscriptionService $service
    ) {}

    /** @group ClientTranscriptions */
    public function list()
    {
        $transcriptions = $this->service->list();

        return response()->json([
            'transcriptions' => $transcriptions
        ]);
    }

    /** @group ClientTranscriptions */
    public function convert(TranscriptionConvertRequest $request)
    {
        $transcription = $this->service->convertAndCreate($request->all());

        return response()->json([
            'transcription' => $transcription
        ]);
    }

    /** @group ClientTranscriptions */
    public function generatePresignedUrl(TranscriptionGeneratePresignedUrlRequest $request)
    {
        $data = $this->service->generatePresignedUrl($request->validated());

        return response()->json($data);
    }

    /** @group ClientTranscriptions */
    public function find(TranscriptionFindExternalRequest $request)
    {
        $transcription = $this->service->findByCode($request->code);

        return response()->json([
            'transcription' => $transcription
        ]);
    }

    /** @group ClientTranscriptions */
    public function delete(TranscriptionFindExternalRequest $request)
    {
        $this->service->deleteByCode($request->code);

        return response()->noContent();
    }
}
