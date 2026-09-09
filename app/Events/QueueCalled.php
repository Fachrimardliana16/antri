<?php

namespace App\Events;

use App\Models\QueueTicket;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QueueCalled implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public array $ticketData;
    public string $voiceText;
    public string $callType; // 'next' or 'recall'

    public function __construct(QueueTicket $ticket, string $callType = 'next')
    {
        $this->callType = $callType;
        $counter = $ticket->counter;
        $service = $ticket->service;

        $this->ticketData = [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'sequence_number' => $ticket->sequence_number,
            'service_name' => $service ? $service->name : '',
            'service_color' => $service ? $service->color : '#2563eb',
            'counter_id' => $counter ? $counter->id : null,
            'counter_number' => $counter ? $counter->number : 1,
            'counter_name' => $counter ? $counter->name : 'Loket 1',
            'status' => $ticket->status,
            'called_at' => now()->toIso8601String(),
        ];

        $counterNum = $counter ? $counter->number : 1;
        $cleanNumber = $ticket->ticket_number;
        $this->voiceText = "Nomor antrean {$cleanNumber}, menuju loket {$counterNum}";
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('queue'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'queue.called';
    }

    public function broadcastWith(): array
    {
        return [
            'ticket' => $this->ticketData,
            'voice_text' => $this->voiceText,
            'call_type' => $this->callType,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
