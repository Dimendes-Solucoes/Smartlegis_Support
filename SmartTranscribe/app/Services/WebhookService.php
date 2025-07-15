<?php

namespace App\Services;

use App\Libraries\Azure\WebhookClient;
use App\Models\Transcription;
use App\Models\Webhook;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookService
{
    public function __construct(
        protected TranscriptionService $transcriptionService,
        protected WebhookClient $webhookClient
    ) {}

    public function list()
    {
        try {
            return $this->webhookClient->getWebhooks();
        } catch (\Throwable $e) {
            Log::error('Erro ao listar webhooks: ' . $e->getMessage());
            throw new \Exception('Erro ao listar webhooks');
        }
    }

    public function createForClient($client_id)
    {
        try {
            DB::beginTransaction();

            $webhook_path = url('api/client/webhooks/transcriptions');
            $webhook_url = str_replace('http://', 'https://', $webhook_path);

            if ($this->hasWebhook($webhook_url)) {
                DB::rollBack();
                return;
            }

            $createdWebhook = $this->createWebhook($webhook_url);

            $this->inativeOthersWebhooks();

            $webhook = Webhook::create([
                'client_id' => $client_id,
                'code' => Str::uuid()->toString(),
                'webhook_external_id' => $createdWebhook['external_id'],
                'webhook_url' => $webhook_url,
                'webhook_status' => Webhook::STATUS_NOT_STARTED
            ]);

            DB::commit();

            return $webhook;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Erro: ' . $e->getMessage());
            throw new \Exception('Erro: ' . $e->getMessage());
        }
    }

    public function deleteByExternal(string $external_id)
    {
        try {
            $this->webhookClient->deleteWebhook($external_id);
            Webhook::where('webhook_external_id', $external_id)->delete();
        } catch (\Throwable $e) {
            Log::error('Erro ao deletar o webhook: ' . $e->getMessage());
            throw new \Exception('Erro ao deletar o webhook');
        }
    }

    public function handleTranscription(array $data)
    {
        try {
            Log::info("Webhook received: " . json_encode($data));

            if (!$this->isTranscription()) {
                Log::info("Webhook is not a transcription");
                return;
            }

            $external_id = $this->webhookClient->extractId($data);

            $transcription = Transcription::where('external_id', $external_id)->first();

            $this->transcriptionService->findByCode($transcription->code);
        } catch (\Throwable $e) {
            Log::error('Erro ao buscar o webhook: ' . $e->getMessage());
            throw new \Exception('Erro ao buscar o webhook');
        }
    }

    private function hasWebhook(string $webhook_url): bool
    {
        return Webhook::where('webhook_url', $webhook_url)
            ->where('webhook_status', Webhook::STATUS_SUCCEEDED)
            ->exists();
    }

    private function createWebhook(string $webhook_url): array
    {
        try {
            return $this->webhookClient->createWebhook($webhook_url);
        } catch (\Throwable $e) {
            Log::error('Não foi possível registrar webhook: ' . $e->getMessage());
            throw new \Exception('Não foi possível registrar webhook');
        }
    }

    private function inativeOthersWebhooks()
    {
        Webhook::where('webhook_status', '!=', Webhook::STATUS_INATIVE)
            ->update(['webhook_status' => Webhook::STATUS_INATIVE]);
    }

    private function isTranscription()
    {
        return request()->header('x-microsoftspeechservices-event') === 'TranscriptionCompletion';
    }
}
