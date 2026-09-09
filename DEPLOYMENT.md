# Deployment Guide - Sistem Antrian Terpadu

**Version**: 1.0  
**Laravel**: 11.x  
**Stack**: TALL (Tailwind, Alpine, Livewire, Laravel)

---

## 📋 System Requirements

### Server Requirements
- **PHP**: 8.2 or higher
- **Extensions**: `pdo_sqlite`, `curl`, `mbstring`, `xml`, `zip`, `pcntl`, `fileinfo`
- **Composer**: 2.x
- **Node.js**: 18+ & NPM
- **Web Server**: Nginx atau Apache
- **OS**: Linux (Ubuntu/Debian recommended) atau Windows Server

### Optional (For Production)
- **Process Manager**: Supervisor (Linux) atau NSSM (Windows)
- **Reverse Proxy**: Nginx dengan SSL/TLS
- **Database**: SQLite (default) atau MySQL/PostgreSQL

---

## 🚀 Installation Steps

### 1. Clone & Install Dependencies

```bash
# Clone repository
git clone <repository-url> antri
cd antri

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install

# Build assets
npm run build
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Create SQLite database
touch database/database.sqlite
```

### 3. Configure Environment Variables

Edit `.env` file:

```env
APP_NAME="Mal Pelayanan Publik"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database/database.sqlite

# Broadcasting (Laravel Reverb)
BROADCAST_CONNECTION=reverb

# Reverb Configuration
REVERB_APP_ID=antri_app
REVERB_APP_KEY=antri_key
REVERB_APP_SECRET=antri_secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite (for frontend)
VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 4. Database Migration & Seeding

```bash
# Run migrations
php artisan migrate --force

# Seed initial data
php artisan db:seed --force
```

**Default Credentials:**
- Super Admin: `superadmin@antri.local` / `password`
- Admin: `admin@antri.local` / `password`
- Operator 1: `operator1@antri.local` / `password`
- Operator 2: `operator2@antri.local` / `password`

### 5. File Permissions

```bash
# Set proper permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## 🔧 Background Services Configuration

### Required Services

1. **Web Server** (Nginx/Apache)
2. **Laravel Reverb** (WebSocket Server)
3. **Laravel Queue Worker** (Optional but recommended)
4. **Laravel Scheduler** (Cron Job)

### Supervisor Configuration (Linux)

Create `/etc/supervisor/conf.d/antri.conf`:

```ini
[program:antri-reverb]
process_name=%(program_name)s
command=php /path/to/antri/artisan reverb:start
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/path/to/antri/storage/logs/reverb.log
stopwaitsecs=3600

[program:antri-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/antri/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/antri/storage/logs/queue.log
stopwaitsecs=3600
```

Reload Supervisor:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start antri-reverb:*
sudo supervisorctl start antri-queue:*
```

### NSSM Configuration (Windows)

```powershell
# Download NSSM from https://nssm.cc/download

# Install Reverb Service
nssm install AntriReverb "C:\php\php.exe" "C:\path\to\antri\artisan reverb:start"
nssm set AntriReverb AppDirectory "C:\path\to\antri"
nssm start AntriReverb

# Install Queue Service
nssm install AntriQueue "C:\php\php.exe" "C:\path\to\antri\artisan queue:work"
nssm set AntriQueue AppDirectory "C:\path\to\antri"
nssm start AntriQueue
```

---

## 🌐 Web Server Configuration

### Nginx Configuration

Create `/etc/nginx/sites-available/antri`:

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/antri/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# WebSocket Reverse Proxy for Reverb
server {
    listen 8080;
    server_name your-domain.com;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "upgrade";
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

Enable site:

```bash
sudo ln -s /etc/nginx/sites-available/antri /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Apache Configuration

Create `/etc/apache2/sites-available/antri.conf`:

```apache
<VirtualHost *:80>
    ServerName your-domain.com
    DocumentRoot /path/to/antri/public

    <Directory /path/to/antri/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/antri-error.log
    CustomLog ${APACHE_LOG_DIR}/antri-access.log combined
</VirtualHost>
```

Enable site and modules:

```bash
sudo a2enmod rewrite
sudo a2ensite antri
sudo systemctl reload apache2
```

---

## ⏰ Cron Job Configuration

Add to crontab (`crontab -e`):

```cron
# Laravel Scheduler (runs every minute)
* * * * * cd /path/to/antri && php artisan schedule:run >> /dev/null 2>&1
```

The scheduler will automatically run:
- **Daily Queue Reset** at 00:00 (midnight)

---

## 🖨️ Kiosk & Printer Setup

### Browser Configuration for Kiosk Mode

**Google Chrome (Recommended)**

```bash
# Linux
google-chrome --kiosk --kiosk-printing --app=http://your-domain.com/kiosk

# Windows
"C:\Program Files\Google\Chrome\Application\chrome.exe" --kiosk --kiosk-printing --app=http://your-domain.com/kiosk

# macOS
"/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" --kiosk --kiosk-printing --app=http://your-domain.com/kiosk
```

**Flags Explanation:**
- `--kiosk`: Fullscreen mode tanpa UI browser
- `--kiosk-printing`: Enable silent printing tanpa dialog
- `--app=URL`: Load URL sebagai aplikasi standalone

### Thermal Printer Setup

#### Option 1: WebUSB API (Chrome Only)

