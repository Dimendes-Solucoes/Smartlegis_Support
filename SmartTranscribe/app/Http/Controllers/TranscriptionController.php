<?php

namespace App\Http\Controllers;

use App\Http\Requests\Transcriptions\TranscriptionConvertRequest;
use App\Http\Requests\Transcriptions\TranscriptionFindExternalRequest;
use App\Services\TranscriptionService;

class TranscriptionController extends Controller
{
    public function __construct(
        protected TranscriptionService $transcriptionService
    ) {}

    public function list()
    {
        $transcriptions = $this->transcriptionService->list();

        return response()->json([
            'transcriptions' => $transcriptions
        ]);
    }

    public function convert(TranscriptionConvertRequest $request)
    {
        $transcription = $this->transcriptionService->convertAndCreate($request->all());

        return response()->json([
            'transcription' => $transcription
        ]);
    }

    public function find(TranscriptionFindExternalRequest $request)
    {
        $transcription = $this->transcriptionService->findByExternalId($request->external_id);

        return response()->json([
            'transcription' => $transcription
        ]);
    }

    public function delete(TranscriptionFindExternalRequest $request)
    {
        $this->transcriptionService->deleteByExternalId($request->external_id);

        return response()->noContent();
    }
}
