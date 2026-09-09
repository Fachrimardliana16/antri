<?php

namespace App\Console\Commands;

use App\Events\QueueStatusUpdated;
use App\Models\Counter;
use App\Models\QueueLog;
use App\Models\QueueTicket;
use Illuminate\Console\Command;

class ResetDailyQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:reset-daily {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset sequence counter and close remaining active tickets for the day (scheduled at 00:00)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting daily queue reset procedure...');

        // 1. Close remaining waiting/calling tickets from previous day
        $updatedTickets = QueueTicket::whereIn('status', ['waiting', 'calling', 'serving'])
            ->whereDate('queue_date', '<', today())
            ->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

        // 2. Reset counters current ticket pointer
        Counter::query()->update([
            'current_ticket_id' => null,
        ]);

        $this->info("Completed daily reset: {$updatedTickets} previous tickets finalized.");

        // Broadcast reset event
        event(new QueueStatusUpdated('daily_reset', [
            'reset_at' => now()->toIso8601String(),
            'finalized_count' => $updatedTickets,
        ]));

        return Command::SUCCESS;
    }
}
