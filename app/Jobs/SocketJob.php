<?php

namespace App\Jobs;

use App\Events\ChannelEvents;
use App\Models\SocketEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class SocketJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $event_id;
    public $dbName;

    /**
     * Create a new job instance.
     */
    public function __construct($event_id, $dbName)
    {
        $this->event_id = $event_id;
        $this->dbName = $dbName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::statement("SET search_path TO {$this->dbName}");
        $event = SocketEvent::find($this->event_id);

        Log::info("SocketJob.handle");
        Log::info(json_encode($event));

        if ($event->active) {
            $channel = $event->channel;
            $event_id = $event->id;
            $socket_id = $event->socket_id;
            $payload = json_decode($event->payload, true);

            Event::dispatch(new ChannelEvents($channel, $event_id, $socket_id, $payload));
        }

        DB::statement("SET search_path TO public");
    }
}
