<?php

namespace Tests\Feature;

use App\Events\QueueCalled;
use App\Livewire\Kiosk\TakeTicket;
use App\Livewire\Operator\Dashboard;
use App\Livewire\Tracking\LiveTicket;
use App\Models\Counter;
use App\Models\QueueLog;
use App\Models\QueueTicket;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class QueueTicketFlowTest extends TestCase
{
    use RefreshDatabase;

    protected Service $serviceA;
    protected Service $serviceB;
    protected Counter $counter1;
    protected User $operator1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->serviceA = Service::create([
            'name' => 'Layanan Administrasi',
            'code' => 'A',
            'prefix' => 'A',
            'estimated_time_minutes' => 5,
            'color' => '#2563eb',
            'is_active' => true,
        ]);

        $this->serviceB = Service::create([
            'name' => 'Layanan Kasir',
            'code' => 'B',
            'prefix' => 'B',
            'estimated_time_minutes' => 4,
            'color' => '#10b981',
            'is_active' => true,
        ]);

        $this->operator1 = User::create([
            'name' => 'Operator Test',
            'email' => 'operator@test.local',
            'password' => bcrypt('password'),
            'role' => 'operator',
        ]);

        $this->counter1 = Counter::create([
            'name' => 'Loket 1',
            'number' => 1,
            'service_id' => $this->serviceA->id,
            'current_operator_id' => $this->operator1->id,
            'status' => 'active',
        ]);

        $this->operator1->update(['assigned_counter_id' => $this->counter1->id]);
    }

    public function test_kiosk_can_issue_ticket_and_generate_qr_tracking()
    {
        Livewire::test(TakeTicket::class)
            ->call('takeTicket', $this->serviceA->id)
            ->assertSet('showSuccessModal', true)
            ->assertDispatched('print-ticket');

        $this->assertDatabaseHas('queue_tickets', [
            'ticket_number' => 'A-001',
            'sequence_number' => 1,
            'service_id' => $this->serviceA->id,
            'status' => 'waiting',
        ]);

        $this->assertDatabaseHas('queue_logs', [
            'service_id' => $this->serviceA->id,
            'action' => 'created',
        ]);
    }

    public function test_operator_can_call_next_ticket_and_dispatches_event()
    {
        Event::fake([QueueCalled::class]);

        // Issue 2 tickets
        $ticket1 = QueueTicket::create([
            'ticket_number' => 'A-001',
            'sequence_number' => 1,
            'service_id' => $this->serviceA->id,
            'status' => 'waiting',
            'queue_date' => today(),
        ]);

        $ticket2 = QueueTicket::create([
            'ticket_number' => 'A-002',
            'sequence_number' => 2,
            'service_id' => $this->serviceA->id,
            'status' => 'waiting',
            'queue_date' => today(),
        ]);

        Livewire::actingAs($this->operator1)
            ->test(Dashboard::class)
            ->call('setCounter', $this->counter1->id)
            ->call('next');

        $ticket1->refresh();
        $this->assertEquals('calling', $ticket1->status);
        $this->assertEquals($this->counter1->id, $ticket1->counter_id);

        $this->assertDatabaseHas('counters', [
            'id' => $this->counter1->id,
            'current_ticket_id' => $ticket1->id,
        ]);

        Event::assertDispatched(QueueCalled::class, function ($e) use ($ticket1) {
            return $e->ticketData['id'] === $ticket1->id;
        });
    }

    public function test_operator_can_finish_and_skip_ticket()
    {
        $ticket = QueueTicket::create([
            'ticket_number' => 'A-001',
            'sequence_number' => 1,
            'service_id' => $this->serviceA->id,
            'status' => 'calling',
            'counter_id' => $this->counter1->id,
            'called_at' => now()->subMinutes(3),
            'served_at' => now()->subMinutes(3),
            'queue_date' => today(),
        ]);

        $this->counter1->update(['current_ticket_id' => $ticket->id]);

        // Finish ticket
        Livewire::actingAs($this->operator1)
            ->test(Dashboard::class)
            ->call('setCounter', $this->counter1->id)
            ->call('finish');

        $ticket->refresh();
        $this->assertEquals('completed', $ticket->status);
        $this->assertNotNull($ticket->completed_at);

        $this->assertDatabaseHas('queue_logs', [
            'ticket_id' => $ticket->id,
            'action' => 'completed',
        ]);
    }

    public function test_operator_can_transfer_ticket_to_another_service()
    {
        $ticket = QueueTicket::create([
            'ticket_number' => 'A-001',
            'sequence_number' => 1,
            'service_id' => $this->serviceA->id,
            'status' => 'calling',
            'counter_id' => $this->counter1->id,
            'queue_date' => today(),
        ]);

        $this->counter1->update(['current_ticket_id' => $ticket->id]);

        Livewire::actingAs($this->operator1)
            ->test(Dashboard::class)
            ->call('setCounter', $this->counter1->id)
            ->set('transferServiceId', $this->serviceB->id)
            ->call('executeTransfer');

        $ticket->refresh();
        $this->assertEquals('transferred', $ticket->status);
        $this->assertEquals($this->serviceB->id, $ticket->transferred_to_service_id);

        // New ticket in service B should be created
        $this->assertDatabaseHas('queue_tickets', [
            'ticket_number' => 'B-001',
            'service_id' => $this->serviceB->id,
            'status' => 'waiting',
        ]);
    }

    public function test_customer_live_tracking_view()
    {
        $ticket = QueueTicket::create([
            'ticket_number' => 'A-001',
            'sequence_number' => 1,
            'service_id' => $this->serviceA->id,
            'status' => 'waiting',
            'queue_date' => today(),
        ]);

        Livewire::test(LiveTicket::class, ['token' => $ticket->tracking_token])
            ->assertSee('A-001')
            ->assertSee($this->serviceA->name);
    }
}
