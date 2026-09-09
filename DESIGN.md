# System Architecture & Design Document (DESIGN.md)

## 1. Arsitektur High-Level (TALL Stack)
- **Framework Utama:** Laravel 11+
- **Frontend / Reactivity:** Livewire 3 + Alpine.js (Tidak menggunakan Filament, murni kustom komponen *dashboard*).
- **Styling:** Tailwind CSS.
- **Database:** SQLite (File `database/database.sqlite` untuk kemudahan *setup*, dengan *schema migration* bawaan Laravel yang siap di-*switch* ke MySQL/PostgreSQL di masa depan).
- **Real-Time Engine:** **Laravel Reverb** (Native WebSocket server dari Laravel) + Laravel Echo di sisi *client*.

## 2. Dynamic Theming Implementation (Livewire Way)
Mekanisme ubah warna dinamis menggunakan ekosistem Laravel:
1. **Database:** Tabel `app_settings` (SQLite) menyimpan nilai konfigurasi (misal: `primary_color: '#3498db'`).
2. **AppServiceProvider:** Menggunakan *View Composer* untuk me- *load* pengaturan ini dari *cache* secara otomatis ke *layout* utama Blade (`app.blade.php`).
3. **Blade Layout:** Menyuntikkan nilai *setting* ke dalam tag `<style>` sebagai *CSS Variables*:
   ```html
   <style>
     :root { --primary: {{ $settings->primary_color }}; }
   </style>