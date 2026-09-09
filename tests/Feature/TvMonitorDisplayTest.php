<?php

namespace Tests\Feature;

use App\Livewire\Display\TvMonitor;
use App\Models\Counter;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TvMonitorDisplayTest extends TestCase
{
    use RefreshDatabase;

    public function test_tv_monitor_only_renders_active_counters()
    {
        $service = Service::create([
            'name' => 'Layanan Umum',
            'code' => 'A',
            'prefix' => 'A',
            'estimated_time_minutes' => 5,
            'color' => '#2563eb',
            'is_active' => true,
        ]);

        $activeCounter = Counter::create([
            'name' => 'Loket Aktif 1',
            'number' => 1,
            'service_id' => $service->id,
            'status' => 'active',
        ]);

        $closedCounter = Counter::create([
            'name' => 'Loket Tutup 2',
            'number' => 2,
            'service_id' => $service->id,
            'status' => 'closed',
        ]);

        Livewire::test(TvMonitor::class)
            ->assertSee('Loket Aktif 1')
            ->assertDontSee('Loket Tutup 2');
    }
}
