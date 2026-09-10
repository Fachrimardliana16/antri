# Changelog - Operator Permission & Theme Update

## #8 - Operator Permission per Loket ✅

### Backend Changes

**File: `app/Livewire/Operator/Dashboard.php`**

1. **Mount Method** - Operator hanya bisa akses assigned counter:
   - Jika `assigned_counter_id` null → error message
   - Lock ke counter yang di-assign
   - Tidak bisa pilih counter lain

2. **setCounter Method** - Block switching untuk operator:
   - Operator tidak bisa `setCounter()` ke loket lain
   - Admin masih bisa switch counter (untuk testing/management)

### Frontend Changes

**File: `resources/views/livewire/operator/dashboard-new.blade.php`**

1. **Counter Selector Removed**:
   - Dropdown/button pilih loket dihapus
   - Diganti dengan display-only card (loket name + service)

2. **Stats Cards Enhanced**:
   - ✅ **Sedang Dilayani** - Current ticket number + waktu
   - ✅ **Antrian Berikutnya** - Next ticket + jumlah waiting
   - ✅ **Selesai Hari Ini** - Completed count
   - ✅ **Dilewati** - Skipped count

3. **Waiting Queue List**:
   - Show max 10 tickets
   - Next ticket highlighted dengan badge "NEXT"

### Security

- Operator role **LOCKED** ke 1 loket saja
- Tidak bisa switch via UI atau wire:click
- Backend validation mencegah unauthorized access
- Admin masih punya full access untuk monitoring

---

## #7 - Theme Operator Dashboard ✅

### Konsistensi dengan TV/Kiosk

**Color Palette:**
- Background: `bg-slate-900` (sama dengan TV/kiosk)
- Cards: `bg-slate-800` dengan `border-slate-700`
- Primary accent: `bg-blue-600` (tombol utama)
- Status colors: green, amber, red dengan opacity 20% untuk background

**Layout:**
- Header: Logo + app name + user info + logout
- Main: 2 kolom grid (content + sidebar stats)
- Cards: Rounded-xl dengan border konsisten
- Spacing: px-8, py-4 untuk consistency

**Typography:**
- Font: Instrument Sans (sama dengan TV/kiosk)
- Ticket numbers: font-mono untuk readability
- Font sizes: text-xs untuk label, text-3xl untuk stats

### New Layout File

**File: `resources/views/layouts/operator-minimal.blade.php`**
- Clean HTML5 boilerplate
- No header/footer (full page control dari Livewire)
- `bg-slate-900` body
- Font Instrument Sans loaded

### View

**File: `resources/views/livewire/operator/dashboard-new.blade.php`**
- Full redesign dengan slate-900 + blue theme
- Keyboard shortcuts tetap aktif (Space, R, F, S)
- Modal transfer dengan theme matching
- Responsive grid layout

---

## Cara Assign Operator ke Loket

### Via Database

```sql
-- Assign operator user_id=2 ke counter_id=1
UPDATE users SET assigned_counter_id = 1 WHERE id = 2;
```

### Via Seeder (Recommended)

```php
// database/seeders/DatabaseSeeder.php
$operator1 = User::where('email', 'operator1@example.com')->first();
$operator1->update(['assigned_counter_id' => 1]);

$operator2 = User::where('email', 'operator2@example.com')->first();
$operator2->update(['assigned_counter_id' => 2]);
```

### Via Admin UI (Future Enhancement)

Bisa ditambahkan di Admin > Users:
- Dropdown "Assign to Counter"
- Admin pilih counter untuk setiap operator
- Save → update `assigned_counter_id`

---

## Testing

### Test Operator Permission

1. Login sebagai operator yang sudah di-assign ke loket 1
2. Dashboard harus show:
   - "Loket Anda: Loket 1" (locked, tidak bisa ganti)
   - Stats untuk loket 1 saja
   - Queue waiting untuk service loket 1

3. Try switching counter via browser console:
   ```javascript
   Livewire.emit('setCounter', 2)
   ```
   Harus muncul error: "Anda tidak memiliki akses ke loket tersebut."

### Test Theme

1. Bandingkan side by side:
   - `http://localhost:8000/tv` (TV Monitor)
   - `http://localhost:8000/kiosk` (Kiosk)
   - `http://localhost:8000/operator` (Operator Dashboard)

2. Check consistency:
   - ✅ Background color: slate-900
   - ✅ Card color: slate-800
   - ✅ Border: slate-700
   - ✅ Primary button: blue-600
   - ✅ Typography: Instrument Sans
   - ✅ Logo display: sama di semua halaman

---

## Breaking Changes

### For Operators

- Operator **HARUS** punya `assigned_counter_id` sebelum bisa akses dashboard
- Jika null → error message: "Anda belum ditugaskan ke loket manapun"
- Tidak bisa switch loket lagi (sebelumnya bisa)

### Migration Needed?

**TIDAK** - kolom `assigned_counter_id` sudah ada di tabel `users`.

Yang perlu dilakukan:
1. Assign semua operator ke counter masing-masing via SQL/seeder
2. Inform operator bahwa mereka locked ke 1 loket

---

## Next Steps (Optional Enhancements)

1. **Admin UI untuk Assign Counter**
   - Admin dashboard → Users list
   - Button "Edit Assignment"
   - Dropdown select counter

2. **Operator Break Timer**
   - Auto-set counter ke "active" setelah X menit break

3. **Performance Metrics**
   - Avg service time per operator
   - Peak hours analysis
   - Operator leaderboard

4. **Multi-counter Assignment** (if needed)
   - Operator bisa handle 2+ loket
   - Switch between assigned counters only
