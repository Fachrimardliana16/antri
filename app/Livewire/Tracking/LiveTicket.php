<?php

namespace App\Livewire\Tracking;

use App\Models\QueueTicket;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.tracking')]
class LiveTicket extends Component
{
    public string $token;
    public ?int $ticketId = null;

    public function mount(string $token)
    {
        $this->token = $token;
        $ticket = QueueTicket::where('tracking_token', $token)->first();
        if ($ticket) {
            $this->ticketId = $ticket->id;
        }
    }

    #[On('echo:queue,.queue.called')]
    #[On('echo:queue,.queue.updated')]
    public function handleLiveUpdate()
    {
        // Automatically refreshes on queue events
    }

    public function render()
    {
        $ticket = QueueTicket::where('tracking_token', $this->token)
            ->with(['service', 'counter', 'operator'])
            ->first();

        $aheadCount = $ticket ? $ticket->getTicketsAheadCount() : 0;
        $estimatedWait = $ticket ? $ticket->getEstimatedWaitTimeMinutes() : 0;

        return view('livewire.tracking.live-ticket', [
            'ticket' => $ticket,
            'aheadCount' => $aheadCount,
            'estimatedWait' => $estimatedWait,
        ]);
    }
}