1. Hubungkan printer thermal via USB
2. Buka Kiosk page di Chrome
3. Klik tombol "Setup Printer" di settings
4. Pilih printer dari dialog USB
5. Grant permission

**Supported Printers:**
- Epson (Vendor ID: 0x04b8)
- Star Micronics (Vendor ID: 0x0519)
- Xprinter (Vendor ID: 0x154f)
- Generic ESC/POS compatible printers

#### Option 2: System Print (Fallback)

1. Install printer driver di OS
2. Set sebagai default printer
3. Browser akan auto-detect printer
4. Printing akan menggunakan `window.print()` API

### Kiosk Auto-start (Linux)

Create `~/.config/autostart/antri-kiosk.desktop`:

```ini
[Desktop Entry]
Type=Application
Name=Antri Kiosk
Exec=google-chrome --kiosk --kiosk-printing --app=http://localhost/kiosk
X-GNOME-Autostart-enabled=true
```

---

## 📺 TV Monitor Setup

### Display Configuration

**URL**: `http://your-domain.com/tv`

**Recommended:**
- Smart TV dengan browser built-in (WebOS, Tizen, Android TV)
- Mini PC (Intel NUC, Raspberry Pi) connected to TV via HDMI
- Chrome/Edge browser dalam fullscreen mode

**Browser Launch Command:**

```bash
# Fullscreen mode with auto-refresh
google-chrome --start-fullscreen --app=http://your-domain.com/tv
```

**Audio Requirements:**
- Speaker aktif terhubung ke TV/Mini PC
- Volume optimal: 70-80%
- Test audio dengan klik "Unlock Audio" banner di layar

---

## 🔒 Security & Network Configuration

### IP Whitelisting for Kiosk

Edit `app/Http/Middleware/CheckKioskIp.php` (create if not exists):

```php
public function handle($request, Closure $next)
{
    $allowedIps = [
        '192.168.1.0/24',  // Local network
        '10.0.0.0/8',      // Private network
    ];
    
    if (!in_array($request->ip(), $allowedIps)) {
        abort(403, 'Access Denied');
    }
    
    return $next($request);
}
```

Register in `routes/web.php`:

```php
Route::middleware(['kiosk.ip'])->group(function () {
    Route::get('/kiosk', TakeTicket::class)->name('kiosk.take-ticket');
});
```

### Firewall Configuration

```bash
# Allow HTTP/HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Allow Reverb WebSocket
sudo ufw allow 8080/tcp

# Enable firewall
sudo ufw enable
```

---

## 📊 Monitoring & Maintenance

### Log Files

- **Application**: `storage/logs/laravel.log`
- **Reverb**: `storage/logs/reverb.log`
- **Queue**: `storage/logs/queue.log`
- **Nginx**: `/var/log/nginx/antri-*.log`

### Health Check Commands

```bash
# Check all services status
sudo supervisorctl status

# View Reverb connections
php artisan reverb:connections

# Check queue status
php artisan queue:failed

# Clear cache
php artisan optimize:clear
```

### Database Backup

```bash
# Backup SQLite database
cp database/database.sqlite database/backups/database_$(date +%Y%m%d_%H%M%S).sqlite

# Automated daily backup (add to cron)
0 2 * * * cp /path/to/antri/database/database.sqlite /path/to/backups/database_$(date +\%Y\%m\%d).sqlite
```

---

## 🐛 Troubleshooting

### WebSocket Connection Failed

**Symptoms**: Real-time updates tidak berfungsi

**Solutions:**
1. Check Reverb service: `sudo supervisorctl status antri-reverb`
2. Check port 8080: `netstat -tulpn | grep 8080`
3. Verify `.env` credentials match between server and client
4. Check browser console for connection errors

### Silent Printing Not Working

**Solutions:**
1. Use Chrome browser (Firefox/Safari limited support)
2. Enable `--kiosk-printing` flag
3. Check printer connection and driver
4. Test with `window.print()` manually
5. Grant WebUSB permissions if using thermal printer

### Text-to-Speech Not Speaking

**Solutions:**
1. Click "Unlock Audio" banner (browser autoplay policy)
2. Check browser TTS support: `window.speechSynthesis`
3. Verify audio output device
4. Test with different voice language in settings

### Queue Reset Not Running

**Solutions:**
1. Verify cron is active: `systemctl status cron`
2. Check crontab: `crontab -l`
3. Test manually: `php artisan queue:reset-daily`
4. Check logs: `storage/logs/laravel.log`

---

## 📱 URLs Quick Reference

| Role | URL | Description |
|------|-----|-------------|
| Kiosk | `/kiosk` | Layar pengambilan nomor |
| TV Display | `/tv` | Monitor panggilan antrian |
| Operator | `/operator` | Dashboard operator loket |
| Admin | `/admin/analytics` | Laporan & analytics |
| Super Admin | `/admin/settings` | Konfigurasi sistem & theme |
| Tracking | `/tracking/{token}` | Live tracking via QR code |

---

## 🔄 Update & Maintenance

### Application Update

```bash
# Pull latest changes
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear cache
php artisan optimize:clear

# Restart services
sudo supervisorctl restart antri-reverb:*
sudo supervisorctl restart antri-queue:*
```

---

## 📞 Support

For technical support or issues, please:
1. Check logs in `storage/logs/`
2. Review troubleshooting section
3. Contact system administrator

---

**Last Updated**: 2026-09-09  
**Maintained by**: Development Team
</content>