-- Skema database Aplikasi Presensi Siswa (MariaDB)
CREATE DATABASE IF NOT EXISTS presensi_siswa
  CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE presensi_siswa;

CREATE TABLE IF NOT EXISTS kelas (
  id_kelas   INT AUTO_INCREMENT PRIMARY KEY,
  nama_kelas VARCHAR(20) NOT NULL UNIQUE,
  jurusan    VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS siswa (
  nisn     CHAR(10) PRIMARY KEY,
  nama     VARCHAR(100) NOT NULL,
  alamat   VARCHAR(255) NULL,
  id_kelas INT NOT NULL,
  CONSTRAINT fk_siswa_kelas FOREIGN KEY (id_kelas)
    REFERENCES kelas (id_kelas) ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS presensi (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  nisn          CHAR(10) NOT NULL,
  tanggal_waktu DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_presensi_siswa FOREIGN KEY (nisn)
    REFERENCES siswa (nisn) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

-- Tabel pendukung login (berdiri sendiri, tidak berelasi)
CREATE TABLE IF NOT EXISTS users (
  id_user  INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL
) ENGINE=InnoDB;
