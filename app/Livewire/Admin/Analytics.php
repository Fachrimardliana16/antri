<?php

namespace App\Livewire\Admin;

use App\Models\Counter;
use App\Models\QueueLog;
use App\Models\QueueTicket;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Analytics extends Component
{
    public string $period = 'today'; // today, week, month, all

    public function setPeriod(string $period)
    {
        $this->period = $period;
    }

    public function render()
    {
        $startDate = match ($this->period) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->subDays(7),
            'month' => Carbon::now()->subDays(30),
            default => Carbon::createFromTimestamp(0),
        };

        // Ticket counts
        $ticketQuery = QueueTicket::where('created_at', '>=', $startDate);
        $totalTickets = (clone $ticketQuery)->count();
        $completedTickets = (clone $ticketQuery)->where('status', 'completed')->count();
        $skippedTickets = (clone $ticketQuery)->where('status', 'skipped')->count();
        $waitingTickets = (clone $ticketQuery)->where('status', 'waiting')->count();

        // REQ-F-11: Average Wait Time & Average Service Time from queue_logs
        $avgWaitSeconds = QueueLog::where('logged_at', '>=', $startDate)
            ->whereNotNull('wait_duration_seconds')
            ->where('wait_duration_seconds', '>', 0)
            ->avg('wait_duration_seconds') ?? 0;

        $avgServiceSeconds = QueueLog::where('logged_at', '>=', $startDate)
            ->whereNotNull('service_duration_seconds')
            ->where('service_duration_seconds', '>', 0)
            ->avg('service_duration_seconds') ?? 0;

        // Breakdown by Service
        $services = Service::withCount([
            'tickets as total_tickets_count' => function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate);
            },
            'tickets as completed_tickets_count' => function ($q) use ($startDate) {
                $q->where('created_at', '>=', $startDate)->where('status', 'completed');
            },
        ])->get();

        // Breakdown by Operator
        $operators = User::where('role', 'operator')
            ->withCount([
                'queueLogs as served_count' => function ($q) use ($startDate) {
                    $q->where('logged_at', '>=', $startDate)->where('action', 'completed');
                },
                'queueLogs as skipped_count' => function ($q) use ($startDate) {
                    $q->where('logged_at', '>=', $startDate)->where('action', 'skipped');
                },
            ])
            ->get();

        // Format times into mm:ss or minutes
        $avgWaitFormatted = sprintf('%02d menit %02d detik', floor($avgWaitSeconds / 60), $avgWaitSeconds % 60);
        $avgServiceFormatted = sprintf('%02d menit %02d detik', floor($avgServiceSeconds / 60), $avgServiceSeconds % 60);

        return view('livewire.admin.analytics', [
            'totalTickets' => $totalTickets,
            'completedTickets' => $completedTickets,
            'skippedTickets' => $skippedTickets,
            'waitingTickets' => $waitingTickets,
            'avgWaitFormatted' => $avgWaitFormatted,
            'avgServiceFormatted' => $avgServiceFormatted,
            'avgWaitSeconds' => (int) $avgWaitSeconds,
            'avgServiceSeconds' => (int) $avgServiceSeconds,
            'services' => $services,
            'operators' => $operators,
        ]);
    }
}
