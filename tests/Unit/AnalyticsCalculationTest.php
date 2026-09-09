<?php

namespace Tests\Unit;

use App\Models\QueueLog;
use App\Models\QueueTicket;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_average_wait_and_service_duration_calculation()
    {
        $service = Service::create([
            'name' => 'Layanan Test',
            'code' => 'T',
            'prefix' => 'T',
            'estimated_time_minutes' => 5,
            'color' => '#2563eb',
            'is_active' => true,
        ]);

        $ticket1 = QueueTicket::create([
            'ticket_number' => 'T-001',
            'sequence_number' => 1,
            'service_id' => $service->id,
            'status' => 'completed',
            'queue_date' => today(),
        ]);

        $ticket2 = QueueTicket::create([
            'ticket_number' => 'T-002',
            'sequence_number' => 2,
            'service_id' => $service->id,
            'status' => 'completed',
            'queue_date' => today(),
        ]);

        // Ticket 1: waited 60s, served 120s
        QueueLog::create([
            'ticket_id' => $ticket1->id,
            'service_id' => $service->id,
            'action' => 'called',
            'wait_duration_seconds' => 60,
            'logged_at' => now(),
        ]);
        QueueLog::create([
            'ticket_id' => $ticket1->id,
            'service_id' => $service->id,
            'action' => 'completed',
            'service_duration_seconds' => 120,
            'logged_at' => now(),
        ]);

        // Ticket 2: waited 180s, served 240s
        QueueLog::create([
            'ticket_id' => $ticket2->id,
            'service_id' => $service->id,
            'action' => 'called',
            'wait_duration_seconds' => 180,
            'logged_at' => now(),
        ]);
        QueueLog::create([
            'ticket_id' => $ticket2->id,
            'service_id' => $service->id,
            'action' => 'completed',
            'service_duration_seconds' => 240,
            'logged_at' => now(),
        ]);

        $avgWait = QueueLog::whereNotNull('wait_duration_seconds')->avg('wait_duration_seconds');
        $avgService = QueueLog::whereNotNull('service_duration_seconds')->avg('service_duration_seconds');

        // (60 + 180) / 2 = 120
        $this->assertEquals(120, $avgWait);

        // (120 + 240) / 2 = 180
        $this->assertEquals(180, $avgService);
    }
}
