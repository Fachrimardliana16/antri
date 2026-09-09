 kan# Panduan Setup Printer Thermal

## 🖨️ Overview

Sistem Antrian Terpadu mendukung dua metode pencetakan:

1. **WebUSB API** - Koneksi langsung ke printer thermal USB (Chrome only)
2. **Browser Print API** - Fallback universal menggunakan `window.print()`

---

## ✅ Printer yang Didukung

### Thermal Printer (58mm atau 80mm)

**Tested & Recommended:**
- Epson TM-T82, TM-T88 Series
- Star Micronics TSP100, TSP650
- Xprinter XP-58, XP-80
- Citizen CT-S310II
- Bixolon SRP-350, SRP-275

**Kompatibilitas:**
- ESC/POS protocol support
- USB atau Network connectivity
- Auto-cutter (recommended)
- Paper width: 58mm atau 80mm

---

## 🔧 Setup Method 1: WebUSB (Direct Connection)

### Kebutuhan
- Google Chrome browser (versi 61+)
- Printer thermal dengan USB interface
- Sistem operasi: Windows 10+, macOS, atau Linux

### Langkah-langkah

#### 1. Hubungkan Printer

```bash
# Cek apakah printer terdeteksi (Linux)
lsusb

# Output akan menampilkan device seperti:
# Bus 001 Device 005: ID 04b8:0e15 Seiko Epson Corp. TM-T88V
```

#### 2. Install Driver (Opsional)

Untuk Windows/macOS, install driver dari vendor:
- **Epson**: https://download.epson-biz.com/
- **Star**: https://www.starmicronics.com/support/
- **Xprinter**: https://www.xprinter.net/download/

> **Note**: WebUSB tidak selalu memerlukan driver, tapi membantu untuk kompatibilitas.

#### 3. Konfigurasi Browser

Buka Kiosk dalam mode Chrome:

```bash
google-chrome --kiosk --kiosk-printing --disable-web-security \
  --user-data-dir=/tmp/chrome-kiosk \
  --app=http://localhost/kiosk
```

**Flag explanation:**
- `--kiosk`: Fullscreen tanpa UI
- `--kiosk-printing`: Silent print tanpa dialog
- `--disable-web-security`: Allow WebUSB access (use only in kiosk mode)
- `--user-data-dir`: Isolated profile untuk kiosk

#### 4. Grant WebUSB Permission

1. Buka halaman Kiosk
2. Ambil tiket pertama kali
3. Browser akan memunculkan dialog "Select a device"
4. Pilih printer thermal dari list
5. Klik "Connect"

**Permission akan tersimpan untuk session berikutnya.**

#### 5. Test Print

Ambil nomor antrian untuk test:
- Tiket harus langsung tercetak tanpa dialog
- Jika gagal, cek console browser (F12) untuk error

---

## 🔧 Setup Method 2: Browser Print API (Fallback)

### Kebutuhan
- Browser modern (Chrome, Firefox, Edge)
- Printer thermal sudah terpasang di sistem

### Langkah-langkah

#### 1. Install Printer Driver

Install driver resmi dari vendor printer.

**Windows:**
1. Download driver installer
2. Hubungkan printer via USB
3. Jalankan installer
4. Test print dari Notepad

**macOS:**
1. Download driver .dmg atau .pkg
2. Install driver
3. Tambah printer via System Preferences > Printers & Scanners

**Linux:**
```bash
# Install CUPS
sudo apt-get install cups

# Install driver (Epson example)
sudo apt-get install printer-driver-escpr

# Add printer
sudo lpadmin -p ThermalPrinter -E -v usb://path/to/printer -m escpr

# Set as default
sudo lpadmin -d ThermalPrinter
```

#### 2. Set Sebagai Default Printer

Pastikan thermal printer adalah default printer sistem:

**Windows:**
- Settings > Devices > Printers & Scanners
- Klik printer > "Set as default"

**macOS:**
- System Preferences > Printers & Scanners
- Pilih printer > klik "Default printer"

**Linux:**
```bash
lpadmin -d ThermalPrinter
```

#### 3. Konfigurasi Paper Size

Set ukuran kertas thermal:

**Windows:**
1. Printer Properties > Preferences
2. Paper/Output tab
3. Set paper size: Custom (58mm x 297mm atau 80mm x 297mm)
4. Save

**macOS:**
1. Print dialog > Show Details
2. Paper Size > Manage Custom Sizes
3. Tambah size 58mm atau 80mm width

**Linux (CUPS):**
```bash
# Edit printer options
lpadmin -p ThermalPrinter -o media=Custom.58x297mm
```

#### 4. Test dengan Kiosk Mode

```bash
# Chrome dengan auto-print
google-chrome --kiosk --kiosk-printing --app=http://localhost/kiosk
```

Tiket akan otomatis tercetak menggunakan printer default.

---

## 🎨 Customize Print Layout

### Edit Template Struk

File: `resources/js/printer.js`

```javascript
generateESCPOS(ticket) {
    // Customize ESC/POS commands here
    // Modify header, font size, alignment, etc.
}

generatePrintHTML(ticket) {
    // Customize HTML template here
    // Change layout, add logo, adjust spacing
}
```

### CSS Print Styling

File: `resources/css/app.css`

```css
@media print {
    @page {
        margin: 0;
        size: 58mm auto; /* Adjust width */
    }
    
    body {
        width: 58mm;
        font-family: 'Courier New', monospace;
    }
    
    /* Add custom styles */
}
```

---

## 🧪 Testing

### Test Print Functionality

