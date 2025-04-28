<?php

namespace App\Libraries;

use App\Models\Transcription;
use GuzzleHttp\Client;

class AzureLibrary
{
    protected Client $client;
    protected string $baseUrl;
    protected string $subscriptionKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->baseUrl = rtrim(env('AZURE_SPEECH_ENDPOINT'), '/') . '/speechtotext/v3.1';
        $this->subscriptionKey = env('AZURE_SPEECH_KEY');
    }

    public function getTranscriptions()
    {
        $response = $this->client->get("{$this->baseUrl}/transcriptions", [
            'headers' => $this->getHeaders(),
        ]);

        $data = json_decode($response->getBody(), true);

        return $this->formatArrayData($data);
    }

    public function findTranscription(string $id): array
    {
        $response = $this->client->get("{$this->baseUrl}/transcriptions/{$id}", [
            'headers' => $this->getHeaders(),
        ]);

        $data = json_decode($response->getBody(), true);

        return $this->formatData($data);
    }

    public function generateTranscription(string $url): array
    {
        $response = $this->client->post("{$this->baseUrl}/transcriptions", [
            'headers' => $this->getHeaders(true),
            'json' => [
                'contentUrls' => [$url],
                'locale' => 'pt-BR',
                'displayName' => 'Batch Transcription ' . now(),
            ]
        ]);

        $data = json_decode($response->getBody(), true);

        return $this->formatData($data);
    }

    public function deleteTranscription(string $external_id)
    {
        $this->client->delete("{$this->baseUrl}/transcriptions/{$external_id}", [
            'headers' => $this->getHeaders(true),
        ]);
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
        foreach ($data['values'] as $key => $value) {
            $data['values'][$key] = $this->formatData($value);
        }

        return $data['values'];
    }

    private function getHeaders(bool $isJson = false): array
    {
        $headers = [
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
        ];

        if ($isJson) {
            $headers['Content-Type'] = 'application/json';
        }

        return $headers;
    }

    private function extractId(array $data): string
    {
        return basename($data['self']);
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

    private function getTranscriptionText(array $transcription): string
    {
        if (($transcription['status'] ?? '') !== 'Succeeded') {
            return '';
        }

        $filesUrl = $transcription['links']['files'] ?? null;
        if (!$filesUrl) {
            return '';
        }

        $files = $this->fetchJson($filesUrl);
        $transcriptionFile = $this->findTranscriptionFile($files['values'] ?? []);

        if (!$transcriptionFile || empty($transcriptionFile['links']['contentUrl'])) {
            return '';
        }

        $content = $this->fetchJson($transcriptionFile['links']['contentUrl']);
        return $this->extractTextFromPhrases($content);
    }

    private function fetchJson(string $url): array
    {
        $response = $this->client->get($url, [
            'headers' => $this->getHeaders(),
        ]);

        return json_decode($response->getBody(), true);
    }

    private function findTranscriptionFile(array $files): ?array
    {
        foreach ($files as $file) {
            if (isset($file['kind']) && str_contains($file['kind'], 'Transcription')) {
                return $file;
            }
        }

        return null;
    }

    private function extractTextFromPhrases(array $content): string
    {
        $text = '';

        foreach ($content['recognizedPhrases'] ?? [] as $phrase) {
            $text .= ($phrase['nBest'][0]['display'] ?? '') . "\n\n";
        }

        return trim($text);
    }
}
