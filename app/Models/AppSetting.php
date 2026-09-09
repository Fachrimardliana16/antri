<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember("app_setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function setValue(string $key, mixed $value): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        Cache::forget("app_setting_{$key}");
        Cache::forget('all_app_settings');
    }

    public static function getAll(): array
    {
        return Cache::remember('all_app_settings', 3600, function () {
            $settings = static::pluck('value', 'key')->toArray();
            return array_merge([
                'app_name' => 'Sistem Antrian Terpadu',
                'primary_color' => '#2563eb', // Modern vibrant Indigo/Blue
                'secondary_color' => '#06b6d4', // Cyan accent
                'logo_url' => '',
                'marquee_text' => 'Selamat datang di Sistem Layanan Terpadu. Mohon menunggu nomor antrean Anda dipanggil.',
                'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&mute=1&loop=1',
                'voice_rate' => '0.9',
                'voice_pitch' => '1.0',
                'voice_lang' => 'id-ID',
            ], $settings);
        });
    }
}
