# Panduan Deploy Wedding Invitation ke Vercel

Dokumen ini adalah arahan lengkap buat deploy project ini ke Vercel. Ikuti urutan dari atas ke bawah.

---

## 1. Ringkasan Project (yang perlu lu tahu sebelum deploy)

- **Framework:** Laravel 13 (PHP ^8.3), styling pakai file statis di `public/css` + `public/js` — **tidak butuh `npm run build`** (Vite cuma dipakai kalau mau develop Tailwind, karena blade views tidak memanggil `@vite`).
- **Fitur:**
  - `GET /` — halaman undangan (menampilkan 30 wedding wishes terbaru dari DB)
  - `POST /rsvp` — simpan RSVP ke tabel `rsvps`
  - `POST /wishes` — simpan wedding wishes ke tabel `wishes`
  - `GET /admin/login` — panel admin untuk rekap RSVP, password dari env `ADMIN_PASSWORD`
  - `GET /up` — health check bawaan Laravel
- **Database:** saat develop pakai SQLite (`database/database.sqlite`). Di Vercel ini **tidak bisa langsung dipakai** — filesystem serverless itu ephemeral (data hilang tiap instance restart), jadi production **wajib pakai database eksternal** (Postgres atau MySQL).
- **Session & CSRF:** form RSVP/wishes/admin pakai `@csrf` dan flash message, jadi session harus jalan di production. Sudah diset `SESSION_DRIVER=cookie` di Dockerfile (cookie session tidak butuh server/file, aman di multi-instance).

## 2. Cara Kerja Deploy di Vercel (konsep singkat)

Vercel deploy Laravel ini sebagai **container** (runtime resmi Vercel untuk Docker). File-file baru yang mendukung ini:

| File | Fungsi |
|---|---|
| `Dockerfile` | Build image: PHP 8.4 + FrankenPHP (web server Caddy), install composer deps, precompile view/event cache |
| `Caddyfile` | Konfigurasi web server: serve folder `public/`, file statis (css/js/gambar/audio) langsung dilayani, sisanya ke Laravel |
| `vercel.json` | Daftarkan service container `app` + rewrite semua request ke service itu |
| `.dockerignore` | File lokal yang dibuang saat build (env, node_modules, dll) |
| `app/Providers/AppServiceProvider.php` | Dipaksa pentelepon/URL https di production (CSRF & redirect tetap benar di belakang proxy Vercel) |

