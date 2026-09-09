<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\QueueTicket;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyResetCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_reset_command_finalizes_previous_day_tickets()
    {
        $service = Service::create([
            'name' => 'Layanan Umum',
            'code' => 'A',
            'prefix' => 'A',
            'estimated_time_minutes' => 5,
            'color' => '#2563eb',
            'is_active' => true,
        ]);

        $yesterdayTicket = QueueTicket::create([
            'ticket_number' => 'A-099',
            'sequence_number' => 99,
            'service_id' => $service->id,
            'status' => 'waiting',
            'queue_date' => today()->subDay(),
        ]);

        $counter = Counter::create([
            'name' => 'Loket 1',
            'number' => 1,
            'service_id' => $service->id,
            'current_ticket_id' => $yesterdayTicket->id,
            'status' => 'active',
        ]);

        $this->artisan('queue:reset-daily --force')
            ->assertSuccessful();

        $yesterdayTicket->refresh();
        $counter->refresh();

        $this->assertEquals('completed', $yesterdayTicket->status);
        $this->assertNull($counter->current_ticket_id);
    }
}
