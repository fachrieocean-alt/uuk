# Aplikasi Presensi Siswa

Aplikasi web sederhana untuk mencatat presensi siswa SMK TI Airlangga Samarinda yang menempelkan kartu RFID di gerbang sekolah. Dibuat untuk Uji Unit Kompetensi PPLG Tahun Pelajaran 2026/2027 menggunakan **PHP native (PDO + OOP)** dan **MariaDB**, tanpa framework.

Setiap tap kartu mencatat satu baris presensi (siapa dan kapan). Siswa yang tidak tap dianggap tidak hadir, sehingga tidak ada kolom status kehadiran.

## Fitur

1. Login
2. Logout
3. Kelola Kelas (CRUD, data awal dari seeder)
4. Kelola Siswa (CRUD, validasi server-side)
5. Simulasi Tap Kartu (tambah presensi)
6. Rekap Presensi (lihat dan hapus log; log tidak dapat diubah)
7. Pencarian Siswa (nama atau NISN)
8. Pencarian dan Filter Presensi (nama/NISN, tanggal, kelas)

## Struktur Proyek

```
presensi-siswa/
├── schema.sql            # DDL database
├── seed.php              # seeder kelas, admin, contoh siswa
├── index.php             # pengarah ke login / rekap
├── login.php, logout.php
├── kelas.php, kelas_form.php
├── siswa.php, siswa_form.php
├── tap.php               # simulasi tap kartu
├── rekap.php             # rekap presensi
├── classes/
│   ├── Database.php      # PDO (ERRMODE_EXCEPTION)
│   ├── BaseModel.php     # abstract class, abstract getAll()
│   ├── Kelas.php, Siswa.php, Presensi.php   # extends BaseModel
│   └── User.php          # login
├── includes/
│   ├── auth.php          # session, proteksi login, helper e()
│   ├── header.php, navbar.php, footer.php
└── assets/style.css
```

## Cara Instalasi

Prasyarat: PHP 8.0+ (ekstensi PDO MySQL), MariaDB, dan web server (XAMPP/Laragon atau `php -S`).

1. Letakkan folder proyek di `htdocs` (XAMPP) atau jalankan server bawaan PHP.
2. Impor skema database:
   ```bash
   mysql -u root -p < schema.sql
   ```
3. Sesuaikan kredensial database bila perlu di `classes/Database.php` (bawaan: host `localhost`, user `root`, tanpa password, database `presensi_siswa`).
4. Jalankan seeder satu kali:
   ```bash
   php seed.php
   ```
5. Jalankan aplikasi:
   ```bash
   php -S localhost:8000
   ```
   lalu buka <http://localhost:8000>.

## Akun Demo

| Username | Password |
|----------|----------|
| admin    | admin123 |

NISN contoh dari seeder: `0012345678` (Andi Pratama), `0098765432` (Siti Aisyah), `1234567890` (Budi Santoso), semuanya di kelas X PPLG 1.

## ERD (Notasi Chen)

Satu kelas memiliki banyak siswa (anggota, 1 : N), satu siswa memiliki banyak presensi (melakukan, 1 : N). Tabel `users` berdiri sendiri untuk login.

```
 (id_kelas)            (nisn)                 (id)
     |                    |                     |
  [ kelas ] 1 --<anggota>-- N [ siswa ] 1 --<melakukan>-- N [ presensi ]
   /      \                 /      \                          |
(nama_kelas)(jurusan)    (nama)  (alamat)                (tanggal_waktu)
```

### Struktur Tabel

| Tabel | Kolom |
|-------|-------|
| kelas | id_kelas (PK, INT AUTO_INCREMENT), nama_kelas (VARCHAR(20), NOT NULL, UNIQUE), jurusan (VARCHAR(50), NOT NULL) |
| siswa | nisn (PK, CHAR(10)), nama (VARCHAR(100), NOT NULL), alamat (VARCHAR(255), NULL), id_kelas (FK, INT, NOT NULL) |
| presensi | id (PK, INT AUTO_INCREMENT), nisn (FK, CHAR(10), NOT NULL), tanggal_waktu (DATETIME, NOT NULL, DEFAULT CURRENT_TIMESTAMP) |
| users | id_user (PK, INT AUTO_INCREMENT), username (VARCHAR(50), NOT NULL, UNIQUE), password (VARCHAR(255), NOT NULL) |

