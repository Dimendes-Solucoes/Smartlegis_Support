<?php

namespace App\Services;

use App\Jobs\ProcessAndTranscribeAudio;
use App\Libraries\AudioProcessorLibrary;
use App\Libraries\Azure\TranscriptionClient;
use App\Libraries\StorageLibrary;
use App\Models\Transcription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class TranscriptionService
{
    public function __construct(
        protected TranscriptionClient $transcriptionClient,
        protected AudioProcessorLibrary $audioProcessorLibrary,
        protected StorageLibrary $storageLibrary
    ) {}

    public function list()
    {
        try {
            return Transcription::where('client_id', currentClient()->id)->get();
        } catch (\Throwable $e) {
            Log::error('Erro ao listar transcrições: ' . $e->getMessage());
            throw new \Exception('Erro ao listar transcrições');
        }
    }

    public function convertAndCreate(array $data)
    {
        $transcription = Transcription::create([
            'client_id' => currentClient()->id,
            'code' => Str::uuid()->toString(),
        ]);

        ProcessAndTranscribeAudio::dispatch($data, $transcription);

        return $transcription;
    }

    public function generatePresignedUrl(array $data)
    {
        $extension = strtolower($data['filetype']);

        $mime_types = [
            // Áudio
            'mp3'  => 'audio/mpeg',
            'wav'  => 'audio/wav',
            'ogg'  => 'audio/ogg',
            'flac' => 'audio/flac',
            'aac'  => 'audio/aac',
            'm4a'  => 'audio/mp4',
            'opus' => 'audio/opus',
            'amr'  => 'audio/amr',
            'aiff' => 'audio/aiff',
            'mid'  => 'audio/midi',
            'midi' => 'audio/midi',
            'weba' => 'audio/webm',

            // Vídeo
            'mp4'  => 'video/mp4',
            'mkv'  => 'video/x-matroska',
            'webm' => 'video/webm',
            'avi'  => 'video/x-msvideo',
            'mov'  => 'video/quicktime',
            '3gp'  => 'video/3gpp',
            'wmv'  => 'video/x-ms-wmv',
            'ts'   => 'video/MP2T',
        ];

        $mime_type = $mime_types[$extension] ?? 'application/octet-stream';

        $filename = 'transcriptions/' . Str::uuid() . '-' . $data['filename'] . '.' . $extension;

        $expires_at = Carbon::now()->addHour();

        $temporary_upload = Storage::disk('s3')->temporaryUploadUrl(
            $filename,
            $expires_at,
            [
                'ContentType' => $mime_type,
                'ACL' => 'public-read'
            ]
        );

        $public_url = Storage::disk('s3')->url($filename);

        return [
            'public_url' => $public_url,
            'upload_url' => $temporary_upload['url'],
        ];
    }

    public function findByCode(string $code)
    {
        $transcription = Transcription::where('code', $code)->first();

        if (!$transcription) {
            throw new NotFoundResourceException('Transcrição não encontrada');
        }

        if (!$transcription->external_id) {
            throw new NotFoundResourceException('Transcrição ainda não processada');
        }

        if ($transcription && $transcription->content) {
            return $transcription;
        }

        try {
            $data = $this->transcriptionClient->findTranscription($transcription->external_id);
            return $this->saveTranscription($data);
        } catch (\Throwable $e) {
            Log::error('Erro ao encontrar a transcrição: ' . $e->getMessage());
            throw new \Exception('Erro ao encontrar a transcrição');
        }
    }

    public function deleteByCode(string $code)
    {
        $transcription = Transcription::where('code', $code)->first();

        if (!$transcription) {
            throw new NotFoundResourceException('Transcrição não encontrada');
        }

        if ($transcription) {
            $transcription->delete();
        }

        try {
            $this->transcriptionClient->deleteTranscription($transcription->external_id);
        } catch (\Throwable $e) {
            Log::error('Erro ao excluir a transcrição: ' . $e->getMessage());
            throw new \Exception('Erro ao excluir a transcrição');
        }
    }

    private function saveTranscription(array $data)
    {
        $transcription = Transcription::where('external_id', $data['external_id'])->first();

        if ($transcription) {
            $transcription->update([
                'filename' => $data['filename'],
                'content' => $data['content'],
                'status' => $data['status']
            ]);

            return $transcription;
        }

        return Transcription::create([
            'client_id' => currentClient()->id,
            'code' => Str::uuid()->toString(),
            'external_id' => $data['external_id'],
            'filename' => $data['filename'],
            'content' => $data['content'],
            'status' => $data['status']
        ]);
    }
}
