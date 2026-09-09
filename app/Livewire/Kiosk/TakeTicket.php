<?php

namespace App\Livewire\Kiosk;

use App\Events\QueueStatusUpdated;
use App\Models\QueueLog;
use App\Models\QueueTicket;
use App\Models\Service;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.kiosk')]
class TakeTicket extends Component
{
    public ?array $latestTicket = null;
    public bool $showSuccessModal = false;

    #[On('echo:queue,.queue.updated')]
    #[On('echo:queue,.queue.called')]
    public function refreshServices()
    {
        // Automatically re-renders component on live queue events
    }

    public function takeTicket(int $serviceId)
    {
        $service = Service::find($serviceId);
        if (! $service || ! $service->is_active) {
            session()->flash('error', 'Layanan tidak aktif atau tidak ditemukan.');
            return;
        }

        // Check if there is at least one active or open counter for this service
        $hasActiveCounter = $service->hasActiveCounter();

        $ticket = DB::transaction(function () use ($service) {
            $nextSequence = $service->getNextSequenceNumber();
            $ticketNumber = $service->formatTicketNumber($nextSequence);

            $ticket = QueueTicket::create([
                'ticket_number' => $ticketNumber,
                'sequence_number' => $nextSequence,
                'service_id' => $service->id,
                'status' => 'waiting',
                'queue_date' => today(),
            ]);

            QueueLog::create([
                'ticket_id' => $ticket->id,
                'service_id' => $service->id,
                'action' => 'created',
                'logged_at' => now(),
            ]);

            return $ticket;
        });

        // Broadcast event
        event(new QueueStatusUpdated('ticket_created', [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'service_id' => $service->id,
        ]));

        $trackingUrl = route('tracking.ticket', ['token' => $ticket->tracking_token]);
        $qrSvg = QrCodeService::generateSvg($trackingUrl, 160);

        $this->latestTicket = [
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'service_name' => $service->name,
            'sequence_number' => $ticket->sequence_number,
            'ahead_count' => $ticket->getTicketsAheadCount(),
            'estimated_wait' => $ticket->getEstimatedWaitTimeMinutes(),
            'tracking_url' => $trackingUrl,
            'qr_svg' => $qrSvg,
            'created_at' => $ticket->created_at->format('d/m/Y H:i:s'),
        ];

        $this->showSuccessModal = true;

        // Dispatch browser event to execute silent thermal printing
        $this->dispatch('print-ticket', ticket: $this->latestTicket);
    }

    public function closeModal()
    {
        $this->showSuccessModal = false;
        $this->latestTicket = null;
    }

    public function render()
    {
        $services = Service::where('is_active', true)
            ->withCount(['waitingTickets'])
            ->with(['counters'])
            ->get();

        return view('livewire.kiosk.take-ticket', [
            'services' => $services,
        ]);
    }
}