Relasi: `siswa.id_kelas` → `kelas.id_kelas` (ON DELETE RESTRICT), `presensi.nisn` → `siswa.nisn` (ON DELETE CASCADE).

## Ketentuan Teknis yang Dipenuhi

- **Database**: DDL lengkap dengan PK, FK, NOT NULL, UNIQUE, AUTO_INCREMENT, DEFAULT CURRENT_TIMESTAMP di `schema.sql`.
- **Koneksi**: PDO `ERRMODE_EXCEPTION` dalam class `Database` dengan constructor.
- **Query**: seluruh query yang memakai input pengguna memakai prepared statement.
- **OOP**: `BaseModel` (abstract, menyimpan PDO, abstract `getAll()`); `Kelas`, `Siswa`, `Presensi` mewarisi `BaseModel` dengan constructor; properti `private` diakses via getter/setter; class `User` untuk login.
- **Session**: menyimpan `id_user` dan `username`; semua halaman selain login diproteksi `require_login()`; logout menghapus session.
- **Layouting**: `header.php`, `navbar.php`, `footer.php` dipakai di semua halaman.
- **Validasi**: server-side, pesan per kolom, isian form tidak hilang saat gagal.
- **Keamanan dasar**: `password_hash` / `password_verify`; output di-escape dengan `htmlspecialchars` (fungsi `e()`).

## Hasil Pengujian

Isi kolom **Hasil** dan **Bukti** setelah menjalankan aplikasi di komputer Anda, lalu tempel tangkapan layar ke folder `screenshots/`.

| No | Skenario Uji | Hasil yang Diharapkan | Hasil | Bukti |
|----|--------------|-----------------------|-------|-------|
| 1 | Login dengan password salah | Gagal, muncul pesan kesalahan | ☐ Berhasil / ☐ Gagal | screenshots/01-login-salah.png |
| 2 | Buka halaman tanpa login lewat URL langsung | Diarahkan ke halaman login | ☐ Berhasil / ☐ Gagal | screenshots/02-tanpa-login.png |
| 3 | Tambah siswa dengan NISN 9 digit | Validasi gagal, pesan muncul, isian tidak hilang | ☐ Berhasil / ☐ Gagal | screenshots/03-nisn-9-digit.png |
| 4 | Tambah siswa dengan NISN yang sudah ada | Ditolak dengan pesan NISN sudah terdaftar | ☐ Berhasil / ☐ Gagal | screenshots/04-nisn-duplikat.png |
| 5 | Simulasi tap dengan NISN yang tidak terdaftar | Ditolak dengan pesan yang jelas | ☐ Berhasil / ☐ Gagal | screenshots/05-tap-tidak-terdaftar.png |
| 6 | Simulasi tap dengan NISN valid | Muncul di rekap hari ini, dengan waktu terisi otomatis | ☐ Berhasil / ☐ Gagal | screenshots/06-tap-valid.png |
| 7 | Hapus kelas yang masih memiliki siswa | Ditolak dengan pesan yang mudah dipahami | ☐ Berhasil / ☐ Gagal | screenshots/07-hapus-kelas.png |
| 8 | Cari siswa berdasarkan sebagian nama | Hanya siswa yang sesuai yang tampil | ☐ Berhasil / ☐ Gagal | screenshots/08-cari-siswa.png |
| 9 | Tampilan aplikasi pada smartphone | Tampilan responsif dan terbaca | ☐ Berhasil / ☐ Gagal | screenshots/09-smartphone.png |

## Tangkapan Layar Halaman Utama

Tambahkan ke folder `screenshots/`: login, rekap presensi, simulasi tap, kelola kelas, kelola siswa.
