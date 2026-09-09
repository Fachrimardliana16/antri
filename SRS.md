# Software Requirements Specification (SRS.md)

## 1. Pendahuluan
### 1.1 Tujuan Dokumen
Dokumen *Software Requirements Specification* (SRS) ini bertujuan untuk mendefinisikan spesifikasi kebutuhan perangkat lunak untuk Sistem Antrian Terpadu berbasis ekosistem Laravel (TALL Stack). Dokumen ini menjadi acuan utama bagi *developer*, QA, dan pemangku kepentingan selama fase pengembangan.

### 1.2 Ruang Lingkup Sistem
Sistem ini mengotomatisasi alur manajemen antrian mulai dari pengambilan tiket (Kiosk), pemanggilan nomor (Operator), hingga notifikasi visual dan audio (TV Monitor). Sistem dilengkapi fitur pelaporan performa, kustomisasi tema visual, dan pelacakan status secara *real-time* via WebSocket.

## 2. Deskripsi Keseluruhan
### 2.1 Perspektif Produk
Aplikasi ini berjalan sebagai aplikasi web monolitik yang beroperasi pada *local area network* (LAN) instansi atau diakses melalui *cloud*. Sistem berinteraksi secara langsung dengan periferal perangkat keras (layar sentuh TV, printer thermal) dan memanfaatkan Laravel Reverb untuk sinkronisasi data antar klien secara instan.

### 2.2 Karakteristik Pengguna (User Classes)
1. **Super Admin:** Membutuhkan akses ke pengaturan *root* aplikasi, lisensi, dan utilitas kustomisasi UI (*Dynamic Theming*).
2. **Admin:** Membutuhkan antarmuka pengelolaan data master (Loket, Layanan, Operator) dan akses penuh ke laporan analitik (SLA).
3. **Operator (User Biasa):** Membutuhkan antarmuka pemanggilan antrian yang sangat cepat, responsif, dan bebas *lag*.
4. **Pelanggan:** Membutuhkan antarmuka Kiosk yang *self-explanatory* (mudah dipahami tanpa bantuan) dan fitur pelacakan (*QR Tracker*).

### 2.3 Lingkungan Operasi (Operating Environment)
*   **Server:** Nginx/Apache, PHP 8.2+, SQLite (dengan dukungan PDO), Laravel Reverb (Port 8080/WebSocket).
*   **Kiosk Client:** OS Windows/Linux, Google Chrome (dijalankan dalam *Kiosk Mode* dengan *flag* `--kiosk-printing`), layar sentuh minimal 10 inci.
*   **TV Client:** Smart TV atau Mini PC (berbasis Chrome/Edge) dengan koneksi audio ke *speaker* ruangan.

## 3. Kebutuhan Fungsional (System Features)

### 3.1 Modul Kiosk (Layar Pengambilan Nomor)
*   **REQ-F-01:** Sistem harus menampilkan tombol layanan yang tersedia secara dinamis berdasarkan status aktif loket (dikontrol via Livewire *polling* atau *event listener*).
*   **REQ-F-02:** Sistem harus mencetak struk antrian secara *silent* (tanpa memunculkan OS Print Dialog) saat tombol layanan ditekan.
*   **REQ-F-03:** Sistem harus men- *generate* QR Code unik pada setiap struk fisik yang mengarah ke URL *live tracking* pengunjung.

### 3.2 Modul Operator (Pemanggilan Antrian)
*   **REQ-F-04:** Sistem harus menyediakan tombol aksi: `Next` (Panggil Nomor Berikutnya), `Recall` (Panggil Ulang), `Skip` (Lewati), dan `Finish` (Selesai).
*   **REQ-F-05:** Sistem harus men-*dispatch* *event* WebSocket (`QueueCalled`) seketika setelah tombol `Next` atau `Recall` ditekan.
*   **REQ-F-06:** Operator harus dapat mengubah status loket mereka menjadi "Aktif", "Istirahat", atau "Tutup", yang akan secara otomatis mengunci/membuka fitur di Kiosk.

### 3.3 Modul TV Monitor (Display Publik)
*   **REQ-F-07:** Layar TV harus otomatis merender *grid* kotak loket sesuai jumlah loket yang statusnya "Aktif".
*   **REQ-F-08:** Sistem harus menerima *event* WebSocket dan memicu pemutaran suara melalui *Web Speech API* (Text-to-Speech) untuk membaca nomor antrian (contoh: "Nomor... A... satu... menuju... loket... dua").
*   **REQ-F-09:** Sistem harus menurunkan volume elemen video (`audio ducking`) secara otomatis saat *Text-to-Speech* sedang aktif.

### 3.4 Modul Admin & Konfigurasi
*   **REQ-F-10 (Dynamic Theming):** Sistem harus memungkinkan modifikasi `primary_color` dan `secondary_color` melalui antarmuka, yang akan langsung merender ulang variabel CSS di semua klien yang terhubung.
*   **REQ-F-11:** Sistem harus menyediakan laporan perhitungan *Average Wait Time* (Waktu Tunggu Rata-rata) dan *Service Time* (Waktu Layanan) berdasarkan data selisih *timestamp* di tabel `queue_logs`.

## 4. Kebutuhan Antarmuka Eksternal
*   **User Interfaces (UI):** Dibangun dengan Tailwind CSS murni. Desain harus *mobile-responsive* untuk *Dashboard* Admin/Operator, dan dioptimalkan untuk orientasi *Landscape* 16:9 pada Kiosk dan TV Monitor.
*   **Hardware Interfaces:** Sistem bergantung pada printer thermal (via USB/Network). Komunikasi di Kiosk ditangani melalui ekstensi WebUSB atau via parameter standar cetak browser Chrome.
*   **Software Interfaces:** Sistem menggunakan SQLite. Pengelolaan antarmuka *real-time* di- *handle* murni oleh Alpine.js yang berinteraksi dengan DOM berdasarkan *state* yang dikirimkan oleh Livewire.

## 5. Kebutuhan Non-Fungsional
*   **REQ-NF-01 (Kinerja):** Pembaruan layar di TV Monitor dan Kiosk saat ada *event* panggilan atau perubahan tema tidak boleh memiliki jeda (*latency*) lebih dari 1 detik sejak tombol di-klik oleh Operator.
*   **REQ-NF-02 (Ketersediaan):** Sistem harus mampu melanjutkan urutan nomor antrian yang tersimpan di basis data meskipun server mengalami *restart* paksa.
*   **REQ-NF-03 (Keamanan):** Semua *endpoint* dan aksi komponen Livewire yang berkaitan dengan operasional antrian dan konfigurasi harus dilindungi oleh otentikasi sesi (*stateful*) dan *Role-Based Access Control* (RBAC).
*   **REQ-NF-04 (Pemeliharaan):** Sistem harus menjalankan *cron job* harian pada pukul 00:00 untuk mereset *counter* urutan nomor kembali ke angka 0.