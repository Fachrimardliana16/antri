<?php

namespace App\Livewire\Display;

use App\Models\AppSetting;
use App\Models\Counter;
use App\Models\QueueTicket;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.tv-minimal')]
class TvMonitor extends Component
{
    public ?array $activeCall = null;
    public string $videoUrl = '';

    public function mount()
    {
        $this->videoUrl = AppSetting::getValue('video_url', 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&mute=1&loop=1');

        // Find latest calling/serving ticket
        $latestCalled = QueueTicket::whereIn('status', ['calling', 'serving'])
            ->whereDate('queue_date', today())
            ->latest('called_at')
            ->with(['service', 'counter'])
            ->first();

        if ($latestCalled) {
            $this->activeCall = [
                'id' => $latestCalled->id,
                'ticket_number' => $latestCalled->ticket_number,
                'service_name' => $latestCalled->service ? $latestCalled->service->name : '',
                'counter_number' => $latestCalled->counter ? $latestCalled->counter->number : 1,
                'counter_name' => $latestCalled->counter ? $latestCalled->counter->name : 'Loket 1',
                'status' => $latestCalled->status,
                'voice_text' => $latestCalled->getVoiceSpokenText(),
                'called_at' => $latestCalled->called_at ? $latestCalled->called_at->toIso8601String() : null,
            ];
        }
    }

    #[On('echo:queue,.queue.called')]
    public function handleQueueCalled(array $event)
    {
        $this->activeCall = [
            'id' => $event['ticket']['id'] ?? null,
            'ticket_number' => $event['ticket']['ticket_number'] ?? '',
            'service_name' => $event['ticket']['service_name'] ?? '',
            'counter_number' => $event['ticket']['counter_number'] ?? 1,
            'counter_name' => $event['ticket']['counter_name'] ?? 'Loket 1',
            'voice_text' => $event['voice_text'] ?? '',
            'call_type' => $event['call_type'] ?? 'next',
            'timestamp' => now()->toIso8601String(),
        ];

        // Dispatch browser event for Web Audio chime + TTS + Audio Ducking
        $this->dispatch('play-queue-call', data: $this->activeCall);
    }

    #[On('echo:queue,.queue.updated')]
    public function handleQueueUpdated()
    {
        // Re-renders view on queue updates
    }

    public function render()
    {
        // REQ-F-07: Display TV only renders counters that are active
        $activeCounters = Counter::where('status', 'active')
            ->with(['service', 'currentTicket'])
            ->orderBy('number', 'asc')
            ->get();

        $allCounters = Counter::with(['service', 'currentTicket'])
            ->orderBy('number', 'asc')
            ->get();

        return view('livewire.display.tv-monitor', [
            'activeCounters' => $activeCounters,
            'allCounters' => $allCounters,
        ]);
    }
}
