
-- ============================================================
-- Bawaan 05
-- ============================================================

-- ============================================================
--  WebGIS Pengentasan Kemiskinan  |  Database: uas_06
--  Jalankan sekali untuk setup awal
-- ============================================================




SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `log_aktivitas`;
DROP TABLE IF EXISTS `pesan`;
DROP TABLE IF EXISTS `histori_bantuan`;
DROP TABLE IF EXISTS `bantuan`;
DROP TABLE IF EXISTS `laporan`;
DROP TABLE IF EXISTS `pelatihan`;
DROP TABLE IF EXISTS `penduduk`;
DROP TABLE IF EXISTS `keluarga`;
DROP TABLE IF EXISTS `rumah_ibadah`;
DROP TABLE IF EXISTS `user`;
DROP TABLE IF EXISTS `role`;
SET FOREIGN_KEY_CHECKS = 1;

-- ── 1. ROLE ─────────────────────────────────────────────────
CREATE TABLE role (
  id       INT AUTO_INCREMENT PRIMARY KEY,
  nama     VARCHAR(50) NOT NULL,
  deskripsi TEXT
) ENGINE=InnoDB;

INSERT INTO role (nama, deskripsi) VALUES
('masyarakat', 'Hanya dapat melihat data dan membuat laporan'),
('pengurus',   'Input & update data penduduk, verifikasi laporan, salurkan bantuan'),
('pimpinan',   'Read-only, monitoring dashboard');

-- ── 2. USER ─────────────────────────────────────────────────
CREATE TABLE user (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(100) NOT NULL,
  email      VARCHAR(100) NOT NULL UNIQUE,
  password   VARCHAR(255) NOT NULL,
  role_id    INT NOT NULL DEFAULT 1,
  aktif      TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES role(id)
) ENGINE=InnoDB;

