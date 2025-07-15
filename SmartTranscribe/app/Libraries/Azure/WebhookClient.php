<?php

namespace App\Libraries\Azure;

use App\Models\Webhook;
use Illuminate\Support\Str;

class WebhookClient extends BaseAzureClient
{
    public function getWebhooks(): array
    {
        $data = $this->request('GET', "{$this->baseUrl}/webhooks");
        return $data['values'] ?? [];
    }

    public function createWebhook(string $webhookUrl): array
    {
        $payload = [
            'displayName' => 'TranscriptionCompletionWebHook-' . Str::random(10),
            'webUrl' => $webhookUrl,
            'events' => [
                'transcriptionCompletion' => true
            ],
            'properties' => [
                'secret' => $this->subscriptionKey
            ],
            'description' => 'Webhook para notificações de transcrição.'
        ];

        $data = $this->request('POST', "{$this->baseUrl}/webhooks", ['json' => $payload]);
        return $this->formatDataWebhook($data);
    }

    public function findWebhookById(string $webhook_external_id): array
    {
        $data = $this->request('GET', "{$this->baseUrl}/webhooks/{$webhook_external_id}");
        return $this->formatDataWebhook($data);
    }

    public function deleteWebhook(string $webhook_external_id): void
    {
        $this->request('DELETE', "{$this->baseUrl}/webhooks/{$webhook_external_id}");
    }

    private function formatDataWebhook(array $data): array
    {
        return [
            'external_id' => $this->extractId($data),
            'status' => $this->mapWebhookStatus($data['status'])
        ];
    }

    private function mapWebhookStatus(string $status): ?string
    {
        return match ($status) {
            'NotStarted' => Webhook::STATUS_NOT_STARTED,
            'Succeeded' => Webhook::STATUS_SUCCEEDED,
            'Failed' => Webhook::STATUS_FAILED,
            default => null
        };
    }
}
