<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChannelEvents implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $channel;
    public $event_id;
    public $socket_id;
    public $data;

    public function __construct($channel, $event_id, $socket_id, $data)
    {
        $this->channel = $channel;
        $this->event_id = $event_id;
        $this->socket_id = $socket_id;
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new Channel($this->channel);
    }

    public function broadcastAs()
    {
        return $this->channel;
    }

    public function broadcastWith()
    {
        return [
            'socket_id' => $this->socket_id,
            'event_id' => $this->event_id,
            'data' => $this->data,
        ];
    }
}
