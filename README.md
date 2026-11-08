# LaporKota

**Rancang Bangun Sistem Informasi Pelaporan dan Monitoring Permasalahan Infrastruktur dan Lingkungan Berbasis Mobile Web di Kota X**

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?logo=php&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-3-06B6D4?logo=tailwindcss&logoColor=white)
![Leaflet](https://img.shields.io/badge/Leaflet-1.9.4-199900?logo=leaflet&logoColor=white)

LaporKota adalah aplikasi web responsif (mobile web) yang memungkinkan masyarakat melaporkan permasalahan infrastruktur dan lingkungan (jalan rusak, lampu jalan padam, sampah menumpuk, saluran tersumbat, dan sejenisnya) lengkap dengan lokasi pada peta dan foto bukti. Laporan ditindaklanjuti oleh petugas, dan perkembangannya dapat dipantau oleh pelapor serta pimpinan melalui dashboard monitoring.

## Daftar isi

- [Fitur](#fitur)
- [Hak akses per peran](#hak-akses-per-peran)
- [Teknologi](#teknologi)
- [Prasyarat](#prasyarat)
- [Instalasi](#instalasi)
- [Akun uji](#akun-uji)
- [Mengakses dari ponsel](#mengakses-dari-ponsel)
- [Pengujian](#pengujian)
- [Pemecahan masalah](#pemecahan-masalah)
- [Struktur basis data](#struktur-basis-data)
- [Struktur direktori penting](#struktur-direktori-penting)
- [Catatan keamanan](#catatan-keamanan)
- [Penulis](#penulis)

## Fitur

**Warga (pelapor)**
- Registrasi, login, dan pengelolaan profil.
- Membuat laporan: kategori, judul, deskripsi, titik lokasi pada peta (Leaflet + OpenStreetMap, dengan tombol "Gunakan lokasi saya"), dan foto bukti.
- Melihat riwayat laporan beserta status berwarna: menunggu, diproses, selesai, ditolak.
- Melihat detail laporan, peta lokasi, dan riwayat penanganan (timeline) lengkap dengan foto hasil.
- Menghapus laporan yang masih berstatus menunggu.

**Admin (petugas)**
- Melihat seluruh laporan masuk dengan filter status, kategori, dan pencarian.
- Memberi tanggapan, mengubah status laporan, dan mengunggah foto hasil penanganan.
- Mengakses dashboard monitoring.

**Eksekutif (koordinator/pimpinan)**
- Mengakses dashboard monitoring dan melihat daftar serta detail laporan (hanya lihat).

**Dashboard monitoring**
- Jumlah laporan total dan per status.
- Rata-rata waktu penyelesaian.
- Peta sebaran laporan berwarna sesuai status.
- Grafik laporan per kategori dan tren 6 bulan terakhir.
- Daftar laporan terbaru.

## Hak akses per peran

| Fitur | Warga | Admin | Eksekutif |
|---|:---:|:---:|:---:|
| Registrasi, login, profil | ✓ | ✓ | ✓ |
| Membuat laporan | ✓ | – | – |
| Melihat dan menghapus laporan milik sendiri | ✓ | – | – |
| Melihat seluruh laporan masuk | – | ✓ | ✓ |
| Memberi tanggapan dan mengubah status | – | ✓ | – |
| Dashboard monitoring | – | ✓ | ✓ |

Seluruh halaman memerlukan login. Pembatasan akses dilakukan oleh middleware `auth` dan `role` (`app/Http/Middleware/EnsureUserHasRole.php`).

## Teknologi

| Komponen | Teknologi |
|---|---|
| Framework | Laravel 12 (starter kit Laravel Breeze) |
| Bahasa | PHP 8.2 atau lebih baru |
| Basis data | MySQL (SQLite untuk pengujian otomatis) |
| Antarmuka | Blade, Tailwind CSS 3, Alpine.js, Vite |
| Peta | Leaflet 1.9.4 dengan data OpenStreetMap |
| Pengujian | Pest 3 (PHPUnit) |

## Prasyarat

- PHP 8.2 atau lebih baru dengan ekstensi umum Laravel (mbstring, xml, curl, zip, gd, pdo_mysql)
- Composer
- Node.js dan npm
- MySQL (misalnya melalui XAMPP atau Laragon)
- Git

## Instalasi

1. **Clone repositori**

   ```bash
   git clone https://github.com/VAdolf/laporkota.git
   cd laporkota
   ```

2. **Pasang dependensi**

   ```bash
   composer install
   npm install
   ```

3. **Salin berkas pengaturan dan buat kunci aplikasi**

   ```bash
   # Windows
   copy .env.example .env
   # Linux / macOS
   # cp .env.example .env

   php artisan key:generate
   ```

4. **Buat basis data** bernama `db_laporkota` (misalnya lewat phpMyAdmin), lalu ubah pengaturan pada `.env`:

   ```env
   APP_NAME=LaporKota
   APP_LOCALE=id

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=db_laporkota
   DB_USERNAME=root
   DB_PASSWORD=
   ```

   Opsional, titik tengah awal peta (bawaan: pusat Kota Surabaya sebagai placeholder untuk Kota X):

   ```env
   MAP_DEFAULT_LAT=-7.2575
   MAP_DEFAULT_LNG=112.7521
   MAP_DEFAULT_ZOOM=13
   ```

5. **Jalankan migrasi, seeder, dan tautan penyimpanan**

   ```bash
   php artisan migrate --seed
   php artisan storage:link
   ```

6. **Kompilasi aset dan jalankan server**

   ```bash
   # Terminal 1
   npm run dev        # atau: npm run build

   # Terminal 2
   php artisan serve
   ```

7. Buka **http://127.0.0.1:8000** di peramban.

## Akun uji

Dibuat otomatis oleh seeder. Hanya untuk pengembangan.

| Peran | Email | Kata sandi |
|---|---|---|
| Admin | `admin@laporkota.go.id` | `pompom123` |
| Eksekutif | `eksekutif@laporkota.go.id` | `pompom123` |
| Warga | `warga@gmail.com` | `pompom123` |

Warga baru juga dapat mendaftar sendiri melalui halaman registrasi.

## Mengakses dari ponsel

Aplikasi berjalan di komputer, dan ponsel membukanya melalui peramban.

**Satu jaringan Wi-Fi**

```bash
npm run build
php artisan serve --host=0.0.0.0
```

Buka `http://IP-KOMPUTER:8000` di ponsel (cek IP dengan `ipconfig`). Tombol lokasi perangkat tidak berfungsi pada alamat HTTP biasa, tetapi titik lokasi tetap dapat dipilih manual pada peta.

**HTTPS sementara untuk demo (Cloudflare Quick Tunnel, gratis)**

```bash
php artisan serve
cloudflared tunnel --url http://localhost:8000
```

Buka alamat `https://...trycloudflare.com` yang tampil di terminal. Agar tampilan benar melalui proxy, tambahkan sementara pada `bootstrap/app.php`, di dalam `withMiddleware`:

```php
$middleware->trustProxies(at: '*');
```

Hapus baris tersebut setelah demo selesai.

## Pengujian

Pengujian otomatis memakai Pest pada basis data sementara, sehingga tidak memengaruhi data asli.

```bash
php artisan test
```

Cakupan pengujian meliputi autentikasi, pembatasan akses per peran, validasi laporan (lokasi dan foto), penghapusan laporan, penanganan oleh admin, dan dashboard monitoring.

## Pemecahan masalah

| Gejala | Penyebab dan solusi |
|---|---|
| Foto laporan tidak tampil (muncul teks alternatif gambar) | Tautan penyimpanan belum ada atau rusak. Jalankan `php artisan storage:link`. Jika sudah ada, hapus lalu buat ulang: `Remove-Item public\storage` (Windows PowerShell) atau `rm public/storage` (Linux/macOS), kemudian `php artisan storage:link`. Pada Windows, jalankan terminal sebagai administrator atau aktifkan Developer Mode bila muncul galat izin symlink. Foto tidak termasuk dalam repositori, sehingga foto dari komputer lain tidak ikut terbawa. |
| Tampilan polos tanpa CSS | Aset belum dikompilasi. Jalankan `npm install`, lalu `npm run dev` (atau `npm run build`). Jika `npm run dev` sudah dihentikan, pastikan berkas `public/hot` tidak tertinggal. |
| Galat `Vite manifest not found` | Aset belum dibuild. Jalankan `npm run build`. |
| Galat koneksi basis data | Pastikan MySQL menyala, basis data `db_laporkota` sudah dibuat, dan pengaturan `DB_*` pada `.env` benar. Lalu jalankan `php artisan config:clear`. |
| `migrate --seed` gagal karena email sudah terdaftar | Seeder sudah pernah dijalankan. Gunakan `php artisan migrate:fresh --seed` (menghapus seluruh data). |
| Perubahan pada `.env` tidak berpengaruh | Jalankan `php artisan optimize:clear`. |
| Peta kosong atau tidak termuat | Leaflet dan peta OpenStreetMap dimuat dari internet, sehingga koneksi internet diperlukan. |
| Tombol "Gunakan lokasi saya" tidak berfungsi | Peramban hanya mengizinkan lokasi pada HTTPS atau `localhost`. Gunakan Cloudflare Quick Tunnel, atau pilih titik secara manual pada peta. |

## Struktur basis data

| Tabel | Keterangan |
|---|---|
| `users` | Akun pengguna dengan kolom `role` (`warga`, `admin`, `eksekutif`) |
| `kategoris` | Kategori permasalahan |
| `laporans` | Laporan: judul, deskripsi, koordinat (`lat`, `lng`), `foto_bukti`, `status` |
| `tanggapans` | Tanggapan petugas: `status`, `catatan`, `foto_hasil` |

Relasi: satu `user` memiliki banyak `laporans`; satu `kategori` memiliki banyak `laporans`; satu `laporan` memiliki banyak `tanggapans`; satu `user` (admin) memberi banyak `tanggapans`.

## Struktur direktori penting

```text
app/
  Http/
    Controllers/
      DashboardController.php        # arahan dashboard per peran
      LaporanController.php          # pelaporan oleh warga
      MonitoringController.php       # dashboard monitoring
      Kelola/LaporanController.php   # penanganan oleh admin
    Middleware/EnsureUserHasRole.php # pembatasan akses per peran
    Requests/StoreLaporanRequest.php # validasi laporan
  Models/                            # User, Kategori, Laporan, Tanggapan
config/laporkota.php                 # pengaturan peta
database/
  migrations/
  seeders/
resources/views/
  laporan/                           # form, detail
  kelola/laporan/                    # daftar dan detail (admin/eksekutif)
  monitoring/                        # dashboard
routes/web.php
tests/Feature/                       # pengujian otomatis
```

## Catatan keamanan

- Ganti seluruh kata sandi akun uji sebelum sistem dipublikasikan.
- Jangan pernah mengunggah berkas `.env` ke repositori (sudah diabaikan oleh `.gitignore`).
- Sebelum dipasang di server produksi, atur `APP_ENV=production` dan `APP_DEBUG=false`, serta hapus `trustProxies(at: '*')` jika tidak berada di belakang proxy tepercaya.

## Penulis

Capstone Project, Program Studi S1 Teknik Elektro, Universitas Negeri Surabaya.

- Penulis: [isi nama lengkap]
- Dosen pembimbing: [isi nama dosen pembimbing]
- GitHub: [@VAdolf](https://github.com/VAdolf)
