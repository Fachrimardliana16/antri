# Panduan Pengaturan Sistem Antrian

## Setting yang Tersedia

Semua setting disimpan di tabel `app_settings` dengan struktur:
- `key` - Nama setting
- `value` - Nilai setting

## Branding & Text

### Logo
```sql
-- Upload logo ke public/storage/logos/logo.png dulu, lalu:
INSERT INTO app_settings (key, value, created_at, updated_at) 
VALUES ('logo_url', '/storage/logos/logo.png', NOW(), NOW())
ON DUPLICATE KEY UPDATE value = '/storage/logos/logo.png', updated_at = NOW();
```

Atau gunakan URL eksternal:
```sql
UPDATE app_settings SET value = 'https://example.com/logo.png' WHERE key = 'logo_url';
```

### Nama Aplikasi
```sql
UPDATE app_settings SET value = 'PMI Kota Jakarta' WHERE key = 'app_name';
```

### Subtitle Kiosk
```sql
UPDATE app_settings SET value = 'Ambil Tiket Donor Darah' WHERE key = 'kiosk_subtitle';
```

### Subtitle TV Monitor
```sql
UPDATE app_settings SET value = 'Display Panggilan Donor' WHERE key = 'tv_subtitle';
```

### Running Text
```sql
UPDATE app_settings 
SET value = 'Selamat datang di PMI Jakarta • Jam operasional 08:00-20:00 • Donor darah menyelamatkan nyawa' 
WHERE key = 'marquee_text';
```

## Video

### YouTube Video
```sql
UPDATE app_settings 
SET value = 'https://www.youtube.com/embed/VIDEO_ID?autoplay=1&mute=1&loop=1' 
WHERE key = 'video_url';
```

### Video Lokal
1. Upload video ke `public/storage/videos/promo.mp4`
2. Update setting:
```sql
UPDATE app_settings 
SET value = '/storage/videos/promo.mp4' 
WHERE key = 'video_url';
```

Format yang didukung: MP4, WebM

## Warna Tema

### Primary Color (Biru)
```sql
UPDATE app_settings SET value = '#2563eb' WHERE key = 'primary_color';
```

### Secondary Color (Cyan)
```sql
UPDATE app_settings SET value = '#06b6d4' WHERE key = 'secondary_color';
```

## Voice/Audio

### Rate (Kecepatan bicara: 0.5 - 2.0)
```sql
UPDATE app_settings SET value = '0.9' WHERE key = 'voice_rate';
```

### Pitch (Tinggi suara: 0.0 - 2.0)
```sql
UPDATE app_settings SET value = '1.0' WHERE key = 'voice_pitch';
```

### Language
```sql
UPDATE app_settings SET value = 'id-ID' WHERE key = 'voice_lang';
```

## Clear Cache

Setelah mengubah setting, clear cache Laravel:
```bash
php artisan cache:clear
```

Atau panggil di code:
```php
\Illuminate\Support\Facades\Cache::forget('all_app_settings');
```

## Contoh Preset

### PMI (Palang Merah Indonesia)
```sql
UPDATE app_settings SET value = 'PMI Kota Jakarta' WHERE key = 'app_name';
UPDATE app_settings SET value = 'Ambil Tiket Donor Darah' WHERE key = 'kiosk_subtitle';
UPDATE app_settings SET value = 'Display Panggilan Donor' WHERE key = 'tv_subtitle';
UPDATE app_settings SET value = '/storage/logos/pmi-logo.png' WHERE key = 'logo_url';
UPDATE app_settings SET value = 'Selamat datang di PMI Jakarta • Donor darah Anda menyelamatkan nyawa • Jam operasional 08:00-20:00' WHERE key = 'marquee_text';
```

### Bank (Contoh)
```sql
UPDATE app_settings SET value = 'Bank Sejahtera' WHERE key = 'app_name';
UPDATE app_settings SET value = 'Ambil Nomor Antrian' WHERE key = 'kiosk_subtitle';
UPDATE app_settings SET value = 'Monitor Customer Service' WHERE key = 'tv_subtitle';
UPDATE app_settings SET value = '/storage/logos/bank-logo.png' WHERE key = 'logo_url';
UPDATE app_settings SET value = 'Selamat datang di Bank Sejahtera • Layanan Senin-Jumat 08:00-16:00 • Sabtu 08:00-12:00' WHERE key = 'marquee_text';
```

### Rumah Sakit
```sql
UPDATE app_settings SET value = 'RS Sehat Sentosa' WHERE key = 'app_name';
UPDATE app_settings SET value = 'Ambil Nomor Antrian Poliklinik' WHERE key = 'kiosk_subtitle';
UPDATE app_settings SET value = 'Monitor Panggilan Pasien' WHERE key = 'tv_subtitle';
UPDATE app_settings SET value = '/storage/logos/rs-logo.png' WHERE key = 'logo_url';
UPDATE app_settings SET value = 'Selamat datang di RS Sehat Sentosa • Layanan 24 jam • Harap membawa kartu identitas' WHERE key = 'marquee_text';
```
