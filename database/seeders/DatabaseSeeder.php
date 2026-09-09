<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Counter;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Users
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@antri.local'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
            ]
        );

        $admin = User::updateOrCreate(
            ['email' => 'admin@antri.local'],
            [
                'name' => 'Supervisor Layanan',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $operator1 = User::updateOrCreate(
            ['email' => 'operator1@antri.local'],
            [
                'name' => 'Budi Santoso (Operator 1)',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'assigned_counter_id' => 1,
            ]
        );

        $operator2 = User::updateOrCreate(
            ['email' => 'operator2@antri.local'],
            [
                'name' => 'Siti Rahma (Operator 2)',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'assigned_counter_id' => 3,
            ]
        );

        // 2. Services
        $serviceA = Service::updateOrCreate(
            ['code' => 'A'],
            [
                'name' => 'Layanan Informasi & Administrasi',
                'prefix' => 'A',
                'description' => 'Pengurusan berkas administrasi dan informasi umum',
                'estimated_time_minutes' => 5,
                'color' => '#2563eb',
                'icon' => 'document-text',
                'is_active' => true,
            ]
        );

        $serviceB = Service::updateOrCreate(
            ['code' => 'B'],
            [
                'name' => 'Layanan Kasir & Pembayaran',
                'prefix' => 'B',
                'description' => 'Pembayaran tagihan, transaksi kasir, dan penerimaan pembayaran',
                'estimated_time_minutes' => 4,
                'color' => '#059669',
                'icon' => 'credit-card',
                'is_active' => true,
            ]
        );

        $serviceC = Service::updateOrCreate(
            ['code' => 'C'],
            [
                'name' => 'Layanan Prioritas & Disabilitas',
                'prefix' => 'C',
                'description' => 'Khusus lansia di atas 60 tahun, ibu hamil, dan penyandang disabilitas',
                'estimated_time_minutes' => 7,
                'color' => '#d97706',
                'icon' => 'heart',
                'is_active' => true,
            ]
        );

        $serviceD = Service::updateOrCreate(
            ['code' => 'D'],
            [
                'name' => 'Customer Service & Konsultasi',
                'prefix' => 'D',
                'description' => 'Konsultasi produk perbankan, aduan layanan, dan penerbitan kartu baru',
                'estimated_time_minutes' => 10,
                'color' => '#7c3aed',
                'icon' => 'chat-bubble-left-right',
                'is_active' => true,
            ]
        );

        // 3. Counters
        Counter::updateOrCreate(
            ['number' => 1],
            [
                'name' => 'Loket 1',
                'service_id' => $serviceA->id,
                'current_operator_id' => $operator1->id,
                'status' => 'active',
            ]
        );

        Counter::updateOrCreate(
            ['number' => 2],
            [
                'name' => 'Loket 2',
                'service_id' => $serviceA->id,
                'current_operator_id' => null,
                'status' => 'closed',
            ]
        );

        Counter::updateOrCreate(
            ['number' => 3],
            [
                'name' => 'Loket 3',
                'service_id' => $serviceB->id,
                'current_operator_id' => $operator2->id,
                'status' => 'active',
            ]
        );

        Counter::updateOrCreate(
            ['number' => 4],
            [
                'name' => 'Loket 4',
                'service_id' => $serviceC->id,
                'current_operator_id' => null,
                'status' => 'closed',
            ]
        );

        // 4. App Settings
        $settings = [
            'app_name' => 'Sistem Antrian Terpadu',
            'primary_color' => '#2563eb',
            'secondary_color' => '#06b6d4',
            'logo_url' => '',
            'marquee_text' => 'Selamat datang di Kantor Layanan Terpadu. Silakan ambil nomor antrean pada layar Kiosk dan perhatikan nomor panggilan di TV Monitor.',
            'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&mute=1&loop=1',
            'voice_rate' => '0.9',
            'voice_pitch' => '1.0',
            'voice_lang' => 'id-ID',
        ];

        foreach ($settings as $key => $value) {
            AppSetting::setValue($key, $value);
        }
    }
}
