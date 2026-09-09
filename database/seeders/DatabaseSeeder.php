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
     * Seed the application's database with clean institutional defaults.
     */
    public function run(): void
    {
        // 1. Users (Buat user tanpa assigned_counter_id terlebih dahulu)
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
                'name' => 'Supervisor Pelayanan',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        $operator1 = User::updateOrCreate(
            ['email' => 'operator1@antri.local'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]
        );

        $operator2 = User::updateOrCreate(
            ['email' => 'operator2@antri.local'],
            [
                'name' => 'Siti Rahma',
                'password' => Hash::make('password'),
                'role' => 'operator',
            ]
        );

        // 2. Services (Government Institutional Services)
        $serviceA = Service::updateOrCreate(
            ['code' => 'A'],
            [
                'name' => 'Layanan Kependudukan & Catatan Sipil',
                'prefix' => 'A',
                'description' => 'Pengurusan KTP-el, Kartu Keluarga, Akta Kelahiran, dan Surat Pindah',
                'estimated_time_minutes' => 5,
                'color' => '#1e3a8a',
                'icon' => 'identification',
                'is_active' => true,
            ]
        );

        $serviceB = Service::updateOrCreate(
            ['code' => 'B'],
            [
                'name' => 'Layanan Perizinan Terpadu (PTSP)',
                'prefix' => 'B',
                'description' => 'Permohonan izin usaha, persetujuan bangunan, dan sertifikasi lingkungan',
                'estimated_time_minutes' => 7,
                'color' => '#0f766e',
                'icon' => 'document-text',
                'is_active' => true,
            ]
        );

        $serviceC = Service::updateOrCreate(
            ['code' => 'C'],
            [
                'name' => 'Layanan Pajak & Pendapatan Daerah',
                'prefix' => 'C',
                'description' => 'Pembayaran PBB-P2, BPHTB, Pajak Restoran, dan Reklame',
                'estimated_time_minutes' => 4,
                'color' => '#0369a1',
                'icon' => 'credit-card',
                'is_active' => true,
            ]
        );

        $serviceD = Service::updateOrCreate(
            ['code' => 'D'],
            [
                'name' => 'Layanan Prioritas (Lansia & Disabilitas)',
                'prefix' => 'D',
                'description' => 'Khusus warga lansia di atas 60 tahun, ibu hamil/menyusui, dan penyandang disabilitas',
                'estimated_time_minutes' => 6,
                'color' => '#b45309',
                'icon' => 'heart',
                'is_active' => true,
            ]
        );

        // 3. Counters
        $counter1 = Counter::updateOrCreate(
            ['number' => 1],
            [
                'name' => 'Loket 01',
                'service_id' => $serviceA->id,
                'current_operator_id' => $operator1->id,
                'status' => 'active',
            ]
        );

        $counter2 = Counter::updateOrCreate(
            ['number' => 2],
            [
                'name' => 'Loket 02',
                'service_id' => $serviceA->id,
                'current_operator_id' => null,
                'status' => 'closed',
            ]
        );

        $counter3 = Counter::updateOrCreate(
            ['number' => 3],
            [
                'name' => 'Loket 03',
                'service_id' => $serviceB->id,
                'current_operator_id' => $operator2->id,
                'status' => 'active',
            ]
        );

        $counter4 = Counter::updateOrCreate(
            ['number' => 4],
            [
                'name' => 'Loket 04',
                'service_id' => $serviceD->id,
                'current_operator_id' => null,
                'status' => 'closed',
            ]
        );

        // 4. Update assigned_counter_id pada Operator setelah Counter berhasil dibuat
        $operator1->update(['assigned_counter_id' => $counter1->id]);
        $operator2->update(['assigned_counter_id' => $counter3->id]);

        // 5. Institutional App Settings
        $settings = [
            'app_name' => 'MAL PELAYANAN PUBLIK',
            'primary_color' => '#1e3a8a',
            'secondary_color' => '#0284c7',
            'logo_url' => '',
            'marquee_text' => 'Selamat datang di Mal Pelayanan Publik. Silakan ambil nomor antrean pada mesin Kiosk dan perhatikan panggilan pada Layar Informasi TV.',
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