-- Password untuk semua akun demo: "password"
INSERT INTO user (nama, email, password, role_id) VALUES
('Admin Utama',      'admin@uas.id',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2),
('Pimpinan Daerah',  'pimpinan@uas.id', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 3),
('Warga Biasa',      'warga@uas.id',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1),
('Pengurus Masjid',  'pengurus2@uas.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 2);

-- ── 3. RUMAH IBADAH ─────────────────────────────────────────
CREATE TABLE rumah_ibadah (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(150) NOT NULL,
  jenis      ENUM('masjid','gereja','pura','vihara','klenteng','lainnya') DEFAULT 'masjid',
  kontak     VARCHAR(20),
  alamat     VARCHAR(255),
  lat        DOUBLE NOT NULL,
  lng        DOUBLE NOT NULL,
  radius     DOUBLE DEFAULT 500,
  user_id    INT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO rumah_ibadah (nama, jenis, kontak, alamat, lat, lng, radius, user_id) VALUES
('Masjid Al-Ikhlas',   'masjid', '081234567890', 'Jl. Merdeka No.1, Pontianak Kota',    0.0269, 109.3425, 600, 1),
('Gereja Santo Yosef', 'gereja', '081298765432', 'Jl. Veteran No.5, Pontianak Selatan', 0.0310, 109.3300, 500, 1),
('Masjid Al-Furqon',   'masjid', '085678901234', 'Jl. Sudirman No.10, Pontianak Barat', 0.0200, 109.3500, 550, 4);

-- ── 4. KELUARGA (Kartu Keluarga) ────────────────────────────
CREATE TABLE keluarga (
  id              INT AUTO_INCREMENT PRIMARY KEY,
  no_kk           VARCHAR(20) NOT NULL UNIQUE,
  alamat          VARCHAR(255),
  rt              VARCHAR(5),
  rw              VARCHAR(5),
  kelurahan       VARCHAR(100),
  kecamatan       VARCHAR(100),
  lat             DOUBLE,
  lng             DOUBLE,
  rumah_ibadah_id INT,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (rumah_ibadah_id) REFERENCES rumah_ibadah(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO keluarga (no_kk, alamat, rt, rw, kelurahan, kecamatan, lat, lng, rumah_ibadah_id) VALUES
('6171010101010001','Jl. Dahlia No.3',  '001','001','Sungai Bangkong','Pontianak Kota',   0.0250,109.3410,1),
('6171010101010002','Jl. Melati No.7',  '002','001','Sungai Bangkong','Pontianak Kota',   0.0280,109.3440,1),
('6171010101010003','Jl. Kenanga No.12','001','002','Benua Melayu',   'Pontianak Selatan',0.0215,109.3480,2),
('6171010101010004','Jl. Mawar No.5',   '003','002','Akcaya',         'Pontianak Selatan',0.0195,109.3460,2),
('6171010101010005','Jl. Anggrek No.8', '001','003','Bansir Laut',    'Pontianak Tenggara',0.0175,109.3510,3);

-- ── 5. PENDUDUK ──────────────────────────────────────────────
CREATE TABLE penduduk (
  id                  INT AUTO_INCREMENT PRIMARY KEY,
  nik                 VARCHAR(16) NOT NULL UNIQUE,
  nama                VARCHAR(150) NOT NULL,
  jenis_kelamin       ENUM('L','P') NOT NULL DEFAULT 'L',
  tanggal_lahir       DATE NOT NULL,
  status_keluarga     ENUM('kepala_keluarga','anggota') DEFAULT 'anggota',
  status_perkawinan   ENUM('belum_kawin','kawin','cerai_hidup','cerai_mati') DEFAULT 'belum_kawin',
  status_hidup        ENUM('hidup','meninggal') DEFAULT 'hidup',
  pekerjaan           VARCHAR(100),
  penghasilan         DECIMAL(15,2) DEFAULT 0,
  status_ekonomi      ENUM('miskin','rentan','mampu') DEFAULT 'rentan',
  pendidikan_terakhir ENUM('tidak_sekolah','SD','SMP','SMA','D3','S1','S2','S3') DEFAULT 'tidak_sekolah',
  status_pendidikan   ENUM('sekolah','tidak_sekolah','lulus') DEFAULT 'tidak_sekolah',
  keluarga_id         INT,
  rumah_ibadah_id     INT,
  lat                 DOUBLE,
  lng                 DOUBLE,
  no_hp               VARCHAR(20),
  jenis_bantuan       VARCHAR(255),
  foto                VARCHAR(255),
  created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (keluarga_id)     REFERENCES keluarga(id) ON DELETE SET NULL,
  FOREIGN KEY (rumah_ibadah_id) REFERENCES rumah_ibadah(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO penduduk (nik,nama,jenis_kelamin,tanggal_lahir,status_keluarga,status_perkawinan,pekerjaan,penghasilan,status_ekonomi,pendidikan_terakhir,status_pendidikan,keluarga_id,rumah_ibadah_id,lat,lng,no_hp,jenis_bantuan) VALUES
('6171010101010001','Ahmad Fauzi',   'L','1985-03-15','kepala_keluarga','kawin',     'Buruh Harian',   800000, 'miskin','SD', 'lulus',   1,1,0.02500,109.34100,'081234567891','sembako'),
('6171010101010002','Siti Aminah',   'P','1988-07-20','anggota',        'kawin',     'Ibu RT',         0,      'miskin','SMP','lulus',   1,1,0.02510,109.34110,NULL,          'sembako'),
('6171010101010003','Budi Santoso',  'L','2012-01-10','anggota',        'belum_kawin','Pelajar',       0,      'miskin','SMP','sekolah', 1,1,0.02520,109.34090,NULL,          NULL),
('6171010101010004','Dewi Rahayu',   'P','1979-11-05','kepala_keluarga','cerai_mati','Pedagang',       1500000,'rentan','SMA','lulus',   2,1,0.02800,109.34400,'085678901235','kesehatan'),
('6171010101010005','Rizky Pratama', 'L','2008-06-22','anggota',        'belum_kawin','Pelajar',       0,      'rentan','SMP','sekolah', 2,1,0.02790,109.34420,NULL,          NULL),
('6171010101010006','Hendra Wijaya', 'L','1992-09-30','kepala_keluarga','kawin',     'Wiraswasta',     3000000,'mampu', 'S1', 'lulus',   3,2,0.02150,109.34800,'087890123456',NULL),
('6171010101010007','Lina Marlina',  'P','1995-04-18','anggota',        'kawin',     'Guru Honorer',   1800000,'rentan','S1', 'lulus',   3,2,0.02155,109.34810,'081122334455','pendidikan'),
('6171010101010008','Samsul Bahri',  'L','1970-08-25','kepala_keluarga','kawin',     'Nelayan',        600000, 'miskin','SD', 'lulus',   4,2,0.01950,109.34600,'089988776655','sembako'),
('6171010101010009','Aisyah Putri',  'P','1975-12-30','anggota',        'kawin',     'Ibu RT',         0,      'miskin','SD', 'lulus',   4,2,0.01960,109.34590,NULL,          NULL),
('6171010101010010','Muhammad Yusuf','L','2005-09-15','anggota',        'belum_kawin','Pelajar',       0,      'miskin','SMA','sekolah', 4,2,0.01970,109.34580,NULL,          NULL),
('6171010101010011','Surya Darma',   'L','1988-02-14','kepala_keluarga','kawin',     'Tukang Ojek',    1200000,'rentan','SMP','lulus',   5,3,0.01750,109.35100,'082233445566','ekonomi'),
('6171010101010012','Ratna Sari',    'P','1990-06-08','anggota',        'kawin',     'Pedagang Kecil', 800000, 'rentan','SMA','lulus',   5,3,0.01760,109.35110,NULL,          NULL);

-- ── 6. PELATIHAN ────────────────────────────────────────────
CREATE TABLE pelatihan (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  penduduk_id   INT NOT NULL,
  nama_pelatihan VARCHAR(150),
  penyelenggara  VARCHAR(100),
  tahun         YEAR,
  keterangan    TEXT,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (penduduk_id) REFERENCES penduduk(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO pelatihan (penduduk_id, nama_pelatihan, penyelenggara, tahun) VALUES
(1,'Pelatihan Menjahit','Dinas Tenaga Kerja',2023),
(4,'Pelatihan UMKM','Kementerian Koperasi',2022),
(11,'Pelatihan Ojek Online','Grab/Gojek',2023);

-- ── 7. BANTUAN (Master) ─────────────────────────────────────
CREATE TABLE bantuan (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nama       VARCHAR(150) NOT NULL,
  jenis      ENUM('sembako','pendidikan','kesehatan','ekonomi','perumahan','lainnya') DEFAULT 'sembako',
  sumber     VARCHAR(150),
  bentuk     VARCHAR(255),
  keterangan TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO bantuan (nama, jenis, sumber, bentuk) VALUES
('Sembako Bulanan',     'sembako',    'Rumah Ibadah',     '10kg beras + minyak goreng 1L + gula 1kg'),
('Beasiswa Pendidikan', 'pendidikan', 'Pemerintah Daerah','Rp 500.000/bulan'),
('BPJS Kesehatan',      'kesehatan',  'Pemerintah Pusat', 'Kartu BPJS PBI gratis'),
('Modal Usaha Mikro',   'ekonomi',    'Donatur/CSR',      'Rp 1.000.000 – Rp 5.000.000 sekali'),
('Perbaikan Rumah',     'perumahan',  'Pemerintah Daerah','Material bangunan senilai Rp 10.000.000');

-- ── 8. HISTORI BANTUAN ──────────────────────────────────────
CREATE TABLE histori_bantuan (
  id               INT AUTO_INCREMENT PRIMARY KEY,
  penduduk_id      INT NOT NULL,
  bantuan_id       INT NOT NULL,
  tanggal          DATE NOT NULL,
  jumlah           VARCHAR(100),
  status           ENUM('direncanakan','disalurkan','ditolak') DEFAULT 'direncanakan',
  disalurkan_oleh  INT,
  keterangan       TEXT,
  created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (penduduk_id)     REFERENCES penduduk(id) ON DELETE CASCADE,
  FOREIGN KEY (bantuan_id)      REFERENCES bantuan(id) ON DELETE CASCADE,
  FOREIGN KEY (disalurkan_oleh) REFERENCES user(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO histori_bantuan (penduduk_id,bantuan_id,tanggal,jumlah,status,disalurkan_oleh,keterangan) VALUES
(1,1,'2024-01-10','10kg beras + minyak 1L','disalurkan',1,'Penyaluran Januari 2024'),
(1,3,'2024-02-01','1 kartu BPJS',          'disalurkan',1,'Didaftarkan BPJS PBI'),
(2,1,'2024-01-10','10kg beras + minyak 1L','disalurkan',1,'Penyaluran Januari 2024'),
(4,2,'2024-03-01','Rp 500.000',            'disalurkan',1,'Beasiswa anak sekolah'),
(8,1,'2024-01-15','10kg beras',            'disalurkan',4,'Bantuan Masjid Al-Furqon'),
(9,3,'2024-02-15','1 kartu BPJS',          'disalurkan',4,'BPJS untuk ibu'),
(11,4,'2024-04-01','Rp 2.000.000',         'disalurkan',4,'Modal usaha ojek online');

-- ── 9. LAPORAN MASYARAKAT ────────────────────────────────────
CREATE TABLE laporan (
  id                INT AUTO_INCREMENT PRIMARY KEY,
  pelapor           VARCHAR(100),
  penduduk_id       INT,
  deskripsi         TEXT NOT NULL,
  lat               DOUBLE,
  lng               DOUBLE,
  foto              VARCHAR(255),
  urgensi           ENUM('rendah','sedang','tinggi','darurat') DEFAULT 'sedang',
  status            ENUM('pending','diverifikasi','diproses','selesai') DEFAULT 'pending',
  diverifikasi_oleh INT,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (penduduk_id)        REFERENCES penduduk(id) ON DELETE SET NULL,
  FOREIGN KEY (diverifikasi_oleh)  REFERENCES user(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO laporan (pelapor,penduduk_id,deskripsi,lat,lng,urgensi,status) VALUES
('Anonim',         1,'Pak Ahmad sakit keras sudah seminggu dan tidak punya biaya berobat. Kondisi sangat memprihatinkan.',0.02505,109.34105,'darurat','pending'),
('Pak RT 02',      8,'Keluarga Pak Samsul di Jl. Mawar sudah 3 hari tidak makan. Sangat membutuhkan bantuan pangan segera.',0.01955,109.34605,'darurat','diverifikasi'),
('Bu Dewi',        NULL,'Ada anak putus sekolah di RT 003 karena orang tuanya tidak mampu bayar uang gedung.',0.02810,109.34410,'tinggi','pending'),
('Pengurus Masjid',10,'Muhammad Yusuf butuh beasiswa lanjut SMA, keluarga tidak mampu.',0.01970,109.34580,'sedang','diproses'),
('Warga RT 001',   NULL,'Jalan di Jl. Anggrek rusak parah, bahaya bagi warga lansia.',0.01760,109.35115,'rendah','pending');

-- ── 10. LOG AKTIVITAS ────────────────────────────────────────
CREATE TABLE log_aktivitas (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  user_id    INT,
  aksi       VARCHAR(50),
  tabel      VARCHAR(50),
  data_id    INT,
  keterangan TEXT,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO log_aktivitas (user_id,aksi,tabel,data_id,keterangan) VALUES
(1,'LOGIN','user',1,'Login pertama kali'),
(1,'CREATE','histori_bantuan',1,'Salurkan sembako untuk Ahmad Fauzi'),
(4,'CREATE','histori_bantuan',5,'Salurkan sembako untuk Samsul Bahri'),
(1,'UPDATE','laporan',2,'Verifikasi laporan darurat');

-- ── 11. PESAN / CHAT ────────────────────────────────────────
CREATE TABLE pesan (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  dari_user  INT NOT NULL,
  ke_user    INT,
  isi        TEXT NOT NULL,
  dibaca     TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (dari_user) REFERENCES user(id) ON DELETE CASCADE,
  FOREIGN KEY (ke_user)   REFERENCES user(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO pesan (dari_user,ke_user,isi,dibaca) VALUES
(1,NULL,'Selamat datang di sistem WebGIS Kemiskinan UAS 06. Semua pengurus harap aktif memperbarui data.',1),
(4,NULL,'Sudah ada 3 laporan darurat baru masuk. Mohon segera ditindaklanjuti.',0),
(1,NULL,'Penyaluran bantuan bulan Januari 2024 sudah selesai untuk wilayah Pontianak Kota.',1);

-- ── 12. VIEWS ───────────────────────────────────────────────
CREATE OR REPLACE VIEW v_penduduk AS
SELECT
  p.*,
  TIMESTAMPDIFF(YEAR,p.tanggal_lahir,CURDATE()) AS umur,
  k.no_kk, k.rt, k.rw, k.kelurahan, k.kecamatan,
  ri.nama AS nama_ibadah, ri.jenis AS jenis_ibadah,
  (SELECT COUNT(*) FROM histori_bantuan hb WHERE hb.penduduk_id=p.id AND hb.status='disalurkan') AS total_bantuan_diterima
FROM penduduk p
LEFT JOIN keluarga k ON p.keluarga_id=k.id
LEFT JOIN rumah_ibadah ri ON p.rumah_ibadah_id=ri.id;

CREATE OR REPLACE VIEW v_statistik_ibadah AS
SELECT
  ri.id, ri.nama, ri.jenis,
  COUNT(p.id) AS total_penduduk,
  SUM(p.status_ekonomi='miskin') AS total_miskin,
  SUM(p.status_ekonomi='rentan') AS total_rentan,
  SUM(p.status_ekonomi='mampu')  AS total_mampu
FROM rumah_ibadah ri
LEFT JOIN penduduk p ON p.rumah_ibadah_id=ri.id AND p.status_hidup='hidup'
GROUP BY ri.id;

-- ── SELESAI ──────────────────────────────────────────────────
-- ============================================================
-- Bawaan 02
-- ============================================================




CREATE TABLE jalan(
 id INT AUTO_INCREMENT PRIMARY KEY,
 nama_jalan VARCHAR(150),
 status VARCHAR(50),
 panjang DOUBLE,
 geom LINESTRING NOT NULL,
 SPATIAL INDEX idx_jalan(geom)
);

CREATE TABLE parsil(
 id INT AUTO_INCREMENT PRIMARY KEY,
 pemilik VARCHAR(100),
 status VARCHAR(50),
 luas DOUBLE,
 geom POLYGON NOT NULL,
 SPATIAL INDEX idx_parsil(geom)
);

CREATE TABLE jalan_rusak(
 id INT AUTO_INCREMENT PRIMARY KEY,
 nama_titik VARCHAR(100),
 keterangan TEXT,
 geom POINT NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 SPATIAL INDEX idx_rusak(geom)
);


-- ============================================================
-- Bawaan 01
-- ============================================================

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Jun 2026 pada 10.53
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12




SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_spbu`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `spbu`
--

CREATE TABLE `spbu_01` (
  `id` int(11) NOT NULL,
  `nama_spbu` varchar(255) DEFAULT NULL,
  `lat` double DEFAULT NULL,
  `lng` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `spbu`
--

INSERT INTO `spbu_01` (`id`, `nama_spbu`, `lat`, `lng`) VALUES
(1, 'spbu kobar', -0.02197187355969219, 109.31207858442205),
(3, 'spbu paris', 0.007368924759898348, 109.37010012983221),
(4, 'spbu tanray', 0.016651739943910275, 109.29988329640928);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `spbu`
--
ALTER TABLE `spbu_01`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `spbu`
--
ALTER TABLE `spbu_01`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


-- ============================================================
-- Bawaan 04
-- ============================================================

-- =============================================
-- DATABASE: db_24jam
-- =============================================





-- ---------------------------------------------
-- Tabel: spbu
-- ---------------------------------------------

CREATE TABLE IF NOT EXISTS `spbu_04` (
  `id`        INT(11)      NOT NULL AUTO_INCREMENT,
  `nama_spbu` VARCHAR(100) NOT NULL,
  `no_wa`     VARCHAR(20)  NOT NULL,
  `status`    ENUM('yes','no') NOT NULL DEFAULT 'no',
  `latitude`  DOUBLE       NOT NULL,
  `longitude` DOUBLE       NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


