# Product Requirements Document (PRD) - Enterprise Edition

## 1. Executive Summary
**Nama Produk:** Sistem Antrian Terpadu (TALL Stack Edition)
**Visi:** Menyediakan platform manajemen antrian *real-time* yang cepat dikembangkan, *scalable*, dan dapat dikustomisasi (*white-label*) menggunakan ekosistem murni Laravel, cocok untuk instansi yang membutuhkan *deployment* cepat dan praktis.

## 2. Role-Based Access Control (RBAC) & Use Case
Sistem memisahkan otorisasi menjadi 3 tingkatan (Tier) pengguna utama (kustom UI tanpa package Filament):

1. **Super Admin (System Owner)**
   - Akses penuh ke seluruh konfigurasi sistem.
   - Mengelola *engine* tema (warna & logo) melalui *Custom Dashboard* Livewire.
   - Manajemen akun **Admin**.
2. **Admin (Manager/Supervisor)**
   - Mengelola data master: Layanan (Services), Loket (Counters), dan Akun **User Biasa** (Operator).
   - Mengelola teks berjalan (*Marquee*) dan URL Video TV.
   - Memantau *dashboard analytics* dan laporan performa pelayanan.
3. **User Biasa (Operator Loket)**
   - Akses spesifik ke antarmuka pemanggilan antrian.
   - Aksi: Panggil (Next), Panggil Ulang (Recall), Lewati (Skip), Selesai (Finish), dan Transfer.
4. **Pelanggan/Pengunjung (End-User)**
   - Mengambil tiket dari Kiosk. Memantau antrian secara *real-time* di TV Monitor atau via *smartphone* melalui fitur *QR Live Tracking*.

## 3. Fitur Utama & Kebutuhan Fungsional
### 3.1 Dynamic Theming Engine (Kustomisasi UI)
- Super Admin dapat mengubah `Primary Color`, `Secondary Color`, dan `Logo`. Perubahan langsung direfleksikan secara *real-time* ke semua *display* melalui mekanisme Laravel Blade dan *CSS Variables*.

### 3.2 Display Kiosk (Touchscreen)
- Tombol layanan cerdas (otomatis *disable* menggunakan *polling* atau *event listener* Livewire jika loket tutup).
- **Silent Printing:** Terintegrasi dengan WebUSB via Alpine.js atau Local Print Middleware untuk cetak struk tanpa *Print Dialog* browser.

### 3.3 Display Monitor (TV) & Smart Audio
- *Layout* dinamis berbasis CSS Grid yang di-*render* secara reaktif oleh Livewire.
- *Text-to-Speech* (TTS) via JavaScript (*Alpine.js*) yang memanggil antrian saat *event* *broadcast* diterima.

### 3.4 Custom Dashboard & Analytics
- Panel *admin* dibangun dari nol menggunakan komponen Livewire (bebas Filament) untuk fleksibilitas UI yang maksimal.