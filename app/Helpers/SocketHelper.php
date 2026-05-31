<?php

namespace App\Helpers;

use App\Jobs\SocketJob;
use App\Models\Credential;
use App\Models\SocketEvent;
use Illuminate\Support\Facades\DB;

class SocketHelper
{
    public static function getChannel()
    {
        $tenant = current_tenant();

        DB::statement("SET search_path TO public");

        $credential = Credential::where('tenant_id', $tenant->id)->first();

        return $credential->channel ?? null;
    }

    public static function send($socket_id, $payload = [])
    {
        $channel = self::getChannel();

        $tenant = current_tenant();
        $dbName = $tenant->data['tenancy_db_name'] ?? null;
        if (!empty($dbName)) {
            DB::statement("SET search_path TO {$dbName}");
        }

        if ($channel) {
            $event = SocketEvent::create([
                'channel' => $channel,
                'socket_id' => $socket_id,
                'payload' => json_encode($payload),
            ]);

            DB::statement("SET search_path TO public");
            SocketJob::dispatch($event->id);
        }
    }
}
