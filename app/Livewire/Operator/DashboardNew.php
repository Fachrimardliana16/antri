<?php

namespace App\Livewire\Operator;

use App\Events\QueueCalled;
use App\Events\QueueStatusUpdated;
use App\Models\Counter;
use App\Models\QueueLog;
use App\Models\QueueTicket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.operator-minimal')]
class DashboardNew extends Component
{
    public ?Counter $myCounter = null;
    public string $counterStatus = 'closed';

    public function mount()
    {
        $user = Auth::user();

        // Operator hanya bisa akses loket yang di-assign ke mereka
        if (!$user->assigned_counter_id) {
            session()->flash('error', 'Anda belum ditugaskan ke loket manapun. Hubungi administrator.');
            return;
        }

        $this->myCounter = Counter::with(['service', 'currentTicket'])->find($user->assigned_counter_id);

        if ($this->myCounter) {
            $this->counterStatus = $this->myCounter->status;

            // Set operator sebagai current operator jika belum
            if ($this->myCounter->current_operator_id !== $user->id) {
                $this->myCounter->update(['current_operator_id' => $user->id]);
            }
        }
    }

    #[On('echo:queue,.queue.updated')]
    #[On('echo:queue,.queue.called')]
    public function refreshData()
    {
        if ($this->myCounter) {
            $this->myCounter->refresh();
            $this->myCounter->load(['service', 'currentTicket']);
        }
    }

    /**
     * Buka loket (set status aktif)
     */
    public function openCounter()
    {
        if (!$this->myCounter) {
            session()->flash('error', 'Loket tidak ditemukan.');
            return;
        }

        $this->myCounter->update([
            'status' => 'active',
            'current_operator_id' => Auth::id(),
        ]);

        $this->counterStatus = 'active';

        event(new QueueStatusUpdated('counter_opened', [
            'counter_id' => $this->myCounter->id,
            'counter_name' => $this->myCounter->name,
        ]));

        session()->flash('success', 'Loket berhasil dibuka. Siap melayani.');
    }

    /**
     * Tutup loket (set status closed)
     */
    public function closeCounter()
    {
        if (!$this->myCounter) {
            return;
        }

        // Jika ada tiket aktif, selesaikan dulu
        if ($this->myCounter->current_ticket_id) {
            $ticket = QueueTicket::find($this->myCounter->current_ticket_id);
            if ($ticket) {
                $this->finishTicket($ticket);
            }
        }

        $this->myCounter->update([
            'status' => 'closed',
            'current_ticket_id' => null,
        ]);

        $this->counterStatus = 'closed';

        event(new QueueStatusUpdated('counter_closed', [
            'counter_id' => $this->myCounter->id,
            'counter_name' => $this->myCounter->name,
        ]));

        session()->flash('success', 'Loket berhasil ditutup.');
    }

    /**
     * Istirahat (set status break)
     */
    public function takeBreak()
    {
        if (!$this->myCounter) {
            return;
        }

        $this->myCounter->update([
            'status' => 'break',
        ]);

        $this->counterStatus = 'break';

        event(new QueueStatusUpdated('counter_break', [
            'counter_id' => $this->myCounter->id,
            'counter_name' => $this->myCounter->name,
        ]));

        session()->flash('success', 'Status loket diubah ke Istirahat.');
    }

    /**
     * Panggil nomor antrian berikutnya
     */
    public function callNext()
    {
        if (!$this->myCounter || $this->counterStatus !== 'active') {
            session()->flash('error', 'Loket harus dalam status Aktif untuk memanggil antrian.');
            return;
        }

        DB::transaction(function () {
            // Auto-selesaikan tiket sebelumnya jika ada
            if ($this->myCounter->current_ticket_id) {
                $currentTicket = QueueTicket::find($this->myCounter->current_ticket_id);
                if ($currentTicket && in_array($currentTicket->status, ['calling', 'serving'])) {
                    $this->finishTicket($currentTicket);
                }
            }

            // Cari tiket berikutnya untuk service loket ini
            $nextTicket = QueueTicket::where('status', 'waiting')
                ->where('service_id', $this->myCounter->service_id)
                ->whereDate('queue_date', today())
                ->orderBy('sequence_number', 'asc')
                ->first();

            if (!$nextTicket) {
                session()->flash('error', 'Tidak ada antrian yang menunggu.');
                return;
            }

            $now = now();
            $waitDuration = $nextTicket->created_at ? (int) $nextTicket->created_at->diffInSeconds($now) : 0;

            // Update tiket
            $nextTicket->update([
                'status' => 'calling',
                'counter_id' => $this->myCounter->id,
                'operator_id' => Auth::id(),
                'called_at' => $now,
                'served_at' => $now,
            ]);

            // Update loket
            $this->myCounter->update([
                'current_ticket_id' => $nextTicket->id,
            ]);

            // Log
            QueueLog::create([
                'ticket_id' => $nextTicket->id,
                'service_id' => $nextTicket->service_id,
                'counter_id' => $this->myCounter->id,
                'operator_id' => Auth::id(),
                'action' => 'called',
                'wait_duration_seconds' => $waitDuration,
                'logged_at' => $now,
            ]);

            // Broadcast ke TV dan Kiosk
            event(new QueueCalled($nextTicket, 'next'));

            event(new QueueStatusUpdated('ticket_called', [
                'ticket_id' => $nextTicket->id,
                'counter_id' => $this->myCounter->id,
            ]));

            $this->myCounter->refresh();
            $this->myCounter->load(['currentTicket']);
        });
    }

