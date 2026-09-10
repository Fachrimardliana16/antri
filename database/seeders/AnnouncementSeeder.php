<?php

namespace Database\Seeders;

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    public function run(): void
    {
        $announcements = [
            [
                'title' => 'Perhatian',
                'content' => 'Harap perhatikan nomor antrian Anda di layar monitor',
                'type' => 'info',
                'icon_color' => 'blue',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Informasi',
                'content' => 'Pastikan datang saat nomor Anda dipanggil',
                'type' => 'info',
                'icon_color' => 'green',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Catatan',
                'content' => 'Pelayanan dimulai pukul 08:00 - 16:00',
                'type' => 'notice',
                'icon_color' => 'amber',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($announcements as $announcement) {
            Announcement::create($announcement);
        }
    }
}
