<?php

namespace App\Services;

use App\Libraries\AzureLibrary;
use App\Models\Transcription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TranscriptionService
{
    public function __construct(
        protected AzureLibrary $azureLibrary
    ) {}

    public function list()
    {
        try {
            return $this->azureLibrary->getTranscriptions();
        } catch (\Throwable $e) {
            Log::error('Erro ao listar transcrições: ' . $e->getMessage());
            throw new \Exception('Erro ao listar transcrições');
        }
    }

    public function convertAndCreate(array $data)
    {
        try {
            $data = $this->azureLibrary->generateTranscription($data['media_url']);
            return $this->saveTranscription($data);
        } catch (\Throwable $e) {
            Log::error('Erro ao criar a transcrição: ' . $e->getMessage());
            throw new \Exception('Erro ao criar a transcrição');
        }
    }

    public function findByExternalId(string $external_id)
    {
        try {
            $transcription = Transcription::where('external_id', $external_id)->first();

            if ($transcription && $transcription->content) {
                return $transcription;
            }

            $data = $this->azureLibrary->findTranscription($external_id);
            return $this->saveTranscription($data);
        } catch (\Throwable $e) {
            Log::error('Erro ao encontrar a transcrição: ' . $e->getMessage());
            throw new \Exception('Erro ao encontrar a transcrição');
        }
    }

    public function deleteByExternalId(string $external_id)
    {
        try {
            DB::beginTransaction();

            $transcription = Transcription::where('external_id', $external_id)->first();

            if ($transcription) {
                $transcription->delete();
            }

            $this->azureLibrary->deleteTranscription($external_id);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro ao excluir a transcrição: ' . $e->getMessage());
            throw new \Exception('Erro ao excluir a transcrição');
        }
    }

    private function saveTranscription(array $data)
    {
        return Transcription::updateOrCreate(
            ['external_id' => $data['external_id']],
            [
                'filename' => $data['filename'],
                'content' => $data['content'],
                'status' => $data['status']
            ]
        );
    }
}
