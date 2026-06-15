# 🏥 Aplikasi Praktik Dokter Umum (Klinik)

Sistem informasi manajemen klinik / praktik dokter umum berbasis web yang dibangun menggunakan **Laravel 11**. Aplikasi ini dirancang untuk membantu pengelolaan operasional klinik mulai dari pendaftaran pasien, antrean, pemeriksaan, resep obat, apotek, pembayaran, hingga laporan dan analisis data.

---

## ✨ Fitur-Fitur

### 👥 Manajemen Pengguna & Role
- **Multi Role**: Admin, Pendaftaran, Dokter, Kasir, Apoteker
- Manajemen pengguna (CRUD) khusus admin
- Middleware berbasis role untuk keamanan akses

### 📋 Pendaftaran & Antrean
- Pendaftaran pasien baru / lama
- Pencarian pasien (berdasarkan nama/NIK/no. RM) dengan TomSelect
- Generate nomor antrean otomatis per hari
- Status antrean: Menunggu → Dipanggil → Selesai / Batal
- Reset antrean per tanggal

### 🩺 Pemeriksaan Pasien
- Input keluhan, diagnosa, dan tanda vital
- Pilih tindakan medis (multi-select dengan TomSelect)
- Resep obat (searchable select dengan TomSelect)
- Otomatis mengecualikan obat yang sudah expired
- Riwayat pemeriksaan per pasien

### 💊 Manajemen Obat
- CRUD obat dengan kode, kategori, satuan, stok, harga
- **Batas minimal stok** untuk notifikasi re-order
- **Warna baris otomatis**: 🔴 Merah (expired), 🟡 Kuning (akan expired ≤ 6 bulan), ⚪ Normal
- Export data obat ke CSV

### 💵 Pembayaran
- Pembayaran jasa dokter (konsultasi + tindakan)
- Pembayaran obat (apotek)
- Struk pembayaran yang bisa di-print
- Riwayat dan status pembayaran

### 🏪 Apotek
- Daftar resep obat yang menunggu
- Proses serah obat ke pasien
- Update status resep: Menunggu → Diproses → Selesai

### 📊 Laporan & Analisis
- **Laporan Harian** (filter rentang tanggal: start_date - end_date)
- **Laporan Bulanan** (filter per bulan)
- **Laporan Tahunan** (grafik pendapatan per bulan)
- **🔬 Analisis Tindakan** - Tindakan yang paling sering dilakukan
- **💊 Analisis Obat** - Obat yang paling sering diresepkan
- **⚠️ Stok Menipis** - Obat dengan stok ≤ batas minimal (re-order alert)
- **⏰ Akan Expired** - Obat yang akan expired ≤ 6 bulan

### 📄 Rekam Medis
- Riwayat kunjungan lengkap per pasien
- Cetak PDF rekam medis

### ⚙️ Pengaturan Website
- Ubah **Nama Website** (real-time di sidebar)
- Upload **Logo** (format gambar, max 500KB, disimpan sebagai base64)
- Logo muncul di: favicon, sidebar, struk pembayaran, PDF rekam medis

### 🎨 Tampilan
- **Sidebar collapsible** (minimize/expand) dengan state tersimpan di localStorage
- Warna hijau (emerald) yang profesional
- Responsive design (Desktop & Mobile)
- Support TomSelect untuk multi-select & searchable dropdown

---

## 🔧 Engine & Teknologi

| Komponen | Teknologi |
|----------|-----------|
| **Backend** | PHP 8.2+, Laravel 11 |
| **Frontend** | Blade Template, Tailwind CSS 4, JavaScript |
| **Database** | MySQL / MariaDB |
| **Select Library** | [TomSelect](https://tom-select.js.org/) |
| **Date/Time** | Carbon (dengan locale Indonesia) |
| **Build Tool** | Vite |
| **CSS Framework** | Tailwind CSS |

---

## 🗄️ Struktur Database

| Tabel | Deskripsi |
|-------|-----------|
| `users` | Pengguna sistem (multi role) |
| `patients` | Data pasien |
| `queues` | Antrean pasien per hari |
| `examinations` | Pemeriksaan / rekam medis |
| `prescriptions` | Resep obat dari pemeriksaan |
| `medicines` | Data obat (stok, harga, expired) |
| `doctor_payments` | Pembayaran jasa dokter |
| `pharmacy_sales` | Penjualan obat dari apotek |
| `service_actions` | Master data tindakan medis |
| `doctor_profiles` | Profil dokter |
| `settings` | Pengaturan website (nama & logo base64) |

---

## 🚀 Cara Install dari Git ke Lokal

### Prasyarat
- **PHP** 8.2 atau lebih baru
- **Composer** (manajer dependensi PHP)
- **Node.js** & **NPM** (untuk build frontend)
- **MySQL** / **MariaDB** (database server)
- **Git**

### Langkah Instalasi

#### 1. Clone Repository
```bash
git clone https://github.com/aangwie/klinik.git
cd klinik
```

#### 2. Install Dependencies PHP
```bash
composer install
```

#### 3. Install Dependencies Node.js
```bash
npm install
```

#### 4. Konfigurasi Environment
```bash
copy .env.example .env
```
Kemudian edit file `.env` dan sesuaikan:
```env
APP_NAME=Klinik_Dokter
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=klinik
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Generate Application Key
```bash
php artisan key:generate
```

#### 6. Buat Database
Buat database MySQL dengan nama `klinik` (atau sesuai konfigurasi DB_DATABASE):
```sql
CREATE DATABASE klinik;
```

#### 7. Jalankan Migration & Seeder
```bash
php artisan migrate
php artisan db:seed
```

#### 8. Build Frontend Assets
```bash
npm run build
```

#### 9. Jalankan Aplikasi
```bash
php artisan serve
```
Akses di browser: `http://localhost:8000`

### Akun Default (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@klinik.test` | `password` |
| Pendaftaran | `pendaftaran@klinik.test` | `password` |
| Dokter | `dokter@klinik.test` | `password` |
| Kasir | `kasir@klinik.test` | `password` |
| Apoteker | `apoteker@klinik.test` | `password` |

---

## 🧑‍💻 Alur Penggunaan Aplikasi

```
Pasien datang → Pendaftaran (input/create pasien + antrean)
       ↓
  Antrean (status: menunggu)
       ↓
  Dokter panggil (status: dipanggil)
       ↓
  Pemeriksaan (diagnosa, tindakan, resep obat) → Status: selesai
       ↓                          ↕
  Kasir (Pembayaran)         Apoteker (Serah obat)
       ↓
  Selesai
```

---

## 📄 Lisensi

Aplikasi ini dikembangkan untuk keperluan praktik dokter umum / klinik. Dibangun dengan [Laravel](https://laravel.com) (framework open-source under [MIT license](https://opensource.org/licenses/MIT)).