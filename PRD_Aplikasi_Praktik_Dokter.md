# PRD - Aplikasi Praktik Dokter Umum

## Ringkasan
Aplikasi manajemen praktik dokter umum berbasis **CodeIgniter 4**, **MySQL**, dan **Tailwind CSS** dengan tema **Modern Light Green**.

Tujuan aplikasi adalah mengelola proses pelayanan pasien dari pendaftaran hingga pengambilan obat secara terintegrasi.

---

# Teknologi

- Framework: Laravel 12
- Database: MySQL 8
- Frontend: Tailwind CSS
- Authentication: Role Based Access Control (RBAC)

---

# Role Pengguna

## Admin
- Mengelola seluruh data sistem

## Petugas Pendaftaran
- Registrasi pasien
- Mengelola antrean

## Dokter
- Pemeriksaan pasien
- Diagnosa
- Resep obat

## Kasir
- Pembayaran jasa dokter
- Pembayaran obat

## Apoteker
- Menyiapkan dan menyerahkan obat

## Owner
- Melihat laporan dan dashboard

---

# Alur Utama

Pasien Datang
→ Pendaftaran
→ Pemeriksaan Dokter
→ Pembayaran Jasa Dokter
→ Pengambilan Obat
→ Pembayaran Obat
→ Selesai

---

# Modul 1 - Pendaftaran Pasien

## Pasien Baru

Data yang diinput:

- NIK
- Nama Lengkap
- Tanggal Lahir
- Jenis Kelamin
- Nomor HP
- Alamat
- Pekerjaan

Sistem membuat:

- Nomor Rekam Medis otomatis
- Nomor Antrean otomatis

Contoh:

RM202600001

A001

## Pasien Lama

Pencarian berdasarkan:

- Nomor Rekam Medis
- NIK
- Nama

Output:

- Nomor antrean
- Status: Menunggu Dokter

---

# Modul 2 - Pemeriksaan Dokter

## Data Pemeriksaan

### Keluhan
Keluhan utama pasien.

### Tanda Vital

- Tekanan darah
- Berat badan
- Tinggi badan
- Suhu tubuh
- Nadi

### Diagnosa

Input diagnosa penyakit.

### Tindakan

Contoh:

- Konsultasi
- Injeksi
- Nebulizer
- Rawat luka

### Resep Obat

Dokter dapat menambahkan:

- Nama obat
- Jumlah
- Aturan pakai

Output:

Status berubah menjadi:

Menunggu Pembayaran

---

# Modul 3 - Pembayaran Jasa Dokter

Kasir melihat:

- Biaya konsultasi
- Biaya tindakan

Contoh:

- Konsultasi Rp50.000
- Nebulizer Rp30.000

Total otomatis dihitung.

## Metode Pembayaran

- Tunai
- QRIS
- Transfer
- Debit

Output:

- Invoice
- Struk pembayaran

Status:

Lunas Jasa Dokter

---

# Modul 4 - Apotek

Apoteker melihat resep dari dokter.

Proses:

1. Verifikasi resep
2. Menyiapkan obat
3. Menghitung total harga obat

Output:

Status:

Menunggu Pembayaran Obat

---

# Modul 5 - Pembayaran Obat

Kasir melihat:

- Daftar obat
- Jumlah
- Harga
- Total

Metode pembayaran:

- Tunai
- QRIS
- Transfer
- Debit

Output:

- Struk obat
- Status Selesai

---

# Modul Master Obat

Data obat:

- Kode Obat
- Nama Obat
- Kategori
- Satuan
- Stok
- Harga Beli
- Harga Jual
- Expired Date

Fitur:

- Tambah
- Edit
- Hapus
- Import Excel
- Export Excel

Stok berkurang otomatis saat obat diserahkan.

---

# Rekam Medis

Riwayat pasien:

- Kunjungan
- Diagnosa
- Tindakan
- Resep
- Dokter

Fitur:

- Detail kunjungan
- Cetak PDF

---

# Dashboard

Menampilkan:

- Jumlah pasien hari ini
- Pendapatan hari ini
- Obat terlaris
- Pasien baru
- Grafik kunjungan

---

# Laporan

## Harian

- Kunjungan pasien
- Pendapatan

## Bulanan

- Pendapatan jasa dokter
- Penjualan obat

## Tahunan

- Grafik pendapatan
- Statistik pasien

---

# Struktur Database Utama

## users
- id
- name
- username
- password
- role

## patients
- id
- medical_record_number
- nik
- name
- phone
- address

## queues
- id
- queue_number
- patient_id
- status

## examinations
- id
- patient_id
- doctor_id
- complaint
- diagnosis
- notes

## prescriptions
- id
- examination_id
- medicine_id
- qty
- instruction

## medicines
- id
- code
- name
- stock
- purchase_price
- selling_price

## doctor_payments
- id
- patient_id
- total
- payment_method

## pharmacy_sales
- id
- patient_id
- total
- payment_method

---

# Target MVP

Versi pertama wajib memiliki:

- Login & Hak Akses
- Pendaftaran Pasien
- Antrean Pasien
- Pemeriksaan Dokter
- Resep Obat
- Pembayaran Jasa Dokter
- Pembayaran Obat
- Manajemen Obat
- Rekam Medis
- Dashboard
- Laporan
