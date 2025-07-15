<?php

namespace App\Services;

use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ClientService
{
    public function __construct(
        protected WebhookService $webhookService,
    ) {}

    public function list()
    {
        return Client::all();
    }

    public function register(array $data)
    {
        try {
            DB::beginTransaction();

            $client = Client::create([
                'code' => Str::uuid()->toString(),
                'website_name' => $data['website_name'],
                'webhook_url' => $data['webhook_url'],
                'token' => Hash::make($data['website_name'] . $data['webhook_url'] . Str::uuid()->toString()),
            ]);

            $this->webhookService->createForClient($client->id);

            DB::commit();

            return $client;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao registrar cliente', ['error' => $e->getMessage()]);
            throw new \Exception('Erro ao registrar cliente');
        }
    }

    public function profile()
    {
        return currentClient();
    }

    public function deleteById($id)
    {
        $client = Client::findOrFail($id);

        try {
            DB::beginTransaction();

            foreach ($client->webhooks as $webhook) {
                $this->webhookService->deleteByExternal($webhook->webhook_external_id);
            }

            $client->delete();

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erro ao deletar cliente', ['error' => $e->getMessage()]);
            throw new \Exception('Erro ao deletar cliente');
        }
    }
}
