<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $action;
    public array $payload;

    public function __construct(string $action, array $payload = [])
    {
        $this->action = $action;
        $this->payload = $payload;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('queue'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'queue.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action,
            'payload' => $this->payload,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
