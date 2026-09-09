<?php

namespace App\Livewire\Admin;

use App\Events\ThemeUpdated;
use App\Models\AppSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Settings extends Component
{
    public string $appName = '';
    public string $primaryColor = '#2563eb';
    public string $secondaryColor = '#06b6d4';
    public string $marqueeText = '';
    public string $videoUrl = '';
    public string $voiceRate = '0.9';
    public string $voicePitch = '1.0';
    public string $voiceLang = 'id-ID';

    public function mount()
    {
        $settings = AppSetting::getAll();
        $this->appName = $settings['app_name'] ?? 'Sistem Antrian Terpadu';
        $this->primaryColor = $settings['primary_color'] ?? '#2563eb';
        $this->secondaryColor = $settings['secondary_color'] ?? '#06b6d4';
        $this->marqueeText = $settings['marquee_text'] ?? '';
        $this->videoUrl = $settings['video_url'] ?? '';
        $this->voiceRate = $settings['voice_rate'] ?? '0.9';
        $this->voicePitch = $settings['voice_pitch'] ?? '1.0';
        $this->voiceLang = $settings['voice_lang'] ?? 'id-ID';
    }

    public function save()
    {
        $this->validate([
            'appName' => 'required|string|max:100',
            'primaryColor' => 'required|string|max:20',
            'secondaryColor' => 'required|string|max:20',
            'voiceRate' => 'required',
            'voicePitch' => 'required',
        ]);

        AppSetting::setValue('app_name', $this->appName);
        AppSetting::setValue('primary_color', $this->primaryColor);
        AppSetting::setValue('secondary_color', $this->secondaryColor);
        AppSetting::setValue('marquee_text', $this->marqueeText);
        AppSetting::setValue('video_url', $this->videoUrl);
        AppSetting::setValue('voice_rate', $this->voiceRate);
        AppSetting::setValue('voice_pitch', $this->voicePitch);
        AppSetting::setValue('voice_lang', $this->voiceLang);

        $newSettings = AppSetting::getAll();

        // Broadcast ThemeUpdated event to all connected TV & Kiosk clients
        event(new ThemeUpdated($newSettings));

        session()->flash('success', 'Pengaturan tema & sistem berhasil disimpan dan disinkronkan secara real-time!');
    }

    public function render()
    {
        return view('livewire.admin.settings');
    }
}
