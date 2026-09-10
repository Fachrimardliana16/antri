# Changelog - Dynamic Announcements & Settings UI

## #3 - Sidebar Info Dinamis ✅

### Database Structure

**Table: `announcements`**
- `id` - Primary key
- `title` - Judul pengumuman (max 100 chars)
- `content` - Isi pengumuman (max 500 chars)
- `type` - Tipe: info, warning, notice
- `icon_color` - Warna border: blue, green, amber, red, purple
- `order` - Urutan tampil (int)
- `is_active` - Status aktif/nonaktif (boolean)
- `timestamps`

### Backend

**Model: `app/Models/Announcement.php`**
- Scope `active()` - Filter hanya yang aktif
- Scope `ordered()` - Sort by order ASC
- Fillable fields: title, content, type, icon_color, order, is_active

**Livewire: `app/Livewire/Admin/Announcements.php`**
- CRUD lengkap untuk announcements
- Modal create/edit
- Toggle active/inactive
- Delete dengan confirmation
- Real-time update list

### Frontend

**Admin UI: `/admin/announcements`**
- Table list semua announcements
- Button "Tambah Pengumuman"
- Edit/Delete actions per row
- Toggle active status inline
- Modal form dengan fields:
  - Title (text input)
  - Content (textarea)
  - Icon Color (radio buttons dengan preview warna)
  - Order (number input)
  - Active status (checkbox)

**TV Monitor: `livewire/display/tv-monitor.blade.php`**
- Sidebar info sekarang dinamis dari database
- Query: `Announcement::active()->ordered()->get()`
- Border color dinamis sesuai `icon_color`
- Fallback ke 3 default announcements jika kosong

### Seeder

**`database/seeders/AnnouncementSeeder.php`**
- 3 default announcements:
  1. Perhatian (blue)
  2. Informasi (green)
  3. Catatan (amber)

### Migration

Run migration untuk create table:
```bash
php artisan migrate
```

Run seeder untuk default data:
```bash
php artisan db:seed --class=AnnouncementSeeder
```

---

## #4 - Running Text & Settings UI Enhancement ✅

### Admin Settings Enhanced

**Livewire: `app/Livewire/Admin/Settings.php`**

Ditambahkan fields baru:
- `logoUrl` - URL logo (file atau external URL)
- `kioskSubtitle` - Subtitle halaman kiosk
- `tvSubtitle` - Subtitle halaman TV
- `marqueeText` - Running text di footer TV
- `videoUrl` - YouTube embed atau local video path

**Admin UI: `/admin/settings`**
Form lengkap untuk edit:
- Branding (app_name, logo_url, subtitles)
- Colors (primary, secondary)
- Running Text (marquee_text)
- Video (video_url dengan note support YouTube & local)
- Voice Settings (rate, pitch, lang)

### Cara Menggunakan

#### 1. Upload Logo

**Via URL eksternal:**
```
https://example.com/logo.png
```

**Via local storage:**
1. Upload file ke `public/storage/logos/logo.png`
2. Isi field Logo URL: `/storage/logos/logo.png`

#### 2. Edit Running Text

Admin → Settings → Running Text:
```
Selamat datang • Jam operasional 08:00-16:00 • Terima kasih
```

Note: Gunakan `•` (bullet) sebagai separator antar pesan

#### 3. Ganti Video

**YouTube:**
```
https://www.youtube.com/embed/VIDEO_ID?autoplay=1&mute=1&loop=1
```

**Video Lokal:**
1. Upload video ke `public/storage/videos/promo.mp4`
2. Isi field Video URL: `/storage/videos/promo.mp4`

Format support: MP4, WebM

---

## Route Admin

```php
// Admin menu (role: admin & super_admin)
/admin/announcements   → Kelola pengumuman sidebar TV
/admin/settings        → Kelola branding, running text, video

// Super Admin only
/admin/settings        → Full settings access
```

---

## Testing

### Test Announcements

1. Login sebagai admin: `http://localhost:8000/admin/announcements`
2. Klik "Tambah Pengumuman"
3. Isi form:
   - Title: "Promo Hari Ini"
   - Content: "Diskon 50% untuk layanan hari ini!"
   - Icon Color: Red
   - Order: 0 (tampil paling atas)
   - Active: ✓
4. Save
5. Buka TV monitor: `http://localhost:8000/tv`
6. Check sidebar kiri → "Promo Hari Ini" muncul paling atas dengan border merah

### Test Running Text

1. Login sebagai admin: `http://localhost:8000/admin/settings`
2. Edit Running Text:
   ```
   Pelayanan cepat & ramah • Kami tunggu kedatangan Anda • Terima kasih
   ```
3. Save
4. Buka TV: `http://localhost:8000/tv`
5. Check footer → running text berjalan dengan text baru

### Test Video Lokal

1. Upload video.mp4 ke `public/storage/videos/`
2. Admin → Settings → Video URL: `/storage/videos/video.mp4`
3. Save
4. TV monitor akan auto-detect dan pakai `<video>` tag (bukan iframe)
5. Video auto-loop, auto-muted

---

## Database Structure Summary

### Existing Tables (Enhanced)
- `app_settings` - Ditambah keys: logo_url, kiosk_subtitle, tv_subtitle

### New Tables
- `announcements` - Info dinamis di sidebar TV

---

## Migration Commands

```bash
# Run migration
php artisan migrate

# Seed default announcements
php artisan db:seed --class=AnnouncementSeeder

# Or seed all
php artisan db:seed

# Clear cache setelah update settings
php artisan cache:clear
```

---

## UI Screenshots Description

### Admin Announcements Page
- Header: "Pengumuman TV Monitor" + button "Tambah Pengumuman"
- Table columns: Order, Judul, Konten, Warna (badge), Status (toggle), Aksi (edit/delete)
- Modal form: Fields lengkap dengan color picker visual

### Admin Settings Page  
- Sections:
  1. Branding (app name, logo, subtitles)
  2. Theme Colors (primary, secondary)
  3. Running Text (textarea)
  4. Video (URL input dengan note)
  5. Voice Settings (rate, pitch, lang)
- Save button dengan flash message success

### TV Monitor
- Sidebar kiri: Card-card pengumuman dengan border color dinamis
- Footer: Running text berjalan smooth dengan badge "INFO"

---

## Security & Validation

### Announcements
- Title: max 100 chars
- Content: max 500 chars
- icon_color: enum validation (blue, green, amber, red, purple)
- order: integer, min 0
- Only admin/super_admin can manage

### Settings
- All fields: sanitized input
- URL fields: max 500 chars
- Color fields: hex validation
- Voice settings: numeric validation

---

## Future Enhancements (Optional)

1. **Rich Text Editor untuk Content**
   - Support bold, italic, links
   - Character counter

2. **Schedule Announcements**
   - start_date, end_date fields
   - Auto active/inactive berdasarkan waktu

3. **Announcement Categories**
   - Group by type: urgent, info, promo
   - Filter di admin UI

4. **Preview Mode**
   - Button "Preview" di admin
   - Show sidebar preview sebelum save

5. **Multi-Language Support**
   - Running text per bahasa
   - Announcements translation

6. **Analytics**
   - Track berapa kali announcement ditampilkan
   - Most viewed announcements
