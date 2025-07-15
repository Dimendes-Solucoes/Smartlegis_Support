<?php

namespace App\Libraries;

use App\Models\OpenAiRequest;
use Exception;
use Illuminate\Support\Facades\Http;

class OpenAiLibrary
{
    protected string $baseUrl;
    protected string $subscriptionKey;
    protected string $model;

    public function __construct()
    {
        $this->baseUrl = env('OPENAI_API_URL');
        $this->subscriptionKey = env('OPENAI_API_KEY');
        $this->model = env('OPENAI_API_MODEL');
    }

    public function makeRequest(array $input): string
    {
        $response = Http::withHeaders(['Authorization' => "Bearer {$this->subscriptionKey}"])
            ->post($this->baseUrl, $input);

        if ($response->failed()) {
            throw new Exception('Erro ao executar a requisição');
        }

        $data = $this->formatData($response->json());

        $this->logRequest($data);

        return $data['content'];
    }

    private function logRequest(array $data): void
    {
        OpenAiRequest::create([
            'client_id' => currentClient()->id,
            'input' => "",
            'model' => $this->model,
            'content' => $data['content'],
            'tokens' => $data['tokens']
        ]);
    }

    private function formatData(array $data): array
    {
        return [
            'content' => $data['choices'][0]['message']['content'] ?? '',
            'tokens' => [
                'prompt' => $data['usage']['prompt_tokens'] ?? 0,
                'completion' => $data['usage']['completion_tokens'] ?? 0,
                'total' => $data['usage']['total_tokens'] ?? 0,
            ],
        ];
    }
}
