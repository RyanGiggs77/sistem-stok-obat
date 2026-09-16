# Mini Sistem Manajemen Stok Obat

Aplikasi operasional persediaan obat: Laravel (Blade + jQuery AJAX tanpa reload),
MySQL, background Job/Queue untuk export, progress export realtime via polling.

## 1. Requirement

- PHP ^8.3 + extension: `pdo_mysql`, `mbstring`, `zip`, `gd`/`imagick` (untuk PDF/DomPDF)
- Composer 2
- MySQL 8 (database `medicine`)
- Node.js + NPM (opsional, hanya jika ubah asset Vite)

## 2. Instalasi

```powershell
composer install
copy .env.example .env
php artisan key:generate
```

## 3. Konfigurasi database

Edit `.env`:

```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=medicine
DB_USERNAME=root
DB_PASSWORD=

APP_TIMEZONE=Asia/Jakarta
QUEUE_CONNECTION=database
CACHE_STORE=database
```

> `.env` berisi credential lokal dan **tidak disertakan** di repository
> (sudah ada di `.gitignore`). Reviewer memakai `.env.example` sebagai acuan.

Buat database kosong `medicine` terlebih dahulu via phpMyAdmin / CLI.

## 4. Migration & seeder

```powershell
php artisan migrate
php artisan db:seed --class=ObatSeeder
```

Seeder mengisi 10 data manual + **10.000 data factory** (`ObatFactory`,
20 batch @500 baris agar hemat memori). Total: 10.010 baris untuk menguji
search, pagination (1.001 halaman @10), queue, dan progress export.
Jalankan ulang dengan aman (`insertOrIgnore` untuk data manual).

## 5. Menjalankan aplikasi (2 terminal)

Terminal 1 — web server:

```powershell
php artisan serve
# buka http://localhost:8000/obat
```

Terminal 2 — queue worker (**wajib** untuk export Excel/PDF):

```powershell
php artisan queue:work --verbose
```

Tanpa worker, progress export stuck di 0%. Setiap habis mengubah kode
PHP (controller/model/job), restart worker (`Ctrl+C`, jalankan lagi).

## 6. Fitur

- CRUD obat via modal + jQuery AJAX (tambah, lihat detail, edit, hapus)
- Validasi server-side, error 422 tampil merah per field, kode unique
- Realtime search (nama/kode/kategori, debounce 500ms) + filter kategori +
  filter status + pagination, semua bisa kombinasi
- Auto-save field Stok, Minimum Stock, Expired Date, Notes (debounce 800ms,
  indikator Saving.../Saved/Failed, anti race-condition via nomor urut + abort)
- Status otomatis: Stok Habis (`stock<=0`), Menipis (`stock<=minimum`),
  Kadaluarsa (`expired_date<hari ini`), Tersedia
- Export Excel async (Job + progress nyata `processed/total`, polling 1 detik,
  tombol download muncul setelah `done`)
- Export PDF laporan stok async dengan pola yang sama (maksimal 2.000 baris,
  lihat keterbatasan)
- Judul + nama file export memuat tanggal/waktu WIB

## 7. Keputusan teknis & keterbatasan

1. **Progress export disimpan di Cache** (`obat_export:{uuid}`,
   `obat_export_pdf:{uuid}`), bukan tabel baru — data sementara, kedaluwarsa
   1 jam. Isi: `status/processed/total/percent/file/error/generated_at`.
2. **Excel: `phpoffice/phpspreadsheet`** langsung (tanpa maatwebsite/excel,
   kebutuhan 1 sheet sederhana). Ditulis `chunk(500)` agar hemat memori.
3. **PDF: `barryvdh/laravel-dompdf`** (render view Blade). DomPDF menyusun
   seluruh tabel di RAM (`Cellmap`), sehingga **dibatasi 2.000 baris**
   (`MAX_PDF_ROWS`); di atas itu ditolak 422 dengan pesan + saran pakai Excel.
   Untuk 10.010 baris penuh gunakan Export Excel.
4. **Filter terpusat** di `Obat::scopeFilter()` dipakai tabel, Excel, dan PDF
   supaya hasil konsisten.
5. **Zona waktu `Asia/Jakarta`** (`APP_TIMEZONE`); badge status frontend
   memakai tanggal lokal browser agar tidak selisih hari vs UTC.
6. Tanpa autentikasi.
