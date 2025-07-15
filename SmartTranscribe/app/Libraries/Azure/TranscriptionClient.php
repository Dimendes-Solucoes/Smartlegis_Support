<?php

namespace App\Libraries\Azure;

use App\Models\Transcription;

class TranscriptionClient extends BaseAzureClient
{
    public function getTranscriptions(): array
    {
        $data = $this->request('GET', "{$this->baseUrl}/transcriptions");
        return $this->formatArrayData($data);
    }

    public function findTranscription(string $id): array
    {
        $data = $this->request('GET', "{$this->baseUrl}/transcriptions/{$id}");
        return $this->formatData($data);
    }

    public function generateTranscription(string $audioUrl): array
    {
        $payload = [
            'contentUrls' => [$audioUrl],
            'locale' => 'pt-BR',
            'displayName' => 'Batch Transcription ' . now(),
            "properties" => [
                "diarizationEnabled" => false,
                "wordLevelTimestampsEnabled" => false,
                "punctuationMode" => "DictatedAndAutomatic",
                "profanityFilterMode" => "Masked"
            ]
        ];

        $data = $this->request('POST', "{$this->baseUrl}/transcriptions", ['json' => $payload]);
        return $this->formatData($data);
    }

    public function deleteTranscription(string $external_id): void
    {
        $this->request('DELETE', "{$this->baseUrl}/transcriptions/{$external_id}");
    }

    private function formatData(array $data): array
    {
        return [
            'external_id' => $this->extractId($data),
            'status' => $this->mapStatus($data['status']),
            'content' => $this->getTranscriptionText($data),
            'filename' => $data['displayName'] ?? ''
        ];
    }

    private function formatArrayData(array $data): array
    {
        if (empty($data['values'])) {
            return [];
        }

        return array_map([$this, 'formatData'], $data['values']);
    }

    private function mapStatus(string $status): string
    {
        return match ($status) {
            'NotStarted' => Transcription::STATUS_NOT_STARTED,
            'Running' => Transcription::STATUS_RUNNING,
            'Succeeded' => Transcription::STATUS_SUCCEEDED,
            'Failed' => Transcription::STATUS_FAILED,
            default => Transcription::STATUS_UNDEFINED
        };
    }

    private function getTranscriptionText(array $transcriptionData): string
    {
        if (($transcriptionData['status'] ?? '') !== 'Succeeded') {
            return '';
        }

        $filesUrl = $transcriptionData['links']['files'] ?? null;
        if (!$filesUrl) {
            return '';
        }

        $filesData = $this->request('GET', $filesUrl);
        $transcriptionFile = $this->findTranscriptionFile($filesData['values'] ?? []);

        if (!$transcriptionFile || empty($transcriptionFile['links']['contentUrl'])) {
            return '';
        }

        $content = $this->request('GET', $transcriptionFile['links']['contentUrl']);

        return $this->extractTextFromPhrases($content);
    }

    private function findTranscriptionFile(array $files): ?array
    {
        foreach ($files as $file) {
            if (isset($file['kind']) && $file['kind'] === 'Transcription') {
                return $file;
            }
        }
        return null;
    }

    private function extractTextFromPhrases(array $content, int $channelId = 0): string
    {
        $phrases = $content['recognizedPhrases'] ?? [];

        if (empty($phrases)) {
            return '';
        }

        $text = collect($phrases)
            ->filter(fn($phrase) => isset($phrase['channel']) && $phrase['channel'] === $channelId)
            ->sortBy(fn($phrase) => $this->parseIsoDurationToSeconds($phrase['offset'] ?? ''))
            ->map(fn($phrase) => $phrase['nBest'][0]['display'] ?? '')
            ->implode(' ');

        return trim($text);
    }

    private function parseIsoDurationToSeconds(string $duration): float
    {
        $regex = '/^PT(?:(\d+)M)?(?:([\d.]+)S)?$/';

        if (preg_match($regex, $duration, $matches)) {
            $minutes = isset($matches[1]) ? (float)$matches[1] : 0;
            $seconds = isset($matches[2]) ? (float)$matches[2] : 0;

            return ($minutes * 60) + $seconds;
        }

        return 0.0;
    }
}