Referensi resmi: [Deploy Laravel on Vercel with Docker](https://vercel.com/kb/guide/laravel-php-with-docker).

## 3. Langkah Deploy

### Langkah 0 — Persiapan lokal (sekali saja)

PHP 8.4 + Composer sudah ada di mesin lu (Laragon). Inisialisasi project lokal:

```powershell
composer install
copy .env.example .env
php artisan key:generate
```

### Langkah 1 — Push kode ke GitHub

Repo sudah ada di `https://github.com/zyaaaaaa939/wedding-invitaion`, branch `main` sudah sinkron. Tinggal:

```powershell
git add .
git commit -m "pesan commit"
git push
```

Setiap push ke `main` akan otomatis memicu deploy baru di Vercel nanti.

### Langkah 2 — Buat database eksternal (WAJIB)

Pilih salah satu:

**Opsi A — Postgres via Vercel Marketplace (paling gampang, terintegrasi):**
1. Setelah project dibuat di Vercel (Langkah 4), buka **Project → Storage → Add Database → pilih Neon Postgres** (plan Free ada).
2. Vercel otomatis bikin env `DATABASE_URL`. Nilainya (format `postgresql://user:pass@host/db`) copy — akan dipakai di Langkah 3 & 5.

**Opsi B — MySQL gratis (Aiven free / hosting share / lainnya):**
- Catat host, port, nama database, user, password. PHP lokal lu (Laragon) sudah punya `pdo_mysql` aktif — lebih gampang buat migrasi dari Windows.

### Langkah 3 — Set Environment Variables di Vercel

Buka **Project → Settings → Environment Variables**, tambahkan (pilih environment: Production, Preview, Development — sesuai kebutuhan):

| Nama | Isi | Wajib |
|---|---|---|
| `APP_KEY` | hasil `php artisan key:generate --show` (format `base64:...`) | ✅ |
| `DB_CONNECTION` | `pgsql` (Opsi A) atau `mysql` (Opsi B) | ✅ |
| `DB_URL` | **khusus pgsql**: connection string Postgres lengkap (dari `DATABASE_URL` Neon) | Opsi A |
| `DB_HOST` | host MySQL | Opsi B |
| `DB_PORT` | port MySQL (contoh `3306`) | Opsi B |
| `DB_DATABASE` | nama database | Opsi B |
| `DB_USERNAME` | user database | Opsi B |
| `DB_PASSWORD` | password database | Opsi B |
| `ADMIN_PASSWORD` | password login panel admin | ✅ |

Yang **sudah dibake di Dockerfile** dan tidak perlu di-set (tapi bisa dioverride kalau mau): `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_DRIVER=cookie`, `CACHE_STORE=array`, `QUEUE_CONNECTION=sync`, `LOG_CHANNEL=stderr`.

> ⚠️ **Jangan ganti `SESSION_DRIVER` ke `file`/`array`** — form RSVP/wishes bakal gagal 419 kalau session gak jalan antar request.

### Langkah 4 — Import project ke Vercel

1. Buka https://vercel.com → login pakai GitHub.
2. **Add New → Project** → pilih import repo `zyaaaaaa939/wedding-invitaion`.
3. Framework preset: biarkan auto-detect (file `vercel.json` sudah bilang ini container service — kalau ditanya, pilih **Other**).
4. Confirm build settings default (jangan ubah Build Command / Output Directory — vercel.json yang pegang kontrol).
5. **Deploy**.

Build pertama biasanya butuh beberapa menit (download base image + composer install). Cek log di halaman deployment kalau error.

### Langkah 5 — Jalankan migrasi database (sekali, dari lokal)

Migrations TIDAK dijalankan otomatis oleh Vercel. Jalankan dari mesin lu mengarah ke DB production:

**Opsi A (Postgres/Neon):** aktifkan dulu ekstensi pdo_pgsql di PHP Laragon:
- Buka `C:\laragon\bin\php\php-8.4.3-nts-Win32-vs17-x64\php.ini`, cari baris `;extension=pdo_pgsql`, hapus tanda `;`, lalu tutup terminal dan buka terminal baru.

```powershell
$env:DB_CONNECTION = "pgsql"
$env:DB_URL = "postgresql://user:password@host/dbname?sslmode=require"   # dari Neon
php artisan migrate --force
```

**Opsi B (MySQL):**

```powershell
$env:DB_CONNECTION = "mysql"
$env:DB_HOST = "host-database-lu"
$env:DB_PORT = "3306"
$env:DB_DATABASE = "nama-database"
$env:DB_USERNAME = "user"
$env:DB_PASSWORD = "password"
php artisan migrate --force
```

> Data RSVP/wishes di SQLite lokal **tidak otomatis terbawa** ke production — production DB mulai kosong setelah migrasi. Kalau ada wishes yang mau dipindah, tidak perlu, biarkan tamu isi ulang di production.

### Langkah 6 — Verifikasi (checklist)

Buka URL project Vercel, tes satu per satu:

- [ ] Halaman undangan terbuka (teks, gambar, audio muncul)
- [ ] `/up` → baliknya "OK" (health check)
- [ ] Isi form RSVP → submit → muncul pesan sukses
- [ ] Isi wedding wishes → submit → muncul di daftar wishes (refresh halaman)
- [ ] `/admin/login` → masukkan `ADMIN_PASSWORD` → dashboard rekap RSVP terbuka, data RSVP tadi masuk
- [ ] `/favicon.ico` & `/css/invitation.css` ter-load (Network tab, status 200)

## 4. Troubleshooting

| Gejala | Penyebab & solusi |
|---|---|
| `500 — server error`, log: "no application encryption key" | `APP_KEY` belum diset di Vercel → set env lalu redeploy |
| `419 Page Expired` pas submit form | Session rusak → pastikan `SESSION_DRIVER=cookie` dan `APP_KEY` stabil (jangan sering ganti ENV APP_KEY) |
| `SQLSTATE...` / halaman 500 terus | Env DB salah salah ketik, migrasi belum jalan, atau DB sedang suspended (Neon free kadang sleep ~0.5–1s di request pertama, cukup refresh) |
| `502 / FUNCTION_INVOCATION_FAILED` | Container gagal start → buka **Deployment → Build Logs / Runtime Logs**, biasanya syntax Dockerfile/Caddyfile |
| Asset css/js/gambar 404 | Pastikan file-nya ada di folder `public/`, hard refresh (Ctrl+Shift+R) |
| Admin login selammer "Password admin salah" | `ADMIN_PASSWORD` belum diset di Vercel, atau isinya beda |
| Template error `No application encryption key` di Preview | Preview environment juga butuh `APP_KEY` (set env untuk semua environment) |

## 5. Catatan Penting

1. **Jangan pernah commit file `.env`** (sudah di-gitignore). Secret hanya boleh di Vercel environment variables.
2. **APP_KEY sekali generate untuk production, jangan terus diganti kek teklek** — cookie session tamu & admin bakal invalid seblag & form 419.
3. **Update isi undangan setelah live:** edit code → push ke GitHub → deploy otomatis jalan. Data di DB tidak terganggu.
4. **Domain custom:** Project → Settings → Domains → Add, terpasang DNS mengikuti spesifikasi Vercel.
5. **Preview deployments:** push ke branch lain → dapat URL preview otomatis. Bagus untuk testing desain baru sebelum main.
6. **Biaya:** Free plan Vercel (Hobby) cukup untuk 1 undangan 1 project, container bisa scale-to-zero. Neon Postgres free tier 0.5 GB.
7. **Audio `Akad.mp3` (5.9 MB):** tiap deploy image membawa file ini; normal di free plan, jadi aman.

## 6. Alternatif (Plan B) — kalau mau manual coba-coba runtime `vercel-php`

Ada cara kedua deploy Laravel ke Vercel tanpa Docker: runtime komunitas `vercel-php` (contoh: https://php.vercel.app). Tetap jalan, tapi **resmi direkomendasikan Vercel sekarang lewat container** (cara di atas), jadi panduan ini pakai cara container. Kalau mau eksplorasi: runtime `vercel-php` + `api/index.php` entrypoint. Anda hampir pasti gak butuh ini.