1. **Manual Test:**
   - Buka `/kiosk`
   - Ambil nomor antrian
   - Verifikasi struk tercetak dengan benar

2. **Console Test:**
   ```javascript
   // Buka browser console (F12)
   window.AntriPrinter.printTicket({
       ticket_number: 'A001',
       service_name: 'Test Service',
       ahead_count: 5,
       estimated_wait: 10,
       created_at: new Date().toLocaleString(),
       qr_svg: '<svg>...</svg>',
       tracking_url: 'http://localhost/tracking/test'
   });
   ```

3. **Check Print Mode:**
   ```javascript
   console.log(window.AntriPrinter.printMode);
   // Output: 'webusb', 'kiosk', or 'browser'
   ```

---

## 🐛 Troubleshooting

### Issue: WebUSB Not Detected

**Symptoms:**
- Dialog "Select device" tidak muncul
- Console error: "WebUSB not supported"

**Solutions:**
1. Gunakan Google Chrome (bukan Chromium atau Firefox)
2. Check browser version: `chrome://version` (harus 61+)
3. Enable WebUSB flag: `chrome://flags/#enable-webusb`
4. Restart browser

### Issue: Permission Denied

**Symptoms:**
- Error: "Failed to open device"
- Access denied saat print

**Solutions:**

**Linux:**
```bash
# Add user to dialout group
sudo usermod -a -G dialout $USER

# Create udev rule for WebUSB
sudo nano /etc/udev/rules.d/99-webusb.rules

# Add line:
SUBSYSTEM=="usb", ATTR{idVendor}=="04b8", MODE="0666"

# Reload udev
sudo udevadm control --reload-rules
sudo udevadm trigger
```

**Windows:**
- Run Chrome as Administrator (first time only)
- Grant USB access when prompted

### Issue: Print Dialog Muncul

**Symptoms:**
- Dialog print OS tetap muncul
- Tidak auto-print

**Solutions:**
1. Pastikan menggunakan `--kiosk-printing` flag
2. Set printer sebagai default
3. Check browser policy:
   ```bash
   chrome://policy
   # Look for: PrintingEnabled = true
   ```

### Issue: Struk Terpotong atau Format Salah

**Symptoms:**
- Teks terpotong di kanan
- QR code tidak muncul
- Spasi aneh

**Solutions:**
1. Check paper width setting (58mm vs 80mm)
2. Adjust CSS `@page size` in `printer.js`
3. Check printer character per line:
   - 58mm = ~32 characters
   - 80mm = ~48 characters
4. Test different fonts (monospace recommended)

### Issue: Print Terlalu Lambat

**Symptoms:**
- Delay 3-5 detik sebelum print
- Queue menumpuk

**Solutions:**
1. Gunakan WebUSB (lebih cepat dari browser print)
2. Check printer connection (USB 2.0 vs 3.0)
3. Update printer firmware
4. Reduce QR code size in settings

---

## 📊 Performance Tips

### Optimize Print Speed

1. **Gunakan ESC/POS commands** (WebUSB) bukan HTML rendering
2. **Kurangi graphics** - QR code ukuran minimal
3. **Cache QR generation** jika memungkinkan
4. **Use print queue** untuk high traffic:

```php
// In Livewire component
Dispatch(new PrintTicketJob($ticket))->afterResponse();
```

### Multiple Printers

Untuk setup multi-kiosk:

1. **Assign printer per device:**
   ```javascript
   // In printer.js, check device ID
   const kioskId = localStorage.getItem('kiosk_id') || 'kiosk-1';
   const printerMap = {
       'kiosk-1': { vendorId: 0x04b8, productId: 0x0e15 },
       'kiosk-2': { vendorId: 0x04b8, productId: 0x0e16 },
   };
   ```

2. **Load balance** dengan queue system
3. **Monitor** printer status via admin dashboard

---

## 🔒 Security Considerations

### WebUSB Permissions

- WebUSB hanya bisa diakses dari **HTTPS** atau **localhost**
- Permission bersifat per-origin (domain)
- User harus explicitly grant access (tidak bisa auto-grant)

### Kiosk Mode Security

```bash
# Isolated Chrome profile
--user-data-dir=/tmp/chrome-kiosk-$(hostname)

# Disable unnecessary features
--disable-sync 
--disable-translate 
--disable-extensions

# Auto-cleanup on restart
rm -rf /tmp/chrome-kiosk-* # Add to startup script
```

---

## 📝 Checklist Setup

### Before Deployment

- [ ] Printer hardware terpasang dan menyala
- [ ] Driver terinstall (untuk browser print)
- [ ] Paper roll terpasang dengan benar
- [ ] Test print dari aplikasi lain (Notepad/TextEdit)
- [ ] Browser kiosk mode configured
- [ ] WebUSB permission granted (untuk Chrome)
- [ ] Default printer set correctly
- [ ] Paper size configured (58mm atau 80mm)
- [ ] Test print 10+ tickets berturut-turut
- [ ] Verify QR code scannable
- [ ] Check print speed (<2 detik per ticket)

### Post Deployment

- [ ] Monitor print error logs
- [ ] Check paper roll setiap hari
- [ ] Clean printer head setiap minggu
- [ ] Backup printer settings
- [ ] Document device-specific quirks

---

## 📞 Support

Untuk masalah teknis:
1. Check console log: F12 > Console
2. Check printer connection: Device Manager (Windows) / System Info (macOS) / `lsusb` (Linux)
3. Review error messages di `storage/logs/laravel.log`

---

**Last Updated**: 2026-09-09  
**Tested On**: Chrome 120+, Windows 10/11, Ubuntu 22.04, macOS Sonoma
</content>
