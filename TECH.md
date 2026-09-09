# Technical Specifications (TECH.md)

## 1. Tech Stack (TALL Stack)
Aplikasi ini dibangun menggunakan arsitektur monolitik modern yang berfokus pada kecepatan *development* dan reaktivitas *real-time* tanpa perlu memisahkan *repository* API dan SPA.

*   **Framework Utama:** Laravel 11.x (PHP 8.2+)
*   **Reactivity Engine:** Livewire 3 (Komunikasi *server-client* reaktif)
*   **Client-Side Interactivity:** Alpine.js (Menangani DOM, *Text-to-Speech*, dan akses periferal/WebUSB)
*   **Styling:** Tailwind CSS (Kustomisasi UI penuh tanpa *package* admin seperti Filament)
*   **Database:** SQLite (Default, file-based). Mudah di- *setup* untuk Kiosk lokal, namun *schema* dijamin kompatibel jika ingin migrasi ke PostgreSQL/MySQL (menggunakan standar Eloquent).
*   **Real-Time / WebSocket:** Laravel Reverb (Native PHP WebSocket server) + Laravel Echo.

## 2. Persyaratan Sistem (System Requirements)
Untuk menjalankan aplikasi ini di *server* lokal (PC Kiosk) maupun VPS, diperlukan lingkungan berikut:
*   PHP >= 8.2 (dengan ekstensi: `pdo_sqlite`, `curl`, `mbstring`, `xml`, `zip`, `pcntl`).
*   Composer v2.x
*   Node.js (v18+) & NPM (untuk *compile assets* Tailwind dan Echo).
*   OS: Linux (Ubuntu/Debian) direkomendasikan untuk stabilitas *background worker*, atau Windows (via WSL2 / Laragon untuk *development*).

## 3. Arsitektur Modul & Komponen Livewire
Alih-alih menggunakan *controller* klasik, aplikasi ini digerakkan oleh komponen Livewire.

*   **`App\Livewire\Kiosk\TakeTicket`**
    *   Menangani logika pengunjung memilih layanan.
    *   Mengecek status ketersediaan loket secara *real-time*.
    *   Men-*trigger* Alpine.js untuk mengeksekusi fungsi pencetakan (Silent Print).
*   **`App\Livewire\Display\TvMonitor`**
    *   Komponen *read-only* yang mendengarkan *event* WebSocket dari Reverb.
    *   Merender grid layanan aktif.
    *   Menerima *payload* nomor antrian untuk men-*trigger* Web Speech API (TTS) via Alpine.
*   **`App\Livewire\Operator\Dashboard`**
    *   Antarmuka kontrol bagi *User Biasa*.
    *   Menjalankan fungsi `next()`, `recall()`, `skip()`, dan mengubah state `status` di tabel SQLite.
    *   Men-*dispatch event* `QueueCalled` ke Reverb.
*   **`App\Livewire\Admin\Settings`**
    *   Menyimpan perubahan warna UI (Primary/Secondary) ke dalam *database*.
    *   Mengosongkan *cache view* dan mengirim *broadcast event* agar seluruh layar Kiosk dan TV berubah warna secara instan.

## 4. Konfigurasi Background Services (Proses Daemon)
Aplikasi antrian *real-time* membutuhkan beberapa proses yang harus berjalan terus-menerus di latar belakang (gunakan Supervisor di Linux atau NSSM di Windows):

1.  **Web Server:** Nginx/Apache untuk menyajikan aplikasi Laravel.
2.  **WebSocket Server:** 
    Perintah: `php artisan reverb:start`
    Menangani koneksi soket TCP untuk *broadcast* panggilan antrian ke TV dan Kiosk.
3.  **Queue Worker (Opsional namun disarankan):** 
    Perintah: `php artisan queue:work`
    Jika proses mencetak tiket diserahkan ke sistem *queue* Laravel agar UI Kiosk tidak *lag/freeze* saat antrian sangat padat.
4.  **Task Scheduler:** 
    Perintah: `php artisan schedule:run`
    Berjalan setiap menit via Cron. Bertugas menjalankan fungsi *reset* nomor antrian kembali ke nol setiap pukul 00:00 (tengah malam).

## 5. Security & Authentication Guard
*   Autentikasi menggunakan bawaan Laravel (Session-based).
*   Pembatasan akses menggunakan **Laravel Gates/Policies** atau *Middleware* kustom (`CheckRole::class`).
*   Rute TV Monitor dan Kiosk bersifat *public* (tanpa otentikasi login), namun dilindungi oleh perlindungan CSRF bawaan Laravel dan rute Kiosk dibatasi IP (*IP Whitelisting*) agar hanya jaringan lokal yang bisa mencetak tiket.