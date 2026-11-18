# Aplikasi Inventaris Sarana dan Prasarana SMK
### Uji Kompetensi Keahlian (UKK) Rekayasa Perangkat Lunak 2026/2027 - Paket 3

Aplikasi web manajemen inventaris sarana dan prasarana sekolah berbasis **PHP OOP**, **MySQL Stored Procedures**, dan antarmuka **Bootstrap 5** (offline-ready, responsif, dan estetis).

---

## Fitur Utama & Matriks Hak Akses

| Fitur | Administrator | Operator | Peminjam |
|---|:---:|:---:|:---:|
| Login / Logout | &check; | &check; | &cross; |
| Inventarisir (Master Sarana, Ruang, Jenis, Petugas) | &check; | &cross; | &cross; |
| Data Pegawai / Guru | &check; | &check; | &cross; |
| Data Siswa | &check; | &check; | &cross; |
| Peminjaman Sarana | &check; | &check; | Ajukan tanpa login |
| Pengembalian Sarana | &check; | &check; | &cross; |
| Generate Laporan (Cetak Kop Resmi & Rekapitulasi) | &check; | &check; | &cross; |

Pada daftar peminjaman dan pratinjau Generate Laporan, peminjam ditampilkan satu kali. Klik nama peminjam untuk melihat transaksi dan rincian kategori/barang yang dipinjam. Hasil cetak laporan juga mengelompokkan transaksi di bawah nama peminjam. Satu transaksi dapat memuat beberapa jenis sarana sekaligus.

---

## Teknologi yang Digunakan
1. **Bahasa Pemrograman**: PHP 8+ (Object-Oriented Programming, PDO Singleton)
2. **Basis Data**: MySQL / MariaDB (8 Tabel relasional + 4 Stored Procedure)
3. **Frontend UI**: Bootstrap 5.3 & Bootstrap Icons secara terpisah dalam file eksternal di folder `assets/css/` (`style.css`, `login.css`, `print.css`), 100% jalan tanpa internet offline.
4. **Web Server**: Apache (XAMPP)

---

## Akun Pengujian Default
- **Administrator**: username `admin` | password `admin123`
- **Operator**: username `operator` | password `operator123`
Peminjam dapat membuka halaman peminjaman dan mengajukan peminjaman tanpa login.

---

## Petunjuk Instalasi
1. Pastikan modul **Apache** dan **MySQL** aktif pada XAMPP Control Panel.
2. File basis data tersedia pada: `database.sql`.
   Import melalui phpMyAdmin atau MySQL CLI:
   ```bash
   mysql -u root < database.sql
   ```
3. Buka browser dan akses aplikasi melalui URL:
   `http://localhost/ujikom26/iventaris_sarana/`
4. Untuk dokumen laporan ujian dan lembar kerja, buka:
   `DOKUMENTASI_PROYEK.md`