    /**
     * Panggil ulang nomor yang sedang aktif
     */
    public function recallCurrent()
    {
        if (!$this->myCounter || !$this->myCounter->current_ticket_id) {
            session()->flash('error', 'Tidak ada nomor yang sedang aktif.');
            return;
        }

        $ticket = $this->myCounter->currentTicket;
        if (!$ticket) {
            return;
        }

        QueueLog::create([
            'ticket_id' => $ticket->id,
            'service_id' => $ticket->service_id,
            'counter_id' => $this->myCounter->id,
            'operator_id' => Auth::id(),
            'action' => 'recalled',
            'logged_at' => now(),
        ]);

        // Broadcast ulang ke TV
        event(new QueueCalled($ticket, 'recall'));

        session()->flash('success', "Memanggil ulang nomor {$ticket->ticket_number}");
    }

    /**
     * Selesaikan layanan (finish/complete)
     */
    public function completeCurrent()
    {
        if (!$this->myCounter || !$this->myCounter->current_ticket_id) {
            session()->flash('error', 'Tidak ada nomor yang sedang dilayani.');
            return;
        }

        $ticket = $this->myCounter->currentTicket;
        if (!$ticket) {
            return;
        }

        $this->finishTicket($ticket);

        $this->myCounter->refresh();
        $this->myCounter->load(['currentTicket']);

        session()->flash('success', "Nomor {$ticket->ticket_number} selesai dilayani.");
    }

    /**
     * Tolak/reject nomor antrian (misalnya: tidak hadir, salah loket, dll)
     */
    public function rejectCurrent()
    {
        if (!$this->myCounter || !$this->myCounter->current_ticket_id) {
            session()->flash('error', 'Tidak ada nomor yang sedang aktif.');
            return;
        }

        $ticket = $this->myCounter->currentTicket;
        if (!$ticket) {
            return;
        }

        $now = now();

        $ticket->update([
            'status' => 'rejected',
            'completed_at' => $now,
        ]);

        $this->myCounter->update([
            'current_ticket_id' => null,
        ]);

        QueueLog::create([
            'ticket_id' => $ticket->id,
            'service_id' => $ticket->service_id,
            'counter_id' => $this->myCounter->id,
            'operator_id' => Auth::id(),
            'action' => 'rejected',
            'notes' => 'Ditolak oleh operator',
            'logged_at' => $now,
        ]);

        event(new QueueStatusUpdated('ticket_rejected', [
            'ticket_id' => $ticket->id,
            'counter_id' => $this->myCounter->id,
        ]));

        $this->myCounter->refresh();
        $this->myCounter->load(['currentTicket']);

        session()->flash('success', "Nomor {$ticket->ticket_number} ditolak.");
    }

    /**
     * Helper: finish ticket
     */
    protected function finishTicket(QueueTicket $ticket)
    {
        $now = now();
        $startTime = $ticket->served_at ?? $ticket->called_at ?? $now;
        $serviceDuration = (int) $startTime->diffInSeconds($now);

        $ticket->update([
            'status' => 'completed',
            'completed_at' => $now,
        ]);

        $this->myCounter->update([
            'current_ticket_id' => null,
        ]);

        QueueLog::create([
            'ticket_id' => $ticket->id,
            'service_id' => $ticket->service_id,
            'counter_id' => $this->myCounter->id,
            'operator_id' => Auth::id(),
            'action' => 'completed',
            'service_duration_seconds' => $serviceDuration,
            'logged_at' => $now,
        ]);

        event(new QueueStatusUpdated('ticket_finished', [
            'ticket_id' => $ticket->id,
            'counter_id' => $this->myCounter->id,
        ]));
    }

    public function render()
    {
        $waitingCount = 0;
        $nextTicket = null;

        if ($this->myCounter && $this->myCounter->service_id) {
            $waitingCount = QueueTicket::where('service_id', $this->myCounter->service_id)
                ->where('status', 'waiting')
                ->whereDate('queue_date', today())
                ->count();

            $nextTicket = QueueTicket::where('service_id', $this->myCounter->service_id)
                ->where('status', 'waiting')
                ->whereDate('queue_date', today())
                ->orderBy('sequence_number', 'asc')
                ->first();
        }

        // Stats hari ini
        $todayServed = QueueTicket::where('operator_id', Auth::id())
            ->where('status', 'completed')
            ->whereDate('queue_date', today())
            ->count();

        $todayRejected = QueueTicket::where('operator_id', Auth::id())
            ->where('status', 'rejected')
            ->whereDate('queue_date', today())
            ->count();

        return view('livewire.operator.dashboard-clean', [
            'waitingCount' => $waitingCount,
            'nextTicket' => $nextTicket,
            'todayServed' => $todayServed,
            'todayRejected' => $todayRejected,
        ]);
    }
}
