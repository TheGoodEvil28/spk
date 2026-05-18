-- =============================================
-- SPK INVENTARIS DATABASE
-- =============================================
CREATE DATABASE IF NOT EXISTS spk_inventaris;
USE spk_inventaris;

-- =============================================
-- DROP TABLES (urutan benar)
-- =============================================
DROP TABLE IF EXISTS laporan;
DROP TABLE IF EXISTS hasil;
DROP TABLE IF EXISTS matriks_keputusan_detail;
DROP TABLE IF EXISTS matriks_keputusan;
DROP TABLE IF EXISTS kriteria;
DROP TABLE IF EXISTS barang;
DROP TABLE IF EXISTS users;

-- =============================================
-- TABLE: users (untuk login & register)
-- =============================================
CREATE TABLE users (
  id_user INT AUTO_INCREMENT PRIMARY KEY,
  nama_lengkap VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =============================================
-- Insert admin default (password: 12345678)
-- gunakan password_hash('12345678', PASSWORD_DEFAULT) = $2y$10$... 
-- Saya sertakan hash untuk "12345678"
-- =============================================
INSERT INTO users (nama_lengkap, email, password) VALUES
('Admin SPK', 'admin@ub.ac.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- =============================================
-- TABLE: barang
-- =============================================
CREATE TABLE barang (
  id_barang INT AUTO_INCREMENT PRIMARY KEY,
  nama_barang VARCHAR(100) NOT NULL,
  stok_tersedia INT NOT NULL DEFAULT 0,
  stok_minimum INT NOT NULL DEFAULT 0,
  tgl_beli DATE,
  usia_pakai_bulan INT DEFAULT 0,
  status_garansi ENUM('Aktif','Tidak Aktif','Hampir Habis') DEFAULT 'Tidak Aktif',
  spesifikasi TEXT
) ENGINE=InnoDB;

-- =============================================
-- TABLE: kriteria
-- =============================================
CREATE TABLE kriteria (
  id_kriteria INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(50) NOT NULL,
  bobot FLOAT NOT NULL,
  tipe ENUM('Benefit','Cost') NOT NULL
) ENGINE=InnoDB;

-- =============================================
-- TABLE: matriks_keputusan
-- =============================================
CREATE TABLE matriks_keputusan (
  id_matriks INT AUTO_INCREMENT PRIMARY KEY,
  nama_matriks VARCHAR(100),
  metode VARCHAR(20)
) ENGINE=InnoDB;

-- =============================================
-- TABLE: matriks_keputusan_detail
-- =============================================
CREATE TABLE matriks_keputusan_detail (
  id_detail INT AUTO_INCREMENT PRIMARY KEY,
  id_matriks INT,
  id_barang INT,
  id_kriteria INT,
  nilai FLOAT NOT NULL,
  FOREIGN KEY (id_matriks) REFERENCES matriks_keputusan(id_matriks) ON DELETE CASCADE,
  FOREIGN KEY (id_barang) REFERENCES barang(id_barang) ON DELETE CASCADE,
  FOREIGN KEY (id_kriteria) REFERENCES kriteria(id_kriteria) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- TABLE: hasil
-- =============================================
CREATE TABLE hasil (
  id_hasil INT AUTO_INCREMENT PRIMARY KEY,
  id_matriks INT,
  id_barang INT,
  nama_barang VARCHAR(100),
  skor FLOAT NOT NULL,
  nilai_s FLOAT DEFAULT 0,
  nilai_v FLOAT DEFAULT 0,
  nilai_yi FLOAT DEFAULT 0,
  nilai_vi_saw FLOAT DEFAULT 0,
  nilai_vi_topsis FLOAT DEFAULT 0,
  rank_moora INT DEFAULT 0,
  rank_saw INT DEFAULT 0,
  rank_topsis INT DEFAULT 0,
  consensus_rank INT DEFAULT 0,
  ranking INT NOT NULL,
  rekomendasi VARCHAR(30),
  FOREIGN KEY (id_matriks) REFERENCES matriks_keputusan(id_matriks) ON DELETE CASCADE,
  FOREIGN KEY (id_barang) REFERENCES barang(id_barang) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- TABLE: laporan
-- =============================================
CREATE TABLE laporan (
  id_laporan INT AUTO_INCREMENT PRIMARY KEY,
  id_matriks INT,
  tgl_laporan DATE NOT NULL,
  metode_digunakan VARCHAR(50) NOT NULL,
  daftar_rekomendasi TEXT,
  FOREIGN KEY (id_matriks) REFERENCES matriks_keputusan(id_matriks) ON DELETE CASCADE
) ENGINE=InnoDB;

-- =============================================
-- DATA AWAL (contoh)
-- =============================================
INSERT INTO barang 
(nama_barang, stok_tersedia, stok_minimum, tgl_beli, status_garansi, spesifikasi) 
VALUES
('Laptop ASUS ROG', 5, 10, '2023-01-15', 'Aktif', 'Intel i7, 16GB RAM'),
('Monitor Dell 24"', 15, 20, '2023-06-20', 'Aktif', 'IPS Panel, 1080p'),
('Keyboard Mechanical', 8, 15, '2023-03-10', 'Hampir Habis', 'Cherry MX Blue'),
('Mouse Wireless', 25, 20, '2023-08-01', 'Aktif', '2.4GHz, 1000 DPI'),
('Printer Epson', 3, 5, '2023-02-28', 'Aktif', 'All-in-One');

INSERT INTO kriteria (nama, bobot, tipe) VALUES
('Stok Tersedia', 0.25, 'Benefit'),
('Rasio Kelangkaan', 0.20, 'Cost'),
('Usia Pakai', 0.15, 'Cost'),
('Skor Garansi', 0.25, 'Benefit'),
('Kesesuaian Spesifikasi', 0.15, 'Benefit');

INSERT INTO matriks_keputusan (nama_matriks, metode) VALUES ('Matriks November 2024', 'WP');

INSERT INTO matriks_keputusan_detail 
(id_matriks, id_barang, id_kriteria, nilai) VALUES
(1,1,1,5),(1,1,2,0.75),(1,1,3,22),(1,1,4,100),(1,1,5,85);

INSERT INTO hasil 
(id_matriks, id_barang, nama_barang, skor, ranking, rekomendasi) 
VALUES
(1, 4, 'Mouse Wireless', 0.92, 1, 'Sangat Direkomendasikan');

INSERT INTO laporan 
(id_matriks, tgl_laporan, metode_digunakan, daftar_rekomendasi) 
VALUES
(1, '2024-11-15', 'Weighted Product (WP)', 'Mouse Wireless');