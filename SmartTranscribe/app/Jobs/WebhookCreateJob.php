<?php

namespace App\Jobs;

use App\Libraries\Azure\WebhookClient;
use App\Models\Webhook;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class WebhookCreateJob implements ShouldQueue
{
    use Queueable;

    private Webhook $webhook;

    /**
     * Create a new job instance.
     */
    public function __construct(Webhook $webhook)
    {
        $this->webhook = $webhook;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $webhookClient = new WebhookClient();

        $external_id = $this->webhook->webhook_external_id;
        $status = $this->webhook->webhook_status;

        $i = 0;

        while ($status == Webhook::STATUS_NOT_STARTED && $i < 100) {
            $data = $webhookClient->findWebhookById($external_id);
            $status = $data['status'] ?? null;
            $i++;

            sleep(5);
        }

        Webhook::where('webhook_external_id', $external_id)->update([
            'webhook_status' => $status
        ]);

        Log::info('Webhook status: ' . $status . ' after ' . $i . ' attempts');
    }
}
