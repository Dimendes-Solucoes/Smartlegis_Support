<?php

namespace App\Libraries\Azure;

use GuzzleHttp\Client as HttpClient;

abstract class BaseAzureClient
{
    protected HttpClient $client;
    protected string $baseUrl;
    protected string $subscriptionKey;

    public function __construct()
    {
        $this->client = new HttpClient();
        $this->baseUrl = rtrim(env('AZURE_SPEECH_ENDPOINT'), '/') . '/speechtotext/v3.2';
        $this->subscriptionKey = env('AZURE_SPEECH_KEY');
    }

    public function extractId(array $data): string
    {
        return basename($data['self']);
    }

    protected function request(string $method, string $uri, array $options = []): ?array
    {
        $defaultOptions = [
            'headers' => $this->getHeaders(isset($options['json']))
        ];

        $response = $this->client->request($method, $uri, array_merge_recursive($defaultOptions, $options));

        if ($response->getStatusCode() === 204) {
            return null;
        }

        return json_decode($response->getBody()->getContents(), true);
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
}
