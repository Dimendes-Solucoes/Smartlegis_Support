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
        $tenant_id = current_tenant_id();

        DB::statement("SET search_path TO public");
        $credential = Credential::where('tenant_id', $tenant_id)->first();

        return $credential->channel ?? null;
    }

    public static function send($socket_id, $payload = [])
    {
        $channel = self::getChannel();

        if ($channel) {
            $event = SocketEvent::create([
                'channel' => $channel,
                'socket_id' => $socket_id,
                'payload' => json_encode($payload),
            ]);

            SocketJob::dispatch($event->id);
        }
    }
}
