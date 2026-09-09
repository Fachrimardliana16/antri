<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThemeUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $settings;

    public function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('theme'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'theme.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'settings' => $this->settings,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
