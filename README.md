# 🏢 SIKOMLEK — Sistem Informasi Bag Komlek

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-v12-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/Filament-v5-FDAE4B?style=for-the-badge&logo=laravel&logoColor=white" alt="Filament">
  <img src="https://img.shields.io/badge/Livewire-v4-FB70A9?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire">
  <img src="https://img.shields.io/badge/Tailwind_CSS-v4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS">
  <img src="https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
</p>

**SIKOMLEK** adalah sistem informasi berbasis web untuk mengelola layanan internal organisasi, mencakup **peminjaman barang**, **perbaikan peralatan**, **dukungan kegiatan**, **inventaris**, dan **review layanan**. Dibangun dengan Laravel 12, Filament v5, dan Tailwind CSS v4.

---

## 📋 Daftar Isi

- [Fitur Utama](#-fitur-utama)
- [Tech Stack](#-tech-stack)
- [Prasyarat](#-prasyarat)
- [Instalasi](#-instalasi)
- [Konfigurasi](#%EF%B8%8F-konfigurasi)
- [Menjalankan Aplikasi](#-menjalankan-aplikasi)
- [Akun Default](#-akun-default)
- [Role & Hak Akses](#-role--hak-akses)
- [Menjalankan Test](#-menjalankan-test)
- [Struktur Proyek](#-struktur-proyek)
- [Perintah Berguna](#-perintah-berguna)

---

## ✨ Fitur Utama

| Modul | Deskripsi |
|---|---|
| 📦 **Inventaris Barang** | Kelola data barang, kategori, merek, dan status ketersediaan |
| 📋 **Pengajuan Peminjaman** | Karyawan mengajukan peminjaman barang, admin menyetujui/menolak |
| 🔧 **Pengajuan Perbaikan** | Ajukan perbaikan peralatan dan pantau progresnya |
| 🤝 **Dukungan Kegiatan** | Permintaan dukungan barang untuk kegiatan organisasi |
| ⭐ **Review Layanan** | Karyawan memberikan review & rating setelah layanan selesai |
| 📊 **Rekap & Laporan** | Rekap peminjaman, perbaikan, dukungan, inventory, dan review dengan export PDF/Excel |
| 📈 **Dashboard Real-time** | Statistik dan grafik overview per role dengan polling otomatis |
| 🔔 **Notifikasi** | Notifikasi real-time untuk pengajuan baru dan perubahan status |

---

## 🛠 Tech Stack

| Teknologi | Versi | Keterangan |
|---|---|---|
| PHP | ^8.2 | Runtime server |
| Laravel | v12 | Framework utama |
| Filament | v5 | Admin panel & dashboard |
| Livewire | v4 | Komponen interaktif real-time |
| Tailwind CSS | v4 | Utility-first CSS framework |
| Vite | v7 | Bundler frontend |
| MariaDB / MySQL | 10.x+ / 8.x+ | Database server |
| Laravel Reverb | latest | WebSocket server |
| DomPDF | v3 | Export PDF |
| Maatwebsite Excel | v3 | Export Excel |

---

## 📌 Prasyarat

Pastikan tools berikut sudah terinstal di sistem Anda:

- **PHP** ≥ 8.2 dengan ekstensi: `mbstring`, `xml`, `ctype`, `json`, `bcmath`, `pdo_mysql`, `gd`, `zip`
- **Composer** ≥ 2.x
- **Node.js** ≥ 18.x dan **npm** ≥ 9.x
- **MariaDB** ≥ 10.x atau **MySQL** ≥ 8.x
- **Git**

> **💡 Tip:** Untuk Windows, gunakan [Laragon](https://laragon.org/) atau [XAMPP](https://www.apachefriends.org/) yang sudah menyediakan PHP, MySQL, dan Composer dalam satu paket.

---

## 🚀 Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/username/silankom.git
cd silankom
```

### 2. Install Dependensi PHP

```bash
composer install
```

### 3. Salin File Environment

```bash
cp .env.example .env
```

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Install Dependensi Node.js

```bash
npm install
```

### 6. Build Asset Frontend

```bash
npm run build
```

> **Atau**, jalankan semua langkah 2–6 sekaligus dengan:
> ```bash
> composer run setup
> ```

---

## ⚙️ Konfigurasi

### Database

Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=silankom
DB_USERNAME=root
DB_PASSWORD=
```

> Untuk MySQL, ubah `DB_CONNECTION` menjadi `mysql`.

### Buat Database

Buat database baru dengan nama `silankom` (atau sesuai `DB_DATABASE` di `.env`):

```sql
CREATE DATABASE silankom CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Migrasi & Seed Database

```bash
php artisan migrate --seed
```

Perintah ini akan membuat semua tabel dan mengisi data awal (role, jabatan, unit kerja, merek, kategori, barang, dan user default).

---

## 🖥 Menjalankan Aplikasi

### Mode Development (Rekomendasi)

Jalankan seluruh service sekaligus (server, queue, Vite HMR, dan Reverb WebSocket):

```bash
composer run dev
```

Aplikasi akan tersedia di: **http://127.0.0.1:8000**

### Mode Manual (Terpisah)

Jika ingin menjalankan service secara terpisah:

```bash
# Terminal 1 — Laravel Server
php artisan serve

# Terminal 2 — Queue Worker
php artisan queue:listen --tries=1

# Terminal 3 — Vite Dev Server (hot reload CSS/JS)
npm run dev

# Terminal 4 — WebSocket Server (opsional, untuk notifikasi real-time)
php artisan reverb:start
```

### Build untuk Production

```bash
npm run build
```

---

## 🔑 Akun Default

Setelah menjalankan `php artisan migrate --seed`, akun berikut tersedia:

| Role | NIP | Password |
|---|---|---|
| **Super Admin** | `12345678` | `admin123` |

> ⚠️ **Penting:** Segera ubah password default setelah login pertama kali.

---

## 👥 Role & Hak Akses

| ID | Role | Akses |
|---|---|---|
| 1 | **Super Admin** | Full access — kelola semua modul, user, rekap, dan laporan |
| 2 | **Admin Komlek** | Kelola barang, pengajuan peminjaman, perbaikan, dan dukungan |
| 3 | **Teknisi Komlek** | Tangani perbaikan dan dukungan kegiatan |
| 4 | **Karyawan** | Ajukan peminjaman, perbaikan, dukungan, dan beri review |
| 5 | **Admin TU** | Ajukan peminjaman atas nama karyawan lain |

---

## 🧪 Menjalankan Test

```bash
# Jalankan semua test
php artisan test --compact

# Jalankan test tertentu
php artisan test --compact --filter=NamaTest

# Jalankan test pada file tertentu
php artisan test --compact tests/Feature/ExampleTest.php
```

### Linting & Formatting

```bash
# Cek & perbaiki code style
vendor/bin/pint
```

---

## 📁 Struktur Proyek

```
silankom/
├── app/
│   ├── Exports/             # Export PDF & Excel (DomPDF, Maatwebsite)
│   ├── Filament/
│   │   ├── Pages/           # Halaman custom (Dashboard, Laporan)
│   │   ├── Resources/       # Resource CRUD Filament
│   │   │   ├── Barangs/     # Manajemen barang & inventaris
│   │   │   ├── Pengajuans/  # Pengajuan peminjaman (admin)
│   │   │   ├── ReqPinjams/  # Pengajuan peminjaman (user)
│   │   │   ├── RekapReviews/# Review layanan
│   │   │   └── ...          # Resource lainnya
│   │   └── Widgets/         # Widget dashboard (stats, chart)
│   ├── Http/Controllers/    # Controller untuk export & print
│   ├── Models/              # Eloquent Models
│   ├── Notifications/       # Notifikasi sistem
│   └── Services/            # Business logic (LaporanService)
├── database/
│   ├── factories/           # Model factories untuk testing
│   ├── migrations/          # Database migrations
│   └── seeders/             # Data awal (role, user, barang, dll.)
├── resources/
│   ├── css/                 # Stylesheet (Tailwind v4)
│   ├── js/                  # JavaScript (Alpine, Echo)
│   └── views/               # Blade templates
├── routes/
│   ├── web.php              # Web routes
│   └── console.php          # Console commands
├── tests/                   # PHPUnit tests
├── .env.example             # Template konfigurasi environment
├── composer.json            # PHP dependencies
├── package.json             # Node.js dependencies
└── vite.config.js           # Vite bundler config
```

---

## 💡 Perintah Berguna

| Perintah | Keterangan |
|---|---|
| `composer run setup` | Install semua dependensi, generate key, migrate, dan build |
| `composer run dev` | Jalankan semua service development sekaligus |
| `php artisan migrate:fresh --seed` | Reset database dan isi ulang data awal |
| `php artisan make:filament-resource` | Buat resource Filament baru |
| `vendor/bin/pint` | Perbaiki code style (PSR-12 + Laravel) |
| `php artisan optimize:clear` | Bersihkan semua cache aplikasi |
| `php artisan queue:listen` | Jalankan queue worker untuk job async |

---

## 🔧 Troubleshooting

<details>
<summary><strong>Error: Vite manifest not found</strong></summary>

Jalankan build ulang:
```bash
npm run build
```
Atau jika dalam mode development, pastikan `npm run dev` sedang berjalan.
</details>

<details>
<summary><strong>Error: SQLSTATE Connection refused</strong></summary>

Pastikan:
1. Database server (MySQL/MariaDB) sudah berjalan
2. Konfigurasi `DB_*` di `.env` sudah benar
3. Database `silankom` sudah dibuat
</details>

<details>
<summary><strong>Error: Class not found setelah pull</strong></summary>

Jalankan:
```bash
composer dump-autoload
php artisan optimize:clear
```
</details>

<details>
<summary><strong>Halaman blank / style tidak muncul</strong></summary>

Jalankan:
```bash
npm run build
# atau
npm run dev
```
</details>


