<?php

namespace App\Jobs;

use App\Models\Transcription;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendUpdateTranscriptionToClientJob implements ShouldQueue
{
    use Queueable;

    protected Transcription $transcription;

    /**
     * Create a new job instance.
     */
    public function __construct(Transcription $transcription)
    {
        $this->transcription = $transcription;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $client = new HttpClient();
        $webhook_url = $this->transcription->client->webhook_url;

        try {
            $client->post($webhook_url, [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'id' => $this->transcription->id,
                    'code' => $this->transcription->code,
                    'filename' => $this->transcription->filename,
                    'content' => $this->transcription->content,
                    'status' => $this->transcription->status,
                ],
                'connect_timeout' => 10,
                'timeout' => 10
            ]);

            Log::info('Webhook enviado com sucesso para o cliente.', ['url' => $webhook_url]);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar webhook para o cliente.', ['url' => $webhook_url, 'error' => $e->getMessage()]);
        }
    }
}
