-- Database: db_inventaris_sarana
-- Aplikasi Inventaris Sarana dan Prasarana

CREATE DATABASE IF NOT EXISTS `db_inventaris_sarana` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `db_inventaris_sarana`;

-- ----------------------------------------------------------
-- 1. Tabel level
-- ----------------------------------------------------------
DROP TABLE IF EXISTS `detail_pinjam`;
DROP TABLE IF EXISTS `peminjaman`;
DROP TABLE IF EXISTS `inventaris`;
DROP TABLE IF EXISTS `ruang`;
DROP TABLE IF EXISTS `jenis`;
DROP TABLE IF EXISTS `petugas`;
DROP TABLE IF EXISTS `pegawai`;
DROP TABLE IF EXISTS `level`;

CREATE TABLE `level` (
  `id_level` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_level` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `level` (`id_level`, `nama_level`) VALUES
(1, 'administrator'),
(2, 'operator'),
(3, 'peminjam');

-- ----------------------------------------------------------
-- 2. Tabel pegawai
-- ----------------------------------------------------------
CREATE TABLE `pegawai` (
  `id_pegawai` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_pegawai` VARCHAR(100) NOT NULL,
  `nip` VARCHAR(30) NOT NULL UNIQUE,
  `alamat` TEXT NOT NULL,
  `kelas` VARCHAR(30) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pegawai` (`id_pegawai`, `nama_pegawai`, `nip`, `alamat`) VALUES
(1, 'Drs. H. Ahmad Fauzi, M.Kom', '19750812213', 'Jl. Pendidikan No. 12, Bandung'),
(2, 'Siti Nurhaliza, S.Pd', '198304152223', 'Jl. Merdeka No. 45, Bandung'),
(3, 'Budi Santoso, S.T', '19881127643', 'Jl. Melati No. 8, Bandung'),
(4, 'Rian Pratama, S.Kom.', '27685345467', 'Jl. Anggrek No. 15, Bandung'),
(6, 'ARLITA SITI FATMAH', 'SISWA-2026-003', 'Jl. Cikutra No. 12, Bandung'),
(7, 'CAHYANI APRILIYANTI', 'SISWA-2026-004', 'Jl. Pahlawan No. 18, Cirebon'),
(8, 'CILA PUTRI PRASETIO', 'SISWA-2026-005', 'Jl. Sudirman No. 25, Jakarta'),
(9, 'CYNARA SIYFA ELYSIA', 'SISWA-2026-006', 'Jl. Merdeka No. 9, Bogor'),
(10, 'FAKHIRA HAFIZA QURANIQUE', 'SISWA-2026-007', 'Jl. Diponegoro No. 15, Yogyakarta'),
(11, 'FATHIYA NUR HASANAH', 'SISWA-2026-008', 'Jl. Raya Cipanas No. 14, Cianjur'),
(12, 'FHATYA NUR ISMI', 'SISWA-2026-009', 'Jl. Riau No. 29, Bekasi'),
(13, 'ILONA BELVA KHANSA', 'SISWA-2026-010', 'Jl. Kramat Jati No. 21, Jakarta Timur'),
(14, 'INDAH EKA PUTRI', 'SISWA-2026-011', 'Jl. Jakarta Selatan No. 17, Jakarta'),
(15, 'LIDA FEBRIANI', 'SISWA-2026-012', 'Jl. Pahlawan No. 27, Tasikmalaya'),
(16, 'N. NAZLA AMALIA NUROHMAH', 'SISWA-2026-013', 'Jl. Cibadak No. 41, Sukabumi'),
(17, 'NADHIRA KIRANASYA AZZAHRA', 'SISWA-2026-014', 'Jl. Raya Subang No. 7, Subang'),
(18, 'NAJWA NUR KARIMA', 'SISWA-2026-015', 'Jl. Astana Anyar No. 22, Bandung'),
(19, 'RIENETTA HUSNA PRIMA', 'SISWA-2026-016', 'Jl. Bumi Asri No. 16, Depok'),
(20, 'RISTA NAURA HIDAYAT', 'SISWA-2026-017', 'Jl. Dawuan No. 31, Cirebon'),
(21, 'SALWA SUMIYATI', 'SISWA-2026-018', 'Jl. Cibaduyut No. 13, Bandung'),
(22, 'SHAKILA SYAFITRI', 'SISWA-2026-019', 'Jl. Pasteur No. 20, Bandung'),
(23, 'SHAUMMI AGUSTINA', 'SISWA-2026-020', 'Jl. Buah Batu No. 26, Bandung'),
(24, 'SILVI SRI RAHAYU', 'SISWA-2026-021', 'Jl. Kiaracondong No. 19, Bogor'),
(25, 'SILVIA ZAHRA', 'SISWA-2026-022', 'Jl. Cimenyan No. 15, Bandung'),
(26, 'SITI MARYAM', 'SISWA-2026-023', 'Jl. Ujungberung No. 10, Bandung'),
(27, 'SITI SARAH', 'SISWA-2026-024', 'Jl. Lembang No. 28, Bandung Barat'),
(28, 'SYABNA DIVA MAORINE', 'SISWA-2026-025', 'Jl. Jatinangor No. 8, Sumedang'),
(29, 'ZAHIRA NUR AZIZAH', 'SISWA-2026-026', 'Jl. Soreang No. 42, Bandung'),
(30, 'ALFIRA NUR REVANDA', 'SISWA-2026-027', 'Jl. Cileunyi No. 11, Bandung'),
(31, 'AMEERA NAYANA HIDAYAT', 'SISWA-2026-028', 'Jl. Cibereum No. 24, Pangalengan'),
(32, 'ANGGITA ANGGRAINI KUSNAINDAR', 'SISWA-2026-029', 'Jl. Majalengka No. 18, Majalengka'),
(33, 'ANISA MEYDIATI PURNAMA', 'SISWA-2026-030', 'Jl. Cisitu No. 23, Sumedang'),
(34, 'DIKRA ASSYIFA', 'SISWA-2026-031', 'Jl. Garut No. 14, Garut'),
(35, 'DINA FEBRIYANI PUTRI', 'SISWA-2026-032', 'Jl. Nagreg No. 21, Bandung'),
(36, 'DWI OKTAVIANI', 'SISWA-2026-033', 'Jl. Baleendah No. 9, Bandung'),
(37, 'FEBI SAPARINGGA', 'SISWA-2026-034', 'Jl. Bojongsoang No. 16, Bandung'),
(38, 'INDRI ANDRIANI', 'SISWA-2026-035', 'Jl. Ciparay No. 30, Bandung'),
(39, 'KANAYA AULIA SALSABILA', 'SISWA-2026-036', 'Jl. Majalaya No. 19, Bandung'),
(40, 'MUHAMMAD FAUZI RIDWAN SESFAO', 'SISWA-2026-037', 'Jl. Cicalengka No. 27, Bandung'),
(41, 'MUHAMMAD ZAKY ARDIANSYAH', 'SISWA-2026-038', 'Jl. Margaasih No. 20, Bandung'),
(42, 'NAIMA PUTRI NURDIANSYAH', 'SISWA-2026-039', 'Jl. Sumedang No. 17, Sumedang'),
(43, 'NAJWA PUSPITA', 'SISWA-2026-040', 'Jl. Tegalega No. 12, Bandung'),
(44, 'NAYSHA NUR SANNI', 'SISWA-2026-041', 'Jl. Ciwidey No. 29, Bandung'),
(45, 'NAZWA NAFISA PUTRI', 'SISWA-2026-042', 'Jl. Cileunyi No. 15, Bandung'),
(46, 'PUSPA FEBBYANTI', 'SISWA-2026-043', 'Jl. Kopo No. 32, Bekasi'),
(47, 'PUTRI CARRISA NUR AZMY', 'SISWA-2026-044', 'Jl. Dayeuhkolot No. 7, Bandung'),
(48, 'RINI AGUSTIN PUTRI FIRDAUS', 'SISWA-2026-045', 'Jl. Banjaran No. 18, Bandung'),
(49, 'SAEKA AGUSTIANI', 'SISWA-2026-046', 'Jl. Soreang No. 13, Bandung'),
(50, 'SYALIA ZAHRATUSYIFA', 'SISWA-2026-047', 'Jl. Turangga No. 22, Bandung'),
(51, 'TYARA OKTAFYA SRIWAHYUNI', 'SISWA-2026-048', 'Jl. Batujajar No. 14, Bandung Barat'),
(52, 'ZAHRA NUR FARIDAH', 'SISWA-2026-049', 'Jl. Bandung Wetan No. 26, Bandung'),
(53, 'ADID YULIANTO', 'SISWA-2026-050', 'Jl. Taman Sari No. 10, Surabaya'),
(54, 'AHMAD FAUZAN', 'SISWA-2026-051', 'Jl. Cihampelas No. 24, Bandung'),
(55, 'ALVI FAUZAN AL-FARIZ', 'SISWA-2026-052', 'Jl. Rancaekek No. 16, Bandung'),
(56, 'ANWAR ABDULAH', 'SISWA-2026-053', 'Jl. Gedebage No. 18, Bandung'),
(57, 'ARKANA ALFARIZI DEVIANTARA', 'SISWA-2026-054', 'Jl. Kebon Kawung No. 9, Bandung'),
(58, 'DEDIN SYAUQAL AL-FAHRI', 'SISWA-2026-055', 'Jl. Cempaka No. 13, Bandung'),
(59, 'DETRAN RIZKI FIRMANSYAH', 'SISWA-2026-056', 'Jl. Sarijadi No. 20, Bandung'),
(60, 'DIKA MUHAMMAD AL-FATH BAHARIZQI', 'SISWA-2026-057', 'Jl. Padalarang No. 11, Bandung Barat'),
(61, 'DIMAS RIZKI MAULANA YUSUF', 'SISWA-2026-058', 'Jl. Bojong Koneng No. 27, Bandung'),
(62, 'FAIQ ADILA', 'SISWA-2026-059', 'Jl. Rancabolang No. 15, Bandung'),
(63, 'FAIRUZ AL SAHABIR AFLI', 'SISWA-2026-060', 'Jl. Cijerah No. 31, Bandung'),
(64, 'FEBRIAN QAIDAN NASIK', 'SISWA-2026-061', 'Jl. Sayati No. 21, Bandung'),
(65, 'FIKRUL ISLAM AL AMIQ', 'SISWA-2026-062', 'Jl. Cibeunying No. 29, Bandung'),
(66, 'MUHAMMAD ALGIFFARI RAMADHANI', 'SISWA-2026-063', 'Jl. Wates No. 17, Yogyakarta'),
(67, 'MUHAMMAD BAGIR ALMALIQY KASTAMAN', 'SISWA-2026-064', 'Jl. Kiarapayung No. 12, Bandung'),
(68, 'MUHAMMAD DZAKY KAMIL', 'SISWA-2026-065', 'Jl. Cibangkong No. 19, Bandung'),
(69, 'MUHAMMAD FAUZAN NURDIANA', 'SISWA-2026-066', 'Jl. Cisaranten No. 23, Bandung'),
(70, 'MUHAMMAD NAZRIL PUTRA NATRABU', 'SISWA-2026-067', 'Jl. Cicadas No. 14, Bandung'),
(71, 'RADITYA BAGAS WICAKSONO', 'SISWA-2026-068', 'Jl. Mande No. 28, Purwakarta'),
(72, 'RAKHA AL FARIZI ABDUL JABAR', 'SISWA-2026-069', 'Jl. Cibogo No. 22, Garut'),
(73, 'SEPTIAN RAMADHAN DWIYANTO', 'SISWA-2026-070', 'Jl. Andir No. 25, Bandung'),
(74, 'ZAKY ZULFIKAR', 'SISWA-2026-071', 'Jl. Ujung Gurun No. 18, Malang');

UPDATE `pegawai`
SET `kelas` = 'XII-PPLG'
WHERE `nip` BETWEEN 'SISWA-2026-050' AND 'SISWA-2026-071';

UPDATE `pegawai`
SET `kelas` = 'XI-AKT'
WHERE `nip` IN (
  'SISWA-2026-027',
  'SISWA-2026-028',
  'SISWA-2026-029',
  'SISWA-2026-030',
  'SISWA-2026-031',
  'SISWA-2026-032',
  'SISWA-2026-033',
  'SISWA-2026-034',
  'SISWA-2026-035',
  'SISWA-2026-036',
  'SISWA-2026-037',
  'SISWA-2026-038',
  'SISWA-2026-039',
  'SISWA-2026-040',
  'SISWA-2026-041',
  'SISWA-2026-042',
  'SISWA-2026-043',
  'SISWA-2026-044',
  'SISWA-2026-045',
  'SISWA-2026-046',
  'SISWA-2026-047',
  'SISWA-2026-048',
  'SISWA-2026-049'
);

UPDATE `pegawai`
SET `kelas` = 'X-AKT'
WHERE `nip` IN (
  'SISWA-2026-003',
  'SISWA-2026-004',
  'SISWA-2026-005',
  'SISWA-2026-006',
  'SISWA-2026-007',
  'SISWA-2026-008',
  'SISWA-2026-009',
  'SISWA-2026-010',
  'SISWA-2026-011',
  'SISWA-2026-012',
  'SISWA-2026-013',
  'SISWA-2026-014',
  'SISWA-2026-015',
  'SISWA-2026-016',
  'SISWA-2026-017',
  'SISWA-2026-018',
  'SISWA-2026-019',
  'SISWA-2026-020',
  'SISWA-2026-021',
  'SISWA-2026-022',
  'SISWA-2026-023',
  'SISWA-2026-024',
  'SISWA-2026-025',
  'SISWA-2026-026'
);

UPDATE `pegawai`
SET `nip` = CASE `nip`
  WHEN 'SISWA-2026-003' THEN '26270129001'
  WHEN 'SISWA-2026-004' THEN '26270129002'
  WHEN 'SISWA-2026-005' THEN '26270129005'
  WHEN 'SISWA-2026-006' THEN '26270129006'
  WHEN 'SISWA-2026-007' THEN '26270129007'
  WHEN 'SISWA-2026-008' THEN '26270129008'
  WHEN 'SISWA-2026-009' THEN '26270129009'
  WHEN 'SISWA-2026-010' THEN '26270129010'
  WHEN 'SISWA-2026-011' THEN '26270129011'
  WHEN 'SISWA-2026-012' THEN '26270129012'
  WHEN 'SISWA-2026-013' THEN '26270129013'
  WHEN 'SISWA-2026-014' THEN '26270129014'
  WHEN 'SISWA-2026-015' THEN '26270129015'
  WHEN 'SISWA-2026-016' THEN '26270129016'
  WHEN 'SISWA-2026-017' THEN '26270129017'
  WHEN 'SISWA-2026-018' THEN '26270129018'
  WHEN 'SISWA-2026-019' THEN '26270129019'
  WHEN 'SISWA-2026-020' THEN '26270129020'
  WHEN 'SISWA-2026-021' THEN '26270129021'
  WHEN 'SISWA-2026-022' THEN '26270129022'
  WHEN 'SISWA-2026-023' THEN '26270129023'
  WHEN 'SISWA-2026-024' THEN '26270129024'
  WHEN 'SISWA-2026-025' THEN '26270129025'
  WHEN 'SISWA-2026-026' THEN '26270129026'
  ELSE `nip`
END
WHERE `nip` IN (
  'SISWA-2026-003', 'SISWA-2026-004', 'SISWA-2026-005', 'SISWA-2026-006',
  'SISWA-2026-007', 'SISWA-2026-008', 'SISWA-2026-009', 'SISWA-2026-010',
  'SISWA-2026-011', 'SISWA-2026-012', 'SISWA-2026-013', 'SISWA-2026-014',
  'SISWA-2026-015', 'SISWA-2026-016', 'SISWA-2026-017', 'SISWA-2026-018',
  'SISWA-2026-019', 'SISWA-2026-020', 'SISWA-2026-021', 'SISWA-2026-022',
  'SISWA-2026-023', 'SISWA-2026-024', 'SISWA-2026-025', 'SISWA-2026-026'
);

UPDATE `pegawai`
SET `nip` = CASE `nip`
  WHEN 'SISWA-2026-027' THEN '25260119001'
  WHEN 'SISWA-2026-028' THEN '25260119002'
  WHEN 'SISWA-2026-029' THEN '25260119003'
  WHEN 'SISWA-2026-030' THEN '25260119004'
  WHEN 'SISWA-2026-031' THEN '25260119005'
  WHEN 'SISWA-2026-032' THEN '25260119006'
  WHEN 'SISWA-2026-033' THEN '25260119007'
  WHEN 'SISWA-2026-034' THEN '25260119008'
  WHEN 'SISWA-2026-035' THEN '25260119009'
  WHEN 'SISWA-2026-036' THEN '25260119011'
  WHEN 'SISWA-2026-037' THEN '25260119012'
  WHEN 'SISWA-2026-038' THEN '25260119024'
  WHEN 'SISWA-2026-039' THEN '25260119013'
  WHEN 'SISWA-2026-040' THEN '25260119014'
  WHEN 'SISWA-2026-041' THEN '25260119015'
  WHEN 'SISWA-2026-042' THEN '25260119016'
  WHEN 'SISWA-2026-043' THEN '25260119017'
  WHEN 'SISWA-2026-044' THEN '25260119018'
  WHEN 'SISWA-2026-045' THEN '25260119019'
  WHEN 'SISWA-2026-046' THEN '25260119020'
  WHEN 'SISWA-2026-047' THEN '25260119021'
  WHEN 'SISWA-2026-048' THEN '25260119022'
  WHEN 'SISWA-2026-049' THEN '25260119023'
  WHEN 'SISWA-2026-050' THEN '24251807002'
  WHEN 'SISWA-2026-051' THEN '24251807003'
  WHEN 'SISWA-2026-052' THEN '24251800005'
  WHEN 'SISWA-2026-053' THEN '24251870007'
  WHEN 'SISWA-2026-054' THEN '24251070010'
  WHEN 'SISWA-2026-055' THEN '24258070014'
  WHEN 'SISWA-2026-056' THEN '24218070015'
  WHEN 'SISWA-2026-057' THEN '24518070016'
  WHEN 'SISWA-2026-058' THEN '24251870017'
  WHEN 'SISWA-2026-059' THEN '24251070020'
  WHEN 'SISWA-2026-060' THEN '24251807021'
  WHEN 'SISWA-2026-061' THEN '24251807027'
  WHEN 'SISWA-2026-062' THEN '24251807028'
  WHEN 'SISWA-2026-063' THEN '24251807035'
  WHEN 'SISWA-2026-064' THEN '24251807036'
  WHEN 'SISWA-2026-065' THEN '24251807037'
  WHEN 'SISWA-2026-066' THEN '24251807038'
  WHEN 'SISWA-2026-067' THEN '24251807039'
  WHEN 'SISWA-2026-068' THEN '24251807041'
  WHEN 'SISWA-2026-069' THEN '24251807043'
  WHEN 'SISWA-2026-070' THEN '24251807047'
  WHEN 'SISWA-2026-071' THEN '24251807050'
  ELSE `nip`
END
WHERE `nip` IN (
  'SISWA-2026-027', 'SISWA-2026-028', 'SISWA-2026-029', 'SISWA-2026-030',
  'SISWA-2026-031', 'SISWA-2026-032', 'SISWA-2026-033', 'SISWA-2026-034',
  'SISWA-2026-035', 'SISWA-2026-036', 'SISWA-2026-037', 'SISWA-2026-038',
  'SISWA-2026-039', 'SISWA-2026-040', 'SISWA-2026-041', 'SISWA-2026-042',
  'SISWA-2026-043', 'SISWA-2026-044', 'SISWA-2026-045', 'SISWA-2026-046',
  'SISWA-2026-047', 'SISWA-2026-048', 'SISWA-2026-049', 'SISWA-2026-050',
  'SISWA-2026-051', 'SISWA-2026-052', 'SISWA-2026-053', 'SISWA-2026-054',
  'SISWA-2026-055', 'SISWA-2026-056', 'SISWA-2026-057', 'SISWA-2026-058',
  'SISWA-2026-059', 'SISWA-2026-060', 'SISWA-2026-061', 'SISWA-2026-062',
  'SISWA-2026-063', 'SISWA-2026-064', 'SISWA-2026-065', 'SISWA-2026-066',
  'SISWA-2026-067', 'SISWA-2026-068', 'SISWA-2026-069', 'SISWA-2026-070',
  'SISWA-2026-071'
);

-- ----------------------------------------------------------
-- 3. Tabel petugas (User Akun Sistem)
-- ----------------------------------------------------------
CREATE TABLE `petugas` (
  `id_petugas` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `nama_petugas` VARCHAR(100) NOT NULL,
  `id_level` INT NOT NULL,
  `id_pegawai` INT NULL,
  CONSTRAINT `fk_petugas_level` FOREIGN KEY (`id_level`) REFERENCES `level` (`id_level`) ON UPDATE CASCADE,
  CONSTRAINT `fk_petugas_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Password standar akun petugas: admin123, operator123
-- Hash untuk password 'admin123': $2y$10$wK1L8R/0a.65z7P2B97gse26f2cO4M/1gEln11aJj9c.k1VqEeVbW
-- Hash untuk password 'operator123': $2y$10$Z3k6p58iY9bJm5f3l79Jfe6Hq6HjQf0xL3o8P.z7p5wQ4u1s2k9L.
-- We can also use a universal hash or verified bcrypt hash in PHP, let's insert standard records with md5/bcrypt
INSERT INTO `petugas` (`id_petugas`, `username`, `password`, `nama_petugas`, `id_level`, `id_pegawai`) VALUES
(1, 'admin', '$2y$10$f3F8g3eS36DkcmgEaI.Dpe9uE84xY20tKx2tHnF9J4U/Z23YV3ZqC', 'Administrator Utama', 1, 1),
(2, 'operator', '$2y$10$f3F8g3eS36DkcmgEaI.Dpe9uE84xY20tKx2tHnF9J4U/Z23YV3ZqC', 'Operator Sarpras', 2, 2);

-- ----------------------------------------------------------
-- 4. Tabel jenis
-- ----------------------------------------------------------
CREATE TABLE `jenis` (
  `id_jenis` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_jenis` VARCHAR(100) NOT NULL,
  `kode_jenis` VARCHAR(20) NOT NULL UNIQUE,
  `keterangan` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `jenis` (`id_jenis`, `nama_jenis`, `kode_jenis`, `keterangan`) VALUES
(1, 'Elektronik & Komputer', 'JNS-ELK', 'Peralatan digital, komputer, laptop, dan proyektor'),
(2, 'Alat Laboratorium RPL', 'JNS-LAB', 'Peralatan khusus praktikum dan jaringan komputer'),
(3, 'Furniture & Meubelair', 'JNS-MBL', 'Meja, kursi, dan lemari penyimpanan inventaris'),
(4, 'Audio Visual & Multimedia', 'JNS-AVM', 'Sound system, mic wireless, dan kamera dokumentasi'),
(5, 'Perangkat Jaringan & Internet', 'JNS-JAR', 'Router, switch, access point, modem, server, dan jaringan sekolah'),
(6, 'Peralatan Kantor & Administrasi', 'JNS-KTR', 'Printer, scanner, arsip, dan peralatan kerja administrasi'),
(7, 'Peralatan Kebersihan & Rumah Tangga', 'JNS-KBS', 'Peralatan kebersihan dan penunjang ruang kelas serta kantor'),
(8, 'Peralatan Keamanan', 'JNS-KMN', 'CCTV, alarm, keamanan ruang, dan perangkat monitoring sekolah'),
(9, 'Peralatan Pembelajaran', 'JNS-PEMB', 'Alat bantu kegiatan belajar, praktik, dan presentasi siswa'),
(10, 'Peralatan Elektronika Umum', 'JNS-ELEK', 'Perangkat elektronik pendukung operasional sekolah'),
(11, 'Perabot & Ruang Kelas', 'JNS-FURN', 'Meja, kursi, lemari, rak, dan alat penunjang kelas'),
(12, 'Perpustakaan & Media Digital', 'JNS-PERP', 'Perangkat media, digital library, dan alat pendukung literasi');

-- ----------------------------------------------------------
-- 5. Tabel ruang
-- ----------------------------------------------------------
CREATE TABLE `ruang` (
  `id_ruang` INT AUTO_INCREMENT PRIMARY KEY,
  `nama_ruang` VARCHAR(100) NOT NULL,
  `kode_ruang` VARCHAR(20) NOT NULL UNIQUE,
  `keterangan` TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `ruang` (`id_ruang`, `nama_ruang`, `kode_ruang`, `keterangan`) VALUES
(1, 'Kepala Sekolah', 'RNG-001', 'Ruang kepala sekolah'),
(2, 'Guru dan Wks. Kurikulum', 'RNG-002', 'Ruang guru dan wakil kurikulum'),
(3, 'Wks kesiswaan dan Bk', 'RNG-003', 'Ruang wakil kesiswaan dan BK'),
(4, 'Wks Hubin dan BKK', 'RNG-004', 'Ruang hubungan industri dan BKK'),
(5, 'TU Keuangan', 'RNG-005', 'Ruang tata usaha keuangan'),
(6, 'TU Administrasi', 'RNG-006', 'Ruang tata usaha administrasi'),
(7, 'UKS', 'RNG-007', 'Ruang UKS sekolah'),
(8, 'Osis', 'RNG-008', 'Ruang organisasi siswa intra sekolah'),
(9, 'Pramuka', 'RNG-009', 'Ruang kegiatan pramuka'),
(10, 'Paskibra', 'RNG-010', 'Ruang paskibra'),
(11, 'Kantor Perusahaan DUDI', 'RNG-011', 'Ruang kerja sama industri'),
(12, 'Sarana Prasarana dan Gudang', 'RNG-012', 'Ruang sarana prasarana dan gudang'),
(13, 'IT SUPPORT', 'RNG-013', 'Ruang dukungan teknologi informasi'),
(14, 'LAB 1 PPLG', 'RNG-014', 'Laboratorium PPLG 1'),
(15, 'LAB 2 PPLG', 'RNG-015', 'Laboratorium PPLG 2'),
(16, 'LAB 3 ANIMASI', 'RNG-016', 'Laboratorium animasi'),
(17, 'LAB GAMBAR', 'RNG-017', 'Laboratorium gambar teknik'),
(18, 'LAB DKV', 'RNG-018', 'Laboratorium desain komunikasi visual'),
(19, 'LAB AKUNTANSI', 'RNG-019', 'Laboratorium akuntansi'),
(20, 'LAB BAHASA', 'RNG-020', 'Laboratorium bahasa'),
(21, 'LAB PEMASARAN', 'RNG-021', 'Laboratorium pemasaran'),
(22, 'LAB PPLG 3', 'RNG-022', 'Laboratorium PPLG 3'),
(23, 'LAB PPLG 4', 'RNG-023', 'Laboratorium PPLG 4'),
(24, 'LAB PPLG 5', 'RNG-024', 'Laboratorium PPLG 5'),
(25, 'LAB PPLG IOT', 'RNG-025', 'Laboratorium IoT'),
(26, 'LAB BROADCAST', 'RNG-026', 'Laboratorium broadcast'),
(27, 'SABLON', 'RNG-027', 'Ruang sablon'),
(28, 'LAB CETAK', 'RNG-028', 'Laboratorium cetak'),
(29, 'Studio FOTO', 'RNG-029', 'Ruang studio foto'),
(30, 'KESENIAN', 'RNG-030', 'Ruang seni dan kesenian'),
(31, 'PERPUSTAKAAN', 'RNG-031', 'Perpustakaan sekolah'),
(32, 'XII AKUNTANSI 1', 'RNG-032', 'Kelas XII Akuntansi 1'),
(33, 'XII AKUNTANSI 2', 'RNG-033', 'Kelas XII Akuntansi 2'),
(34, 'XII DKV', 'RNG-034', 'Kelas XII DKV'),
(35, 'XII ANIMASI', 'RNG-035', 'Kelas XII Animasi'),
(36, 'GUDANG KURSI', 'RNG-036', 'Gudang kursi sekolah'),
(37, 'XII PPLG 1', 'RNG-037', 'Kelas XII PPLG 1'),
(38, 'XII PPLG 2', 'RNG-038', 'Kelas XII PPLG 2'),
(39, 'XI AKT', 'RNG-039', 'Kelas XI Akuntansi'),
(40, 'KOSONG', 'RNG-040', 'Ruang kosong'),
(41, 'XI PPLG 1', 'RNG-041', 'Kelas XI PPLG 1'),
(42, 'XI PPLG 2', 'RNG-042', 'Kelas XI PPLG 2'),
(43, 'X PPLG 1', 'RNG-043', 'Kelas X PPLG 1'),
(44, 'X PPLG 2', 'RNG-044', 'Kelas X PPLG 2'),
(45, 'XI DKV', 'RNG-045', 'Kelas XI DKV'),
(46, 'XI ANIMASI', 'RNG-046', 'Kelas XI Animasi'),
(47, 'X DKV 2', 'RNG-047', 'Kelas X DKV 2'),
(48, 'X DKV 1', 'RNG-048', 'Kelas X DKV 1'),
(49, 'KOSONG', 'RNG-049', 'Ruang kosong'),
(50, 'X AKUNTANSI 2', 'RNG-050', 'Kelas X Akuntansi 2'),
(51, 'GUDANG KURSI', 'RNG-051', 'Gudang kursi sekolah'),
(52, 'X AKUNTANSI 1', 'RNG-052', 'Kelas X Akuntansi 1'),
(53, 'KELAS KOSONG 1', 'RNG-053', 'Ruang kelas kosong 1'),
(54, 'KELAS KOSONG 2', 'RNG-054', 'Ruang kelas kosong 2'),
(55, 'KELAS KOSONG 3', 'RNG-055', 'Ruang kelas kosong 3'),
(56, 'KELAS KOSONG GD DEPAN 1', 'RNG-056', 'Ruang kelas kosong depan 1'),
(57, 'KELAS KOSONG GD DEPAN 2', 'RNG-057', 'Ruang kelas kosong depan 2'),
(58, 'KELAS KOSONG GD DEPAN 3', 'RNG-058', 'Ruang kelas kosong depan 3'),
(59, 'KELAS KOSONG GD DEPAN 4', 'RNG-059', 'Ruang kelas kosong depan 4');

-- ----------------------------------------------------------
-- 6. Tabel inventaris
-- ----------------------------------------------------------
CREATE TABLE `inventaris` (
  `id_inventaris` INT AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(150) NOT NULL,
  `kondisi` VARCHAR(50) NOT NULL DEFAULT 'Baik',
  `keterangan` TEXT,
  `jumlah` INT NOT NULL DEFAULT 0,
  `id_jenis` INT NOT NULL,
  `tanggal_register` DATE NOT NULL,
  `id_ruang` INT NOT NULL,
  `kode_inventaris` VARCHAR(50) NOT NULL UNIQUE,
  `id_petugas` INT NOT NULL,
  CONSTRAINT `fk_inventaris_jenis` FOREIGN KEY (`id_jenis`) REFERENCES `jenis` (`id_jenis`) ON UPDATE CASCADE,
  CONSTRAINT `fk_inventaris_ruang` FOREIGN KEY (`id_ruang`) REFERENCES `ruang` (`id_ruang`) ON UPDATE CASCADE,
  CONSTRAINT `fk_inventaris_petugas` FOREIGN KEY (`id_petugas`) REFERENCES `petugas` (`id_petugas`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `inventaris` (`id_inventaris`, `nama`, `kondisi`, `keterangan`, `jumlah`, `id_jenis`, `tanggal_register`, `id_ruang`, `kode_inventaris`, `id_petugas`) VALUES
(1, 'Laptop Asus Core i5', 'Baik', 'Laptop inventaris untuk praktikum guru dan siswa', 15, 1, '2026-01-10', 1, 'INV-2026-001', 1),
(2, 'Proyektor Epson EB-X500', 'Baik', 'Proyektor presentasi ruang multimedia', 5, 1, '2026-01-12', 3, 'INV-2026-002', 1),
(3, 'Crimping Tool RJ45 Pro', 'Baik', 'Tang crimping kabel UTP Cat6', 20, 2, '2026-01-15', 2, 'INV-2026-003', 1),
(4, 'Kamera DSLR Canon EOS 200D', 'Baik', 'Kamera dokumentasi dan materi multimedia', 3, 4, '2026-02-01', 3, 'INV-2026-004', 1),
(5, 'Microphone Wireless Shure', 'Baik', 'Mic wireless acara dan seminar', 4, 4, '2026-02-05', 3, 'INV-2026-005', 1),
(6, 'Router Mikrotik RB750Gr3', 'Baik', 'Routerboard praktikum jaringan komputer', 10, 2, '2026-02-10', 2, 'INV-2026-006', 1),
(7, 'Printer Epson L3210', 'Baik', 'Printer warna dan scanner', 2, 1, '2026-02-15', 5, 'INV-2026-007', 1),
(8, 'Monitor LCD 24 inch Dell', 'Baik', 'Monitor untuk lab komputer dan kelas', 18, 1, '2026-02-18', 1, 'INV-2026-008', 1),
(9, 'Switch 24 Port TP-Link', 'Baik', 'Switch jaringan untuk server dan lab', 6, 2, '2026-02-20', 11, 'INV-2026-009', 1),
(10, 'UPS 1500VA APC', 'Baik', 'UPS untuk menjaga kestabilan server dan router', 8, 2, '2026-02-22', 11, 'INV-2026-010', 1),
(11, 'Keyboard & Mouse Set Logitech', 'Baik', 'Set keyboard dan mouse untuk workstation siswa', 40, 1, '2026-02-25', 1, 'INV-2026-011', 1),
(12, 'Meja Komputer Siswa', 'Baik', 'Meja komputer untuk 1 siswa', 30, 3, '2026-02-27', 7, 'INV-2026-012', 1),
(13, 'Kursi Siswa Ergonomis', 'Baik', 'Kursi siswa untuk ruang kelas dan lab', 60, 3, '2026-03-01', 7, 'INV-2026-013', 1),
(14, 'Scanner Canon Lide 300', 'Baik', 'Scanner untuk admin dan dokumentasi', 2, 1, '2026-03-05', 5, 'INV-2026-014', 1),
(15, 'Speaker Aktif JBL 12 inch', 'Baik', 'Speaker untuk studio dan acara sekolah', 4, 4, '2026-03-10', 3, 'INV-2026-015', 1),
(16, 'Hard Disk Eksternal 2TB', 'Baik', 'Media penyimpanan data siswa dan pembelajaran', 5, 1, '2026-03-12', 10, 'INV-2026-016', 1),
(17, 'Set Perangkat Desain Grafis', 'Baik', 'Komputer dan tablet untuk workshop desain', 6, 1, '2026-03-14', 12, 'INV-2026-017', 1),
(18, 'Whiteboard Digital Interaktif', 'Baik', 'Whiteboard digital untuk pembelajaran', 2, 4, '2026-03-18', 8, 'INV-2026-018', 1),
(19, 'Desktop PC Intel Core i7', 'Baik', 'PC unit untuk server dan laboratorium multimedia', 8, 1, '2026-03-20', 6, 'INV-2026-019', 1),
(20, 'Laptop Lenovo ThinkPad', 'Baik', 'Laptop untuk guru dan operator sekolah', 6, 1, '2026-03-22', 5, 'INV-2026-020', 1),
(21, 'Printer Dot Matrix Epson', 'Baik', 'Printer untuk administrasi sekolah', 3, 1, '2026-03-24', 5, 'INV-2026-021', 1),
(22, 'Kabel UTP Cat6 100m', 'Baik', 'Kabel jaringan untuk lab dan ruang server', 12, 2, '2026-03-25', 11, 'INV-2026-022', 1),
(23, 'Access Point TP-Link EAP245', 'Baik', 'Access point untuk jaringan Wi-Fi sekolah', 5, 2, '2026-03-27', 11, 'INV-2026-023', 1),
(24, 'Modem Fiber Optic', 'Baik', 'Modem internet untuk layanan jaringan sekolah', 2, 2, '2026-03-29', 11, 'INV-2026-024', 1),
(25, 'Kursi Kepala Lab', 'Baik', 'Kursi kerja untuk kepala laboratorium', 2, 3, '2026-04-01', 6, 'INV-2026-025', 1),
(26, 'Meja Guru', 'Baik', 'Meja kerja guru di ruang kelas dan lab', 12, 3, '2026-04-03', 5, 'INV-2026-026', 1),
(27, 'Rak Data Komputer', 'Baik', 'Rak penyimpanan perangkat jaringan dan data', 4, 3, '2026-04-05', 11, 'INV-2026-027', 1),
(28, 'Smart TV 65 inch', 'Baik', 'Smart TV untuk aula dan pembelajaran digital', 2, 4, '2026-04-07', 3, 'INV-2026-028', 1),
(29, 'Tripod Kamera DSLR', 'Baik', 'Tripod pendukung pengambilan gambar dokumentasi', 3, 4, '2026-04-10', 3, 'INV-2026-029', 1),
(30, 'Mixer Audio Digital', 'Baik', 'Mixer untuk event dan studio audio', 2, 4, '2026-04-12', 3, 'INV-2026-030', 1),
(31, 'Layar Proyeksi 100 inch', 'Baik', 'Layar proyeksi untuk ruang presentasi', 2, 4, '2026-04-15', 3, 'INV-2026-031', 1),
(32, 'Server Mini PC', 'Baik', 'Server untuk pengelolaan data dan perangkat sekolah', 2, 2, '2026-04-18', 11, 'INV-2026-032', 1),
(33, 'Kipas Angin Ruang Lab', 'Baik', 'Pendingin udara ruang laboratorium', 8, 3, '2026-04-20', 1, 'INV-2026-033', 1),
(34, 'Lemari Arsip Administrasi', 'Baik', 'Lemari untuk dokumen dan surat', 4, 3, '2026-04-22', 5, 'INV-2026-034', 1),
(35, 'Webcam Logitech C920', 'Baik', 'Webcam untuk pembelajaran hybrid dan konferensi', 6, 1, '2026-04-25', 8, 'INV-2026-035', 1),
(36, 'Laptop Acer TravelMate', 'Baik', 'Laptop untuk kelas TKJ dan praktik siswa', 12, 1, '2026-05-02', 14, 'INV-2026-036', 1),
(37, 'Printer Inkjet Epson L360', 'Baik', 'Printer untuk administrasi dan dokumentasi kelas', 4, 1, '2026-05-04', 5, 'INV-2026-037', 1),
(38, 'UPS 3000VA APC', 'Baik', 'UPS untuk server dan ruangan kritis', 4, 2, '2026-05-06', 16, 'INV-2026-038', 1),
(39, 'Switch 48 Port Cisco', 'Baik', 'Switch jaringan utama lab jaringan', 3, 2, '2026-05-08', 16, 'INV-2026-039', 1),
(40, 'Access Point Indoor TP-Link', 'Baik', 'Access point untuk area kelas dan laboratorium', 8, 2, '2026-05-10', 15, 'INV-2026-040', 1),
(41, 'CCTV Dome Camera', 'Baik', 'Camera pengawas keamanan sekolah', 10, 2, '2026-05-12', 18, 'INV-2026-041', 1),
(42, 'Tablet Drawing Wacom', 'Baik', 'Tablet digital untuk desain grafis dan multimedia', 4, 1, '2026-05-15', 12, 'INV-2026-042', 1),
(43, 'Meja Siswa 2 in 1', 'Baik', 'Meja belajar untuk kelas XI dan XII', 20, 3, '2026-05-17', 13, 'INV-2026-043', 1),
(44, 'Kursi Siswa Plastik', 'Baik', 'Kursi belajar untuk kelas tambahan', 30, 3, '2026-05-18', 13, 'INV-2026-044', 1),
(45, 'Lemari Kaca Peraga', 'Baik', 'Lemari display barang dan alat bantu pembelajaran', 3, 3, '2026-05-20', 17, 'INV-2026-045', 1),
(46, 'Smart TV 75 inch', 'Baik', 'Smart TV untuk aula dan presentasi massal', 2, 4, '2026-05-22', 17, 'INV-2026-046', 1),
(47, 'Speaker Active 15 inch', 'Baik', 'Speaker untuk aula dan kegiatan sekolah', 6, 4, '2026-05-24', 17, 'INV-2026-047', 1),
(48, 'Mixer Audio 8 Channel', 'Baik', 'Mixer untuk audio event sekolah', 2, 4, '2026-05-26', 3, 'INV-2026-048', 1),
(49, 'Scanner Dokumen A3', 'Baik', 'Scanner untuk surat dan arsip sekolah', 2, 1, '2026-05-28', 19, 'INV-2026-049', 1),
(50, 'Kios Internet Pelajar', 'Baik', 'Perangkat akses internet edukasi dan penelitian', 3, 2, '2026-05-30', 10, 'INV-2026-050', 1),
(51, 'Monitor LG 27 inch', 'Baik', 'Monitor untuk workstations dan ruang server', 10, 1, '2026-06-01', 16, 'INV-2026-051', 1),
(52, 'Keyboard Mechanical RGB', 'Baik', 'Keyboard yang digunakan untuk PC gaming dan lab', 15, 1, '2026-06-03', 1, 'INV-2026-052', 1),
(53, 'Mouse Wireless Logitech', 'Baik', 'Mouse untuk komputer laboratorium', 20, 1, '2026-06-05', 1, 'INV-2026-053', 1),
(54, 'Whiteboard Portable', 'Baik', 'Whiteboard untuk ruang belajar dan presentasi', 4, 4, '2026-06-08', 8, 'INV-2026-054', 1),
(55, 'Rak Arsip Besar', 'Baik', 'Rak penyimpanan dokumen dan peralatan', 5, 3, '2026-06-10', 19, 'INV-2026-055', 1),
(56, 'Meja Komputer Guru', 'Baik', 'Meja kerja operator dan guru', 8, 3, '2026-06-12', 5, 'INV-2026-056', 1),
(57, 'Kursi Putar Guru', 'Baik', 'Kursi kantor untuk guru dan TU', 10, 3, '2026-06-14', 5, 'INV-2026-057', 1),
(58, 'Projector LCD XGA', 'Baik', 'Projector untuk kelas dan lab', 5, 1, '2026-06-16', 8, 'INV-2026-058', 1),
(59, 'Webcam PTZ', 'Baik', 'Webcam untuk ruang audio visual dan konferensi', 3, 1, '2026-06-18', 3, 'INV-2026-059', 1),
(60, 'Kipas Angin Industri', 'Baik', 'Kipas angin untuk aula dan ruang besar', 6, 3, '2026-06-20', 17, 'INV-2026-060', 1),
(61, 'Laptop HP ProBook', 'Baik', 'Laptop untuk laboratorium dan kelas produktif', 10, 1, '2026-06-22', 21, 'INV-2026-061', 1),
(62, 'Monitor Dell 24 inch', 'Baik', 'Monitor untuk ruang simulasi dan teknologi', 12, 1, '2026-06-24', 22, 'INV-2026-062', 1),
(63, 'Server Rack 12U', 'Baik', 'Rak server jaringan dan perangkat sekolah', 2, 3, '2026-06-25', 23, 'INV-2026-063', 1),
(64, 'Kabel HDMI 10m', 'Baik', 'Kabel koneksi audio visual kelas', 18, 2, '2026-06-26', 24, 'INV-2026-064', 1),
(65, 'USB Hub 7 Port', 'Baik', 'Hub ekstensi untuk workstation komputer', 15, 1, '2026-06-27', 25, 'INV-2026-065', 1),
(66, 'Lemari Simpan Barang', 'Baik', 'Lemari pengamanan alat sekolah', 4, 3, '2026-06-28', 26, 'INV-2026-066', 1),
(67, 'Camera Live Streaming', 'Baik', 'Kamera untuk siaran dan dokumentasi digital', 2, 4, '2026-06-29', 27, 'INV-2026-067', 1),
(68, 'Soundbar Multimedia', 'Baik', 'Sound system untuk ruang media dan aula', 3, 4, '2026-06-30', 28, 'INV-2026-068', 1),
(69, 'Router Wireless AC', 'Baik', 'Router untuk akses internet kelas dan area sekolah', 6, 2, '2026-07-01', 29, 'INV-2026-069', 1),
(70, 'Printer Label Barcode', 'Baik', 'Printer untuk administrasi barang dan logistik', 2, 1, '2026-07-02', 30, 'INV-2026-070', 1),
(71, 'Meja Kerja Operator', 'Baik', 'Meja untuk operator laboratorium dan admin', 5, 3, '2026-07-03', 5, 'INV-2026-071', 1),
(72, 'Kursi Lipat', 'Baik', 'Kursi tambahan untuk acara sekolah', 20, 3, '2026-07-04', 17, 'INV-2026-072', 1),
(73, 'Televisi LED 32 inch', 'Baik', 'TV untuk ruang kelas dan media pembelajaran', 6, 4, '2026-07-05', 27, 'INV-2026-073', 1),
(74, 'Mic Condenser', 'Baik', 'Microphone untuk studio dan acara', 4, 4, '2026-07-06', 28, 'INV-2026-074', 1),
(75, 'Kabel UTP Cat5e', 'Baik', 'Kabel jaringan tambahan untuk kelas', 25, 2, '2026-07-07', 23, 'INV-2026-075', 1),
(76, 'Laptop Dell Inspiron', 'Baik', 'Laptop untuk pembelajaran digital siswa', 8, 1, '2026-07-08', 23, 'INV-2026-076', 1),
(77, 'Hard Disk SSD 512GB', 'Baik', 'Media penyimpanan cepat workstation', 9, 1, '2026-07-09', 11, 'INV-2026-077', 1),
(78, 'Sinyal Booster WiFi', 'Baik', 'Repeater untuk memperluas jaringan', 5, 2, '2026-07-10', 29, 'INV-2026-078', 1),
(79, 'Kursi Praktikum', 'Baik', 'Kursi laboratorium untuk siswa praktikum', 22, 3, '2026-07-11', 1, 'INV-2026-079', 1),
(80, 'Meja Praktikum', 'Baik', 'Meja untuk kegiatan eksperimen siswa', 18, 3, '2026-07-12', 1, 'INV-2026-080', 1),
(81, 'Scanner Barcode', 'Baik', 'Scanner untuk pengecekan barang inventaris', 3, 1, '2026-07-13', 30, 'INV-2026-081', 1),
(82, 'Kamera Dokumentasi 4K', 'Baik', 'Kamera rekam kegiatan sekolah', 2, 4, '2026-07-14', 25, 'INV-2026-082', 1),
(83, 'Kipas Angin Ceiling', 'Baik', 'Pendingin ruangan kelas dan studio', 12, 3, '2026-07-15', 24, 'INV-2026-083', 1),
(84, 'Proyektor Mini', 'Baik', 'Proyektor portabel untuk kelas kecil', 4, 1, '2026-07-16', 22, 'INV-2026-084', 1),
(85, 'UPS 1000VA', 'Baik', 'UPS untuk perangkat kantor dan workstation', 10, 2, '2026-07-17', 29, 'INV-2026-085', 1),
(86, 'Switch 16 Port', 'Baik', 'Switch jaringan kecil untuk kelas', 8, 2, '2026-07-18', 24, 'INV-2026-086', 1),
(87, 'Keyboard Bluetooth', 'Baik', 'Keyboard untuk perangkat mobile dan laptop', 12, 1, '2026-07-19', 23, 'INV-2026-087', 1),
(88, 'Mouse Bluetooth', 'Baik', 'Mouse nirkabel untuk kelas produktif', 14, 1, '2026-07-20', 23, 'INV-2026-088', 1),
(89, 'Layar Monitor Touchscreen', 'Baik', 'Monitor sentuh untuk lab multimedia', 2, 1, '2026-07-21', 27, 'INV-2026-089', 1),
(90, 'Rak Kontainer Data', 'Baik', 'Rak penyimpanan alat dan perangkat teknologi', 3, 3, '2026-07-22', 30, 'INV-2026-090', 1),
(91, 'Mesin Absensi Digital', 'Baik', 'Mesin absensi untuk kehadiran siswa dan pegawai', 2, 8, '2026-07-23', 18, 'INV-2026-091', 1),
(92, 'Alat Pembersih Lantai', 'Baik', 'Peralatan kebersihan ruang kelas dan laboratorium', 6, 7, '2026-07-24', 4, 'INV-2026-092', 1),
(93, 'Kipas Angin Standing', 'Baik', 'Pendingin tambahan ruang kelas dan kantor', 3, 11, '2026-07-25', 24, 'INV-2026-093', 1),
(94, 'Printer Laser A4', 'Baik', 'Printer untuk kebutuhan dokumentasi sekolah', 2, 6, '2026-07-26', 5, 'INV-2026-094', 1),
(95, 'Tablet Pembelajaran', 'Baik', 'Tablet belajar untuk kegiatan digital siswa', 5, 9, '2026-07-27', 10, 'INV-2026-095', 1),
(96, 'Kamera Pengawas Ruangan', 'Baik', 'Kamera keamanan untuk ruang server dan aula', 2, 8, '2026-07-28', 18, 'INV-2026-096', 1),
(97, 'Duplicator USB', 'Baik', 'Perangkat penyimpanan dan transfer data', 5, 10, '2026-07-29', 11, 'INV-2026-097', 1),
(98, 'Meja Baca Perpustakaan', 'Baik', 'Meja baca siswa dan pengunjung perpustakaan', 10, 11, '2026-07-30', 10, 'INV-2026-098', 1),
(99, 'Kursi Baca Perpustakaan', 'Baik', 'Kursi baca untuk area perpustakaan', 12, 11, '2026-07-31', 10, 'INV-2026-099', 1),
(100, 'Modul Pembelajaran Interaktif', 'Baik', 'Perangkat media pembelajaran digital dan praktik', 8, 12, '2026-08-01', 10, 'INV-2026-100', 1);

INSERT INTO `inventaris`
  (`nama`, `kondisi`, `keterangan`, `jumlah`, `id_jenis`, `tanggal_register`, `id_ruang`, `kode_inventaris`, `id_petugas`)
VALUES
('Interactive Flat Panel', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 9, '2026-11-13', 12, 'INV-ELEK-001', 1),
('AC', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 10, '2026-11-13', 12, 'INV-ELEK-002', 1),
('CCTV', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 8, '2026-11-13', 12, 'INV-ELEK-003', 1),
('Dispenser', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 10, '2026-11-13', 12, 'INV-ELEK-004', 1),
('Kipas Angin/Exhaust fan', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 7, '2026-11-13', 12, 'INV-ELEK-005', 1),
('Layar Proyektor', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 4, '2026-11-13', 12, 'INV-ELEK-006', 1),
('Mesin Fotokopi', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 6, '2026-11-13', 12, 'INV-ELEK-007', 1),
('Microphone', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 4, '2026-11-13', 12, 'INV-ELEK-008', 1),
('Mixer Suara', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 4, '2026-11-13', 12, 'INV-ELEK-009', 1),
('Printer', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 6, '2026-11-13', 12, 'INV-ELEK-010', 1),
('Proyektor/Infokus', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 9, '2026-11-13', 12, 'INV-ELEK-011', 1),
('Scanner', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 6, '2026-11-13', 12, 'INV-ELEK-012', 1),
('Telepon Kantor', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 6, '2026-11-13', 12, 'INV-ELEK-013', 1),
('TV', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 4, '2026-11-13', 12, 'INV-ELEK-014', 1),
('Sound Sistem', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 4, '2026-11-13', 12, 'INV-ELEK-015', 1),
('Ampli Gitar', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 4, '2026-11-13', 12, 'INV-ELEK-016', 1),
('Speaker Portable', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 4, '2026-11-13', 12, 'INV-ELEK-017', 1),
('Speaker Kecil', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 4, '2026-11-13', 12, 'INV-ELEK-019', 1),
('Terminal stop kontak', 'Baik', 'Barang tambahan dari daftar elektronik', 1, 10, '2026-11-13', 12, 'INV-ELEK-020', 1);

INSERT INTO `inventaris`
  (`nama`, `kondisi`, `keterangan`, `jumlah`, `id_jenis`, `tanggal_register`, `id_ruang`, `kode_inventaris`, `id_petugas`)
SELECT item.`nama`, item.`kondisi`, 'Barang tambahan dari daftar alat praktik/lab', 1, item.`id_jenis`, '2026-11-13', 12, item.`kode`, 1
FROM (
  SELECT 'Komputer Akuntansi' AS `nama`, 9 AS `id_jenis`, 'Baik' AS `kondisi`, 'INV-LAB-001' AS `kode`
  UNION ALL SELECT 'Komputer Animasi', 9, 'Baik', 'INV-LAB-002'
  UNION ALL SELECT 'Komputer DKV', 9, 'Baik', 'INV-LAB-003'
  UNION ALL SELECT 'Komputer Pemasaran', 9, 'Baik', 'INV-LAB-004'
  UNION ALL SELECT 'Komputer PPLG 1', 9, 'Baik', 'INV-LAB-005'
  UNION ALL SELECT 'Komputer PPLG 2', 9, 'Baik', 'INV-LAB-006'
  UNION ALL SELECT 'Komputer PPLG 3', 9, 'Baik', 'INV-LAB-007'
  UNION ALL SELECT 'Komputer PPLG 4', 9, 'Baik', 'INV-LAB-008'
  UNION ALL SELECT 'Komputer PPLG 5', 9, 'Baik', 'INV-LAB-009'
  UNION ALL SELECT 'LapTop', 9, 'Baik', 'INV-LAB-010'
  UNION ALL SELECT 'Mesin Cutting', 2, 'Rusak Ringan', 'INV-LAB-011'
  UNION ALL SELECT 'Mesin Laminasi', 2, 'Baik', 'INV-LAB-012'
  UNION ALL SELECT 'Mesin Oven', 2, 'Baik', 'INV-LAB-013'
  UNION ALL SELECT 'Mesin Pres', 2, 'Rusak Ringan', 'INV-LAB-014'
  UNION ALL SELECT 'Printer 3D', 2, 'Baik', 'INV-LAB-015'
  UNION ALL SELECT 'Printer DTF', 2, 'Baik', 'INV-LAB-016'
  UNION ALL SELECT 'Printer Foto', 6, 'Baik', 'INV-LAB-017'
  UNION ALL SELECT 'Printer Sublim', 2, 'Baik', 'INV-LAB-018'
  UNION ALL SELECT 'Printer UV', 2, 'Rusak Ringan', 'INV-LAB-019'
  UNION ALL SELECT 'Stabilizer/ PSU', 2, 'Baik', 'INV-LAB-020'
  UNION ALL SELECT 'Stand Lighting', 4, 'Baik', 'INV-LAB-021'
  UNION ALL SELECT 'Stand Microphone', 4, 'Baik', 'INV-LAB-022'
  UNION ALL SELECT 'Tripod Camera', 4, 'Baik', 'INV-LAB-023'
  UNION ALL SELECT 'Kamera MD 1000', 4, 'Baik', 'INV-LAB-024'
  UNION ALL SELECT 'Tripod Kamera', 4, 'Baik', 'INV-LAB-025'
  UNION ALL SELECT 'Soft Box Standar', 4, 'Baik', 'INV-LAB-026'
  UNION ALL SELECT 'Lampu LED', 4, 'Baik', 'INV-LAB-027'
  UNION ALL SELECT 'Tripod Kecil', 4, 'Baik', 'INV-LAB-028'
  UNION ALL SELECT 'Tuled + Bracket', 4, 'Baik', 'INV-LAB-029'
  UNION ALL SELECT 'Background Hijau', 4, 'Baik', 'INV-LAB-030'
  UNION ALL SELECT 'Lampu Sorot', 4, 'Baik', 'INV-LAB-031'
  UNION ALL SELECT 'Komputer Lab Broadcast', 9, 'Baik', 'INV-LAB-032'
  UNION ALL SELECT 'Audio Mixer', 4, 'Baik', 'INV-LAB-033'
  UNION ALL SELECT 'Video Mixer', 4, 'Rusak Ringan', 'INV-LAB-034'
  UNION ALL SELECT 'Rekording Pack', 4, 'Baik', 'INV-LAB-035'
  UNION ALL SELECT 'Speaker', 4, 'Baik', 'INV-LAB-036'
  UNION ALL SELECT 'Telepromter', 4, 'Baik', 'INV-LAB-037'
  UNION ALL SELECT 'Mic Wireless', 4, 'Baik', 'INV-LAB-038'
  UNION ALL SELECT 'Kamera Mirrorless', 4, 'Baik', 'INV-LAB-039'
  UNION ALL SELECT 'Drone', 4, 'Rusak Ringan', 'INV-LAB-040'
  UNION ALL SELECT 'kamera Digital', 4, 'Baik', 'INV-LAB-041'
  UNION ALL SELECT 'Oculus', 9, 'Baik', 'INV-LAB-042'
  UNION ALL SELECT 'Lampu Sorot', 4, 'Baik', 'INV-LAB-043'
  UNION ALL SELECT 'Lampu Studi', 4, 'Baik', 'INV-LAB-044'
  UNION ALL SELECT 'Kamera DSLR', 4, 'Baik', 'INV-LAB-045'
  UNION ALL SELECT 'Kamera DSLR', 4, 'Rusak Ringan', 'INV-LAB-046'
  UNION ALL SELECT 'Kamera DSLR', 4, 'Baik', 'INV-LAB-047'
  UNION ALL SELECT 'Lensa', 4, 'Baik', 'INV-LAB-048'
  UNION ALL SELECT 'Lensa', 4, 'Baik', 'INV-LAB-049'
  UNION ALL SELECT 'Lensa', 4, 'Baik', 'INV-LAB-050'
  UNION ALL SELECT 'Lampu Studio', 4, 'Baik', 'INV-LAB-051'
  UNION ALL SELECT 'Flash', 4, 'Baik', 'INV-LAB-052'
  UNION ALL SELECT 'Background', 4, 'Baik', 'INV-LAB-053'
  UNION ALL SELECT 'Tripod Besar', 4, 'Baik', 'INV-LAB-054'
  UNION ALL SELECT 'Soft Box Besar', 4, 'Rusak Ringan', 'INV-LAB-055'
  UNION ALL SELECT 'Soft Box Sedang', 4, 'Baik', 'INV-LAB-056'
  UNION ALL SELECT 'Komputer Mac', 9, 'Baik', 'INV-LAB-057'
  UNION ALL SELECT 'Printer Foto', 6, 'Baik', 'INV-LAB-058'
  UNION ALL SELECT 'hub switch', 5, 'Baik', 'INV-LAB-059'
  UNION ALL SELECT 'Haedphone', 4, 'Baik', 'INV-LAB-060'
  UNION ALL SELECT 'Pan Tablet', 9, 'Baik', 'INV-LAB-061'
  UNION ALL SELECT 'Server', 2, 'Rusak Ringan', 'INV-LAB-062'
  UNION ALL SELECT 'Access Point', 5, 'Baik', 'INV-LAB-063'
  UNION ALL SELECT 'Kalkulator', 9, 'Baik', 'INV-LAB-064'
  UNION ALL SELECT 'Mesin Kasir', 6, 'Baik', 'INV-LAB-065'
  UNION ALL SELECT 'meja cetak sablon', 9, 'Baik', 'INV-LAB-066'
  UNION ALL SELECT 'Alat Cetak Pin', 9, 'Baik', 'INV-LAB-067'
  UNION ALL SELECT 'Alat Cetak Mug', 9, 'Baik', 'INV-LAB-068'
  UNION ALL SELECT 'Alat musik', 4, 'Baik', 'INV-LAB-069'
  UNION ALL SELECT 'Kendang', 4, 'Baik', 'INV-LAB-070'
  UNION ALL SELECT 'Gamelan Degung Pelog selap Salendro', 4, 'Rusak Ringan', 'INV-LAB-071'
  UNION ALL SELECT 'Angklung Melodi', 4, 'Baik', 'INV-LAB-072'
  UNION ALL SELECT 'Angklung Bass Partay', 4, 'Baik', 'INV-LAB-073'
  UNION ALL SELECT 'Accompangemen', 4, 'Baik', 'INV-LAB-074'
  UNION ALL SELECT 'Cuk', 4, 'Baik', 'INV-LAB-075'
  UNION ALL SELECT 'Bass Pukul', 4, 'Baik', 'INV-LAB-076'
  UNION ALL SELECT 'Gambang Pengiring', 4, 'Baik', 'INV-LAB-077'
  UNION ALL SELECT 'Drum', 4, 'Baik', 'INV-LAB-078'
  UNION ALL SELECT 'Gitar Akustik', 4, 'Baik', 'INV-LAB-079'
  UNION ALL SELECT 'Gitar Bass', 4, 'Baik', 'INV-LAB-080'
  UNION ALL SELECT 'Matras', 9, 'Rusak Ringan', 'INV-LAB-081'
  UNION ALL SELECT 'Bola Voli', 9, 'Baik', 'INV-LAB-082'
  UNION ALL SELECT 'Bola Basket', 9, 'Baik', 'INV-LAB-083'
  UNION ALL SELECT 'Bola Futsal', 9, 'Baik', 'INV-LAB-084'
  UNION ALL SELECT 'Net Voli', 9, 'Baik', 'INV-LAB-085'
  UNION ALL SELECT 'Net Badminton', 9, 'Baik', 'INV-LAB-086'
  UNION ALL SELECT 'Stik Kasti', 9, 'Baik', 'INV-LAB-087'
) AS item
WHERE NOT EXISTS (
  SELECT 1
  FROM `inventaris` existing
  WHERE existing.`kode_inventaris` = item.`kode`
);

-- Pastikan setiap inventaris memiliki stok minimal 5 unit dan catatan kondisi.
UPDATE `inventaris`
SET
  `jumlah` = GREATEST(`jumlah`, 5),
  `kondisi` = CASE
    WHEN MOD(`id_inventaris`, 10) IN (3, 8) THEN 'Rusak Berat'
    WHEN MOD(`id_inventaris`, 10) IN (2, 7) THEN 'Rusak Ringan'
    ELSE 'Baik'
  END,
  `keterangan` = CONCAT(
    COALESCE(NULLIF(`keterangan`, ''), 'Barang inventaris sekolah.'),
    CASE
      WHEN MOD(`id_inventaris`, 10) IN (3, 8)
        THEN ' Kondisi rusak berat, tidak dapat digunakan dan perlu perbaikan atau penggantian.'
      WHEN MOD(`id_inventaris`, 10) IN (2, 7)
        THEN ' Kondisi rusak ringan, masih dapat digunakan dan perlu perbaikan.'
      ELSE ' Kondisi baik, berfungsi normal dan siap digunakan.'
    END
  );

-- ----------------------------------------------------------
-- 7. Tabel peminjaman
-- ----------------------------------------------------------
CREATE TABLE `peminjaman` (
  `id_peminjaman` INT AUTO_INCREMENT PRIMARY KEY,
  `kode_peminjaman` VARCHAR(50) NOT NULL UNIQUE,
  `tanggal_pinjam` DATE NOT NULL,
  `tanggal_kembali` DATE NULL,
  `status_peminjaman` ENUM('Menunggu Konfirmasi', 'Dipinjam', 'Sudah Kembali', 'Ditolak') NOT NULL DEFAULT 'Dipinjam',
  `id_pegawai` INT NOT NULL,
  CONSTRAINT `fk_peminjaman_pegawai` FOREIGN KEY (`id_pegawai`) REFERENCES `pegawai` (`id_pegawai`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------------------------------------
-- 8. Tabel detail_pinjam
-- ----------------------------------------------------------
CREATE TABLE `detail_pinjam` (
  `id_detail_pinjam` INT AUTO_INCREMENT PRIMARY KEY,
  `id_inventaris` INT NOT NULL,
  `jumlah` INT NOT NULL,
  `id_peminjaman` INT NOT NULL,
  CONSTRAINT `fk_detail_inventaris` FOREIGN KEY (`id_inventaris`) REFERENCES `inventaris` (`id_inventaris`) ON UPDATE CASCADE,
  CONSTRAINT `fk_detail_peminjaman` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id_peminjaman`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample peminjaman
INSERT INTO `peminjaman` (`id_peminjaman`, `kode_peminjaman`, `tanggal_pinjam`, `tanggal_kembali`, `status_peminjaman`, `id_pegawai`) VALUES
(1, 'PJ-20261101-001', '2026-11-01', '2026-11-03', 'Sudah Kembali', 2),
(2, 'PJ-20261108-002', '2026-11-08', NULL, 'Dipinjam', 4);

INSERT INTO `detail_pinjam` (`id_detail_pinjam`, `id_inventaris`, `jumlah`, `id_peminjaman`) VALUES
(1, 2, 1, 1),
(2, 1, 1, 2);

-- Update stok barang yang sedang dipinjam (INV 1 berkurang 1)
UPDATE `inventaris` SET `jumlah` = `jumlah` - 1 WHERE `id_inventaris` = 1;


-- Stored Procedures
DELIMITER $$

-- Procedure tambah peminjaman & potong stok
DROP PROCEDURE IF EXISTS `sp_tambah_peminjaman`$$
CREATE PROCEDURE `sp_tambah_peminjaman`(
    IN p_kode_peminjaman VARCHAR(50),
    IN p_tanggal_pinjam DATE,
    IN p_id_pegawai INT,
    IN p_id_inventaris INT,
    IN p_jumlah INT,
    IN p_status VARCHAR(30),
    OUT p_status_code INT,
    OUT p_status_msg VARCHAR(255)
)
BEGIN
    DECLARE v_stok_tersedia INT;
    DECLARE v_id_peminjaman INT;

    -- Periksa stok inventaris
    SELECT `jumlah` INTO v_stok_tersedia 
    FROM `inventaris` 
    WHERE `id_inventaris` = p_id_inventaris;

    IF v_stok_tersedia IS NULL THEN
        SET p_status_code = 0;
        SET p_status_msg = 'Barang inventaris tidak ditemukan.';
    ELSEIF v_stok_tersedia < p_jumlah THEN
        SET p_status_code = 0;
        SET p_status_msg = CONCAT('Stok tidak mencukupi. Sisa stok: ', v_stok_tersedia);
    ELSE
        -- Simpan ke tabel peminjaman
        INSERT INTO `peminjaman` (`kode_peminjaman`, `tanggal_pinjam`, `tanggal_kembali`, `status_peminjaman`, `id_pegawai`)
        VALUES (p_kode_peminjaman, p_tanggal_pinjam, NULL, p_status, p_id_pegawai);

        SET v_id_peminjaman = LAST_INSERT_ID();

        -- Simpan ke tabel detail_pinjam
        INSERT INTO `detail_pinjam` (`id_peminjaman`, `id_inventaris`, `jumlah`)
        VALUES (v_id_peminjaman, p_id_inventaris, p_jumlah);

        -- Kurangi jumlah stok jika status 'Dipinjam'
        IF p_status = 'Dipinjam' THEN
            UPDATE `inventaris` 
            SET `jumlah` = `jumlah` - p_jumlah 
            WHERE `id_inventaris` = p_id_inventaris;
        END IF;

        SET p_status_code = 1;
        SET p_status_msg = 'Peminjaman berhasil dicatat.';
    END IF;
END$$

-- Prosedur 2: Pengembalian Peminjaman dan Pengembalian Stok Otomatis
DROP PROCEDURE IF EXISTS `sp_kembalikan_peminjaman`$$
CREATE PROCEDURE `sp_kembalikan_peminjaman`(
    IN p_id_peminjaman INT,
    IN p_tanggal_kembali DATE,
    OUT p_status_code INT,
    OUT p_status_msg VARCHAR(255)
)
BEGIN
    DECLARE v_status_sekarang VARCHAR(30);
    DECLARE v_id_inventaris INT;
    DECLARE v_jumlah INT;

    -- Cek status peminjaman saat ini
    SELECT `status_peminjaman` INTO v_status_sekarang
    FROM `peminjaman`
    WHERE `id_peminjaman` = p_id_peminjaman;

    IF v_status_sekarang IS NULL THEN
        SET p_status_code = 0;
        SET p_status_msg = 'Data peminjaman tidak ditemukan.';
    ELSEIF v_status_sekarang = 'Sudah Kembali' THEN
        SET p_status_code = 0;
        SET p_status_msg = 'Barang untuk peminjaman ini sudah pernah dikembalikan sebelumnya.';
    ELSE
        -- Ambil data barang dan jumlah dari detail_pinjam
        SELECT `id_inventaris`, `jumlah` INTO v_id_inventaris, v_jumlah
        FROM `detail_pinjam`
        WHERE `id_peminjaman` = p_id_peminjaman
        LIMIT 1;

        -- Update status peminjaman
        UPDATE `peminjaman`
        SET `status_peminjaman` = 'Sudah Kembali',
            `tanggal_kembali` = p_tanggal_kembali
        WHERE `id_peminjaman` = p_id_peminjaman;

        -- Kembalikan stok inventaris
        IF v_id_inventaris IS NOT NULL AND v_jumlah IS NOT NULL THEN
            UPDATE `inventaris`
            SET `jumlah` = `jumlah` + v_jumlah
            WHERE `id_inventaris` = v_id_inventaris;
        END IF;

        SET p_status_code = 1;
        SET p_status_msg = 'Pengembalian barang berhasil dicatat dan stok telah dikembalikan.';
    END IF;
END$$

-- Prosedur 3: Ringkasan Dashboard
DROP PROCEDURE IF EXISTS `sp_ringkasan_dashboard`$$
CREATE PROCEDURE `sp_ringkasan_dashboard`()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM `inventaris`) AS total_inventaris,
        (SELECT IFNULL(SUM(`jumlah`), 0) FROM `inventaris`) AS total_stok_barang,
        (SELECT COUNT(*) FROM `peminjaman` WHERE `status_peminjaman` = 'Dipinjam') AS total_sedang_dipinjam,
        (SELECT COUNT(*) FROM `ruang`) AS total_ruang,
        (SELECT COUNT(*) FROM `pegawai`) AS total_pegawai,
        (SELECT COUNT(*) FROM `jenis`) AS total_jenis;
END$$

-- Prosedur 4: Laporan Peminjaman Berdasarkan Rentang Tanggal
DROP PROCEDURE IF EXISTS `sp_laporan_peminjaman`$$
CREATE PROCEDURE `sp_laporan_peminjaman`(
    IN p_tgl_awal DATE,
    IN p_tgl_akhir DATE,
    IN p_status VARCHAR(50)
)
BEGIN
    SELECT 
        p.`id_peminjaman`,
        p.`kode_peminjaman`,
        p.`tanggal_pinjam`,
        p.`tanggal_kembali`,
        p.`status_peminjaman`,
        pg.`nama_pegawai`,
        pg.`nip`,
        i.`kode_inventaris`,
        i.`nama` AS nama_barang,
        j.`nama_jenis`,
        r.`nama_ruang`,
        dp.`jumlah`
    FROM `peminjaman` p
    INNER JOIN `pegawai` pg ON p.`id_pegawai` = pg.`id_pegawai`
    INNER JOIN `detail_pinjam` dp ON p.`id_peminjaman` = dp.`id_peminjaman`
    INNER JOIN `inventaris` i ON dp.`id_inventaris` = i.`id_inventaris`
    LEFT JOIN `jenis` j ON i.`id_jenis` = j.`id_jenis`
    LEFT JOIN `ruang` r ON i.`id_ruang` = r.`id_ruang`
    WHERE (p.`tanggal_pinjam` BETWEEN p_tgl_awal AND p_tgl_akhir)
      AND (p_status = 'ALL' OR p.`status_peminjaman` = p_status)
    ORDER BY p.`tanggal_pinjam` DESC, p.`id_peminjaman` DESC;
END$$

DELIMITER ;
