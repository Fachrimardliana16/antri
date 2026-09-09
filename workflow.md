# Workflow: Analisis - Eksekusi - Test

## 1. Fase Analisis

### 1.1 Pemahaman Kebutuhan
- Baca dokumen PRD.md, SRS.md, dan TECH.md secara menyeluruh
- Identifikasi fitur/fungsionalitas yang akan dikerjakan
- Catat asumsi dan pertanyaan yang perlu diklarifikasi
- Tentukan kriteria sukses yang dapat diukur

### 1.2 Perancangan Teknis
- Tinjau struktur kode yang ada (Laravel, Livewire, komponen)
- Identifikasi file yang perlu dimodifikasi atau ditambahkan
- Tentukan pendekatan implementasi yang sesuai dengan arsitektur TALL Stack
- Pertimbangkan dampak pada real-time engine (Laravel Reverb/Laravel Echo)

### 1.3 Persiapan
- Buat cabang git baru untuk pekerjaan: `git checkout -b fitur/[nama-fitur]`
- Pastikan environment development sudah siap (Laravel, Node.js, Composer)
- Jalankan migrasi database jika diperlukan: `php artisan migrate`

## 2. Fase Eksekusi

### 2.1 Implementasi Inkremental
- Ikuti prinsip "Surgical Changes" dari CLAUDE.md
- Fokus pada minimum code yang diperlukan
- Jangan menambahkan abstraksi atau konfigurasi yang tidak diminta
- Sesuaikan gaya kode yang ada (komentar, indentasi, penamaan)

### 2.2 Komponen Spesifik TALL Stack
- **Livewire Components**: Pastikan menggunakan properti publik dan metod yang sesuai
- **Alpine.js**: Gunakan untuk interaksi client-side yang kompleks (WebUSB, TTS, print)
- **Tailwind CSS**: Manfaatkan utility classes untuk styling
- **Laravel Reverb/Echo**: Pastikan event broadcasting dan listener dikonfigurasi dengan benar
- **Blade Layouts**: Manfaatkan view composer untuk variabel CSS (dynamic theming)

### 2.3 Praktik Keamanan
- Terapkan Laravel Gates/Policies atau middleware kustom untuk otorisasi
- Pastikan CSRF protection aktif
- Terapkan rate limiting untuk endpoint yang sensitif
- Validasi dan sanitasi semua input pengguna

## 3. Fase Test

### 3.1 Pengujian Unit
- Tuliskan test untuk kelas PHP (Eloquent models, services, dll)
- Gunakan PHPUnit yang sudah terintegrasi dengan Laravel
- Fokus pada logika bisnis dan edge cases

### 3.2 Pengujian Fitur
- Tuliskan test Livewire untuk menguji interaksi komponen
- Gunakan Laravel Dusk jika diperlukan untuk pengujian browser
- Verifikasi integrasi dengan WebSocket/Laravel Echo

### 3.3 Pengujian Manual
- Uji di browser yang berbeda (Chrome, Firefox, Safari)
- Uji di mode kiosk jika memungkinkan
- Verifikasi fungsi real-time antara multiple klien (Kiosk, Operator, TV Monitor)
- Test fitur cetak silent dan WebUSB

### 3.4 Verifikasi Berdasarkan Kebutuhan
- Kembalikan ke dokumen PRD.md dan SRS.md
- Verifikasi setiap REQ-F dan REQ-NF yang terkait dengan fitur yang dikerjakan
- Pastikan tidak ada regresi pada fungsionalitas yang sudah ada

## 4. Siklus Perbaikan

### 4.1 Review Kode
- Lakukan self-review sebelum commit
- Periksa apakah semua perubahan terkait langsung dengan permintaan pengguna
- Hapus kode/variabel yang tidak digunakan yang hasil dari perubahan Anda
- Jangan menghapus kode mati yang ada kecuali diminta

### 4.2 Integrasi dan Deploy
- Commit dengan pesan yang jelas dan deskriptif
- Tarik perubahan terbaru dari cabang utama sebelum merge
- Jalankan seluruh test suite sebelum merge
- Setelah merge, verifikasi aplikasi masih berjalan dengan baik

### 4.3 Dokumentasi
- Perbarui dokumentasi teknis jika diperlukan
- Catat keputusan arsitektur yang signifikan
- Update contoh penggunaan jika ada perubahan API internal