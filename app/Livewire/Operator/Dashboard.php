<?php

namespace App\Livewire\Operator;

use App\Events\QueueCalled;
use App\Events\QueueStatusUpdated;
use App\Models\Counter;
use App\Models\QueueLog;
use App\Models\QueueTicket;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public ?int $selectedCounterId = null;
    public string $counterStatus = 'active'; // active, break, closed
    public ?int $transferServiceId = null;
    public bool $showTransferModal = false;

    public function mount()
    {
        $user = Auth::user();
        if ($user->assigned_counter_id) {
            $this->selectedCounterId = $user->assigned_counter_id;
        } else {
            $firstCounter = Counter::first();
            $this->selectedCounterId = $firstCounter ? $firstCounter->id : null;
        }

        if ($this->selectedCounterId) {
            $counter = Counter::find($this->selectedCounterId);
            if ($counter) {
                $this->counterStatus = $counter->status;
            }
        }
    }

    #[On('echo:queue,.queue.updated')]
    #[On('echo:queue,.queue.called')]
    public function refreshData()
    {
        // Re-renders operator dashboard upon queue updates
    }

    public function setCounter(int $counterId)
    {
        $this->selectedCounterId = $counterId;
        $counter = Counter::find($counterId);
        if ($counter) {
            $this->counterStatus = $counter->status;
            $counter->update(['current_operator_id' => Auth::id()]);
            Auth::user()->update(['assigned_counter_id' => $counterId]);
        }
    }

    public function updateStatus(string $newStatus)
    {
        if (! in_array($newStatus, ['active', 'break', 'closed'])) {
            return;
        }

        $counter = Counter::find($this->selectedCounterId);
        if (! $counter) {
            session()->flash('error', 'Loket belum dipilih.');
            return;
        }

        $counter->update([
            'status' => $newStatus,
            'current_operator_id' => $newStatus === 'closed' ? null : Auth::id(),
        ]);
        $this->counterStatus = $newStatus;

        event(new QueueStatusUpdated('counter_status_changed', [
            'counter_id' => $counter->id,
            'status' => $newStatus,
        ]));

        session()->flash('success', "Status loket diubah menjadi: " . strtoupper($newStatus));
    }

    public function next()
    {
        $counter = Counter::find($this->selectedCounterId);
        if (! $counter || $counter->status !== 'active') {
            session()->flash('error', 'Loket harus berstatus Aktif untuk memanggil antrean.');
            return;
        }

        DB::transaction(function () use ($counter) {
            // If current ticket is calling or serving, auto complete it before next
            if ($counter->current_ticket_id) {
                $currentTicket = QueueTicket::find($counter->current_ticket_id);
                if ($currentTicket && in_array($currentTicket->status, ['calling', 'serving'])) {
                    $this->finishCurrentTicket($currentTicket, $counter);
                }
            }

            // Find next waiting ticket for the counter's service
            $query = QueueTicket::where('status', 'waiting')
                ->whereDate('queue_date', today());

            if ($counter->service_id) {
                $query->where('service_id', $counter->service_id);
            }

            $nextTicket = $query->orderBy('sequence_number', 'asc')->first();

            if (! $nextTicket) {
                session()->flash('error', 'Tidak ada antrean yang sedang menunggu.');
                return;
            }

            $now = now();
            $waitDuration = $nextTicket->created_at ? (int) $nextTicket->created_at->diffInSeconds($now) : 0;

            $nextTicket->update([
                'status' => 'calling',
                'counter_id' => $counter->id,
                'operator_id' => Auth::id(),
                'called_at' => $now,
                'served_at' => $now,
            ]);

            $counter->update([
                'current_ticket_id' => $nextTicket->id,
                'current_operator_id' => Auth::id(),
            ]);

            QueueLog::create([
                'ticket_id' => $nextTicket->id,
                'service_id' => $nextTicket->service_id,
                'counter_id' => $counter->id,
                'operator_id' => Auth::id(),
                'action' => 'called',
                'wait_duration_seconds' => $waitDuration,
                'logged_at' => $now,
            ]);

            // Dispatch WebSocket Call Event
            event(new QueueCalled($nextTicket, 'next'));

            event(new QueueStatusUpdated('ticket_called', [
                'ticket_id' => $nextTicket->id,
                'counter_id' => $counter->id,
            ]));
        });
    }

    public function recall()
    {
        $counter = Counter::find($this->selectedCounterId);
        if (! $counter || ! $counter->current_ticket_id) {
            session()->flash('error', 'Tidak ada nomor yang sedang aktif untuk dipanggil ulang.');
            return;
        }

        $ticket = QueueTicket::find($counter->current_ticket_id);
        if (! $ticket) {
            return;
        }

        QueueLog::create([
            'ticket_id' => $ticket->id,
            'service_id' => $ticket->service_id,
            'counter_id' => $counter->id,
            'operator_id' => Auth::id(),
            'action' => 'recalled',
            'logged_at' => now(),
        ]);

        // Dispatch WebSocket Call Event
        event(new QueueCalled($ticket, 'recall'));

        session()->flash('success', "Memanggil ulang nomor: {$ticket->ticket_number}");
    }

    public function finish()
    {
        $counter = Counter::find($this->selectedCounterId);
        if (! $counter || ! $counter->current_ticket_id) {
            session()->flash('error', 'Tidak ada nomor yang sedang dilayani.');
            return;
        }

        $ticket = QueueTicket::find($counter->current_ticket_id);
        if (! $ticket) {
            return;
        }

        $this->finishCurrentTicket($ticket, $counter);
        session()->flash('success', "Nomor {$ticket->ticket_number} berhasil diselesaikan.");
    }

    protected function finishCurrentTicket(QueueTicket $ticket, Counter $counter)
    {
        $now = now();
        $startTime = $ticket->served_at ?? $ticket->called_at ?? $now;
        $serviceDuration = (int) $startTime->diffInSeconds($now);

        $ticket->update([
            'status' => 'completed',
            'completed_at' => $now,
        ]);

        $counter->update([
            'current_ticket_id' => null,
        ]);

        QueueLog::create([
            'ticket_id' => $ticket->id,
            'service_id' => $ticket->service_id,
            'counter_id' => $counter->id,
            'operator_id' => Auth::id(),
            'action' => 'completed',
            'service_duration_seconds' => $serviceDuration,
            'logged_at' => $now,
        ]);

        event(new QueueStatusUpdated('ticket_finished', [
            'ticket_id' => $ticket->id,
            'counter_id' => $counter->id,
        ]));
    }

    public function skip()
    {
        $counter = Counter::find($this->selectedCounterId);
        if (! $counter || ! $counter->current_ticket_id) {
            session()->flash('error', 'Tidak ada nomor yang sedang aktif untuk dilewati.');
            return;
        }

        $ticket = QueueTicket::find($counter->current_ticket_id);
        if (! $ticket) {
            return;
        }

        $now = now();
        $ticket->update([
            'status' => 'skipped',
            'completed_at' => $now,
        ]);

        $counter->update([
            'current_ticket_id' => null,
        ]);

        QueueLog::create([
            'ticket_id' => $ticket->id,
            'service_id' => $ticket->service_id,
            'counter_id' => $counter->id,
            'operator_id' => Auth::id(),
            'action' => 'skipped',
            'logged_at' => $now,
        ]);

        event(new QueueStatusUpdated('ticket_skipped', [
            'ticket_id' => $ticket->id,
            'counter_id' => $counter->id,
        ]));

        session()->flash('success', "Nomor {$ticket->ticket_number} dilewati.");
    }

    public function openTransferModal()
    {
        $counter = Counter::find($this->selectedCounterId);
        if (! $counter || ! $counter->current_ticket_id) {
            session()->flash('error', 'Tidak ada nomor yang sedang aktif untuk ditransfer.');
            return;
        }
        $this->showTransferModal = true;
    }

    public function closeTransferModal()
    {
        $this->showTransferModal = false;
        $this->transferServiceId = null;
    }

    public function executeTransfer()
    {
        if (! $this->transferServiceId) {
            session()->flash('error', 'Pilih layanan tujuan transfer.');
            return;
        }

        $counter = Counter::find($this->selectedCounterId);
        if (! $counter || ! $counter->current_ticket_id) {
            return;
        }

        $targetService = Service::find($this->transferServiceId);
        if (! $targetService) {
            return;
        }

        $ticket = QueueTicket::find($counter->current_ticket_id);
        if (! $ticket) {
            return;
        }

        DB::transaction(function () use ($ticket, $counter, $targetService) {
            $now = now();
            // Mark current as transferred
            $ticket->update([
                'status' => 'transferred',
                'transferred_to_service_id' => $targetService->id,
                'completed_at' => $now,
            ]);

            $counter->update([
                'current_ticket_id' => null,
            ]);

            // Create new ticket in the target service
            $nextSequence = $targetService->getNextSequenceNumber();
            $newTicketNumber = $targetService->formatTicketNumber($nextSequence);

            $newTicket = QueueTicket::create([
                'ticket_number' => $newTicketNumber,
                'sequence_number' => $nextSequence,
                'service_id' => $targetService->id,
                'status' => 'waiting',
                'queue_date' => today(),
            ]);

            QueueLog::create([
                'ticket_id' => $ticket->id,
                'service_id' => $ticket->service_id,
                'counter_id' => $counter->id,
                'operator_id' => Auth::id(),
                'action' => 'transferred',
                'notes' => "Ditransfer ke {$targetService->name} (Tiket Baru: {$newTicketNumber})",
                'logged_at' => $now,
            ]);

            QueueLog::create([
                'ticket_id' => $newTicket->id,
                'service_id' => $targetService->id,
                'action' => 'created',
                'notes' => "Hasil transfer dari {$ticket->ticket_number}",
                'logged_at' => $now,
            ]);

            event(new QueueStatusUpdated('ticket_transferred', [
                'from_ticket' => $ticket->ticket_number,
                'new_ticket' => $newTicket->ticket_number,
            ]));
        });

        $this->closeTransferModal();
        session()->flash('success', "Antrean berhasil ditransfer ke {$targetService->name}.");
    }

    public function render()
    {
        $counters = Counter::with(['service', 'currentOperator', 'currentTicket'])->get();
        $currentCounter = Counter::with(['service', 'currentTicket.service'])->find($this->selectedCounterId);

        $services = Service::where('is_active', true)->get();

        // Queue waiting list for this counter's service
        $waitingTickets = collect();
        if ($currentCounter && $currentCounter->service_id) {
            $waitingTickets = QueueTicket::where('service_id', $currentCounter->service_id)
                ->where('status', 'waiting')
                ->whereDate('queue_date', today())
                ->orderBy('sequence_number', 'asc')
                ->get();
        }

        // Today's operator stats
        $servedTodayCount = QueueTicket::where('operator_id', Auth::id())
            ->where('status', 'completed')
            ->whereDate('queue_date', today())
            ->count();

        $skippedTodayCount = QueueTicket::where('operator_id', Auth::id())
            ->where('status', 'skipped')
            ->whereDate('queue_date', today())
            ->count();

        return view('livewire.operator.dashboard', [
            'counters' => $counters,
            'currentCounter' => $currentCounter,
            'services' => $services,
            'waitingTickets' => $waitingTickets,
            'servedTodayCount' => $servedTodayCount,
            'skippedTodayCount' => $skippedTodayCount,
        ]);
    }
}
