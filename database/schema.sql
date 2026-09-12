-- =====================================================
-- Sistem Manajemen Tata Tertib & Penghargaan Siswa
-- DBMS: MySQL 8 / MariaDB 10.6+  |  Charset: utf8mb4
-- Sekolah default: SMK Negeri 1 Krangkeng
-- -----------------------------------------------------
-- CARA PAKAI:
--   mysql -u root < database/schema.sql
--   (otomatis buat DB `tatibapp` + isi data dummy)
-- AKUN DUMMY:
--   admin/admin123 | kepsek/kepsek123 | wakasek/wakasek123
--   wali/wali123 (Wali Kelas X TKJ 1) | osis/osis123
-- DATA DUMMY:
--   3 kelas, 10 siswa, 19 laporan pelanggaran (Jan-Sep 2026,
--   termasuk 2 pending), 16 laporan penghargaan (termasuk
--   2 pending), 6 tindakan (SP1x2, SP2x2, SP3, PENGEMBALIAN),
--   3 sertifikat (Berprestasi/Hadiah/Waluya Utama), 5 log.
-- ATURAN POIN:
--   Pelanggaran: 25-50 SP1, 51-75 SP2, >=76 SP3,
--   Force Majeure -> PENGEMBALIAN + status dikembalikan.
--   Penghargaan: 100-125 BERPRESTASI, 126-150 HADIAH,
--   >=151 WALUYA_UTAMA.
-- DUMP OTOMATIS dari database berjalan (struktur + data).
-- =====================================================
CREATE DATABASE IF NOT EXISTS tatibapp
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tatibapp;

-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: tatibapp
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `jenis_pelanggaran`
--

DROP TABLE IF EXISTS `jenis_pelanggaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_pelanggaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kategori` enum('ringan','sedang','berat','hukum_asusila') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ringan',
  `bobot_poin` int NOT NULL,
  `is_force_majeure_default` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = otomatis usulkan pengembalian',
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`),
  CONSTRAINT `jenis_pelanggaran_chk_1` CHECK ((`bobot_poin` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenis_pelanggaran`
--

LOCK TABLES `jenis_pelanggaran` WRITE;
/*!40000 ALTER TABLE `jenis_pelanggaran` DISABLE KEYS */;
INSERT INTO `jenis_pelanggaran` VALUES (1,'R01','Terlambat masuk sekolah','ringan',5,0,NULL,1),(2,'R02','Tidak memakai atribut lengkap','ringan',5,0,NULL,1),(3,'S01','Bolhos tanpa keterangan','sedang',15,0,NULL,1),(4,'S02','Merokok di lingkungan sekolah','sedang',20,0,NULL,1),(5,'B01','Perkelahian / bullying','berat',40,0,NULL,1),(6,'H01','Narkoba / miras / asusila / kriminal','hukum_asusila',76,1,NULL,1);
/*!40000 ALTER TABLE `jenis_pelanggaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jenis_penghargaan`
--

DROP TABLE IF EXISTS `jenis_penghargaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_penghargaan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bobot_poin` int NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`),
  CONSTRAINT `jenis_penghargaan_chk_1` CHECK ((`bobot_poin` > 0))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenis_penghargaan`
--

LOCK TABLES `jenis_penghargaan` WRITE;
/*!40000 ALTER TABLE `jenis_penghargaan` DISABLE KEYS */;
INSERT INTO `jenis_penghargaan` VALUES (1,'P01','Juara 1 lomba akademik/non-akademik tingkat kabupaten',50,NULL,1),(2,'P02','Juara 2-3 lomba tingkat kabupaten',30,NULL,1),(3,'P03','Petugas upacara / kegiatan teladan',10,NULL,1),(4,'P04','Juara tingkat provinsi/nasional',75,NULL,1);
/*!40000 ALTER TABLE `jenis_penghargaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kelas`
--

DROP TABLE IF EXISTS `kelas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kelas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_kelas` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tingkat` enum('X','XI','XII') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jurusan` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_ajaran` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wali_kelas_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_kelas_wali` (`wali_kelas_id`),
  CONSTRAINT `fk_kelas_wali` FOREIGN KEY (`wali_kelas_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kelas`
--

LOCK TABLES `kelas` WRITE;
/*!40000 ALTER TABLE `kelas` DISABLE KEYS */;
INSERT INTO `kelas` VALUES (1,'X TKJ 1','X','TKJ','2025/2026',5),(2,'XI TKJ 1','XI','TKJ','2025/2026',NULL),(3,'XII TKJ 1','XII','TKJ','2025/2026',NULL);
/*!40000 ALTER TABLE `kelas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laporan_pelanggaran`
--

DROP TABLE IF EXISTS `laporan_pelanggaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laporan_pelanggaran` (
  `id` int NOT NULL AUTO_INCREMENT,
  `siswa_id` int NOT NULL,
  `jenis_id` int NOT NULL,
  `pelapor_id` int DEFAULT NULL COMMENT 'user pelapor (osis/wali/admin)',
  `tanggal` date NOT NULL,
  `tahun_ajaran` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kronologi` text COLLATE utf8mb4_unicode_ci,
  `foto_bukti_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'path relatif ex: uploads/bukti/xxxx.jpg (opsional)',
  `bobot_poin_saat_lapor` int NOT NULL COMMENT 'snapshot bobot master saat dilapor',
  `is_force_majeure` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'flag pelanggaran berat langsung dikembalikan',
  `status_validasi` enum('pending','divalidasi','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `divalidasi_oleh` int DEFAULT NULL,
  `catatan_validasi` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_langgar_siswa` (`siswa_id`),
  KEY `fk_langgar_jenis` (`jenis_id`),
  KEY `fk_langgar_pelapor` (`pelapor_id`),
  KEY `fk_langgar_validator` (`divalidasi_oleh`),
  KEY `idx_langgar_tgl` (`tanggal`),
  KEY `idx_langgar_status` (`status_validasi`),
  CONSTRAINT `fk_langgar_jenis` FOREIGN KEY (`jenis_id`) REFERENCES `jenis_pelanggaran` (`id`),
  CONSTRAINT `fk_langgar_pelapor` FOREIGN KEY (`pelapor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_langgar_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_langgar_validator` FOREIGN KEY (`divalidasi_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laporan_pelanggaran`
--

LOCK TABLES `laporan_pelanggaran` WRITE;
/*!40000 ALTER TABLE `laporan_pelanggaran` DISABLE KEYS */;
INSERT INTO `laporan_pelanggaran` VALUES (1,2,3,4,'2026-01-08','2025/2026','Bolhos tanpa keterangan pada jam pelajaran pertama.',NULL,15,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(2,7,1,5,'2026-01-12','2025/2026','Terlambat masuk gerbang sekolah 20 menit.',NULL,5,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(3,10,5,4,'2026-01-20','2025/2026','Terlibat perkelahian dengan siswa lain di kantin.',NULL,40,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(4,1,1,4,'2026-02-03','2025/2026','Terlambat mengikuti upacara bendera.',NULL,5,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(5,4,5,5,'2026-02-10','2025/2026','Perkelahian di lapangan saat istirahat.',NULL,40,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(6,7,1,4,'2026-02-17','2025/2026','Terlambat masuk kelas setelah istirahat.',NULL,5,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(7,10,4,4,'2026-02-24','2025/2026','Merokok di area parkir sekolah.',NULL,20,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(8,1,2,5,'2026-03-05','2025/2026','Tidak memakai dasi dan topi saat upacara.',NULL,5,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(9,5,4,4,'2026-03-11','2025/2026','Merokok di belakang laboratorium.',NULL,20,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(10,7,2,4,'2026-03-19','2025/2026','Seragam tidak lengkap (tanpa badge).',NULL,5,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(11,2,4,5,'2026-04-07','2025/2026','Merokok di toilet sekolah.',NULL,20,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(12,3,5,4,'2026-05-06','2025/2026','Bullying verbal terhadap adik kelas.',NULL,40,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(13,3,3,5,'2026-06-09','2025/2026','Bolhos saat jam praktik produktif.',NULL,15,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(14,9,4,4,'2026-06-16','2025/2026','Merokok di lingkungan sekolah.',NULL,20,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(15,4,5,4,'2026-07-14','2025/2026','Perkelahian ulang dengan siswa yang sama.',NULL,40,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(16,6,6,1,'2026-08-05','2025/2026','Force majeure: kasus hukum/asusila, langsung dikembalikan ke orang tua.',NULL,76,1,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(17,7,3,5,'2026-08-19','2025/2026','Bolhos tanpa keterangan.',NULL,15,0,'divalidasi',1,NULL,'2026-09-12 14:48:57'),(18,5,2,4,'2026-09-04','2025/2026','Tidak memakai atribut lengkap saat upacara.',NULL,5,0,'pending',NULL,NULL,'2026-09-12 14:48:57'),(19,9,3,4,'2026-09-07','2025/2026','Bolhos, menunggu konfirmasi wali kelas.',NULL,15,0,'pending',NULL,NULL,'2026-09-12 14:48:57');
/*!40000 ALTER TABLE `laporan_pelanggaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laporan_penghargaan`
--

DROP TABLE IF EXISTS `laporan_penghargaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laporan_penghargaan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `siswa_id` int NOT NULL,
  `jenis_id` int NOT NULL,
  `pelapor_id` int DEFAULT NULL,
  `tanggal` date NOT NULL,
  `tahun_ajaran` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `foto_bukti_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'opsional: foto piagam/kegiatan',
  `bobot_poin_saat_lapor` int NOT NULL,
  `status_validasi` enum('pending','divalidasi','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `divalidasi_oleh` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_harga_siswa` (`siswa_id`),
  KEY `fk_harga_jenis` (`jenis_id`),
  KEY `fk_harga_pelapor` (`pelapor_id`),
  KEY `idx_harga_tahun` (`tahun_ajaran`),
  CONSTRAINT `fk_harga_jenis` FOREIGN KEY (`jenis_id`) REFERENCES `jenis_penghargaan` (`id`),
  CONSTRAINT `fk_harga_pelapor` FOREIGN KEY (`pelapor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_harga_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laporan_penghargaan`
--

LOCK TABLES `laporan_penghargaan` WRITE;
/*!40000 ALTER TABLE `laporan_penghargaan` DISABLE KEYS */;
INSERT INTO `laporan_penghargaan` VALUES (1,10,1,5,'2026-01-15','2025/2026','Juara 1 lomba web design tingkat kabupaten.',NULL,50,'divalidasi',1,'2026-09-12 14:48:57'),(2,8,2,5,'2026-02-12','2025/2026','Juara 2 lomba pidato tingkat kabupaten.',NULL,30,'divalidasi',1,'2026-09-12 14:48:57'),(3,8,1,1,'2026-03-10','2025/2026','Juara 1 olimpiade matematika tingkat kabupaten.',NULL,50,'divalidasi',1,'2026-09-12 14:48:57'),(4,10,1,5,'2026-03-22','2025/2026','Juara 1 lomba jaringan komputer tingkat kabupaten.',NULL,50,'divalidasi',1,'2026-09-12 14:48:57'),(5,3,1,5,'2026-04-08','2025/2026','Juara 1 lomba desain poster tingkat kabupaten.',NULL,50,'divalidasi',1,'2026-09-12 14:48:57'),(6,2,3,5,'2026-05-04','2025/2026','Petugas upacara teladan bulan Mei.',NULL,10,'divalidasi',1,'2026-09-12 14:48:57'),(7,3,3,5,'2026-05-20','2025/2026','Petugas upacara teladan.',NULL,10,'divalidasi',1,'2026-09-12 14:48:57'),(8,9,4,1,'2026-05-27','2025/2026','Juara 1 lomba robotik tingkat provinsi.',NULL,75,'divalidasi',1,'2026-09-12 14:48:57'),(9,1,3,5,'2026-06-03','2025/2026','Petugas upacara teladan.',NULL,10,'divalidasi',1,'2026-09-12 14:48:57'),(10,8,4,1,'2026-06-24','2025/2026','Juara 1 olimpiade sains tingkat provinsi.',NULL,75,'divalidasi',1,'2026-09-12 14:48:57'),(11,2,2,5,'2026-07-08','2025/2026','Juara 3 lomba cerdas cermat tingkat kabupaten.',NULL,30,'divalidasi',1,'2026-09-12 14:48:57'),(12,10,2,5,'2026-07-21','2025/2026','Juara 2 lomba karya tulis tingkat kabupaten.',NULL,30,'divalidasi',1,'2026-09-12 14:48:57'),(13,8,3,5,'2026-08-11','2025/2026','Duta literasi sekolah.',NULL,10,'divalidasi',1,'2026-09-12 14:48:57'),(14,9,2,5,'2026-08-26','2025/2026','Juara 2 lomba fotografi tingkat kabupaten.',NULL,30,'divalidasi',1,'2026-09-12 14:48:57'),(15,1,3,5,'2026-09-02','2025/2026','Petugas upacara (menunggu validasi).',NULL,10,'pending',NULL,'2026-09-12 14:48:57'),(16,4,3,4,'2026-09-05','2025/2026','Usulan OSIS: relawan kebersihan (menunggu validasi).',NULL,10,'pending',NULL,'2026-09-12 14:48:57');
/*!40000 ALTER TABLE `laporan_penghargaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `log_aktivitas`
--

DROP TABLE IF EXISTS `log_aktivitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_aktivitas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `aksi` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_log_user` (`user_id`),
  CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `log_aktivitas`
--

LOCK TABLES `log_aktivitas` WRITE;
/*!40000 ALTER TABLE `log_aktivitas` DISABLE KEYS */;
INSERT INTO `log_aktivitas` VALUES (1,1,'login','Admin masuk ke aplikasi.','2026-09-12 14:48:57'),(2,4,'lapor-pelanggaran','OSIS melaporkan Sinta Dewi (B01).','2026-09-12 14:48:57'),(3,1,'validasi','Admin memvalidasi laporan #3.','2026-09-12 14:48:57'),(4,5,'cetak-sp','Wali mencetak SP1 Budi Santoso.','2026-09-12 14:48:57'),(5,1,'update-tampilan','Admin mengganti warna primer tema.','2026-09-12 14:48:57');
/*!40000 ALTER TABLE `log_aktivitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengaturan_sekolah`
--

DROP TABLE IF EXISTS `pengaturan_sekolah`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengaturan_sekolah` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama_sekolah` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'SMK Negeri 1 Krangkeng',
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `telepon` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tahun_ajaran_aktif` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2025/2026',
  `semester_aktif` enum('Ganjil','Genap') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ganjil',
  `logo_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'path relatif ex: uploads/logo/logo-171xxx.png',
  `kepala_sekolah_nama` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kepala_sekolah_nip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengaturan_sekolah`
--

LOCK TABLES `pengaturan_sekolah` WRITE;
/*!40000 ALTER TABLE `pengaturan_sekolah` DISABLE KEYS */;
INSERT INTO `pengaturan_sekolah` VALUES (1,'SMK Negeri 1 Krangkeng','Jl. Raya Krangkeng, Indramayu, Jawa Barat',NULL,NULL,'2025/2026','Ganjil',NULL,'-',NULL,'2026-09-12 14:00:09','2026-09-12 14:00:09');
/*!40000 ALTER TABLE `pengaturan_sekolah` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengaturan_tampilan`
--

DROP TABLE IF EXISTS `pengaturan_tampilan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengaturan_tampilan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `warna_primer` varchar(7) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#0284c7',
  `font_family` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sistem',
  `mode_default` enum('sistem','terang','gelap') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'sistem',
  `custom_css` text COLLATE utf8mb4_unicode_ci,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengaturan_tampilan`
--

LOCK TABLES `pengaturan_tampilan` WRITE;
/*!40000 ALTER TABLE `pengaturan_tampilan` DISABLE KEYS */;
INSERT INTO `pengaturan_tampilan` VALUES (1,'#ae00ff','sistem','sistem',NULL,'2026-09-12 14:45:12');
/*!40000 ALTER TABLE `pengaturan_tampilan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sertifikat_penghargaan`
--

DROP TABLE IF EXISTS `sertifikat_penghargaan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sertifikat_penghargaan` (
  `id` int NOT NULL AUTO_INCREMENT,
  `siswa_id` int NOT NULL,
  `kategori` enum('BERPRESTASI','HADIAH','WALUYA_UTAMA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_poin_saat_itu` int NOT NULL,
  `tahun` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_cetak` date NOT NULL,
  `dicetak_oleh` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_sert_siswa` (`siswa_id`),
  CONSTRAINT `fk_sert_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sertifikat_penghargaan`
--

LOCK TABLES `sertifikat_penghargaan` WRITE;
/*!40000 ALTER TABLE `sertifikat_penghargaan` DISABLE KEYS */;
INSERT INTO `sertifikat_penghargaan` VALUES (1,9,'BERPRESTASI',105,'2025/2026','2026-08-27',1,'2026-09-12 14:48:57'),(2,10,'HADIAH',130,'2025/2026','2026-07-22',1,'2026-09-12 14:48:57'),(3,8,'WALUYA_UTAMA',165,'2025/2026','2026-08-12',1,'2026-09-12 14:48:57');
/*!40000 ALTER TABLE `sertifikat_penghargaan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `siswa`
--

DROP TABLE IF EXISTS `siswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `siswa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nis` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nisn` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_kelamin` enum('L','P') COLLATE utf8mb4_unicode_ci NOT NULL,
  `kelas_id` int DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `nama_ortu` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_hp_ortu` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('aktif','dikembalikan','lulus','pindah') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'aktif',
  `total_poin_pelanggaran` int NOT NULL DEFAULT '0' COMMENT 'cache akumulasi tervalidasi',
  `total_poin_penghargaan` int NOT NULL DEFAULT '0' COMMENT 'cache akumulasi tervalidasi',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nis` (`nis`),
  KEY `idx_siswa_kelas` (`kelas_id`),
  KEY `idx_siswa_status` (`status`),
  CONSTRAINT `fk_siswa_kelas` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `siswa`
--

LOCK TABLES `siswa` WRITE;
/*!40000 ALTER TABLE `siswa` DISABLE KEYS */;
INSERT INTO `siswa` VALUES (1,'1001','001001','Andini Pratiwi','P',1,'Jl. Mawar No.1, Krangkeng','Bpk. Andi','081200000001','aktif',10,10,'2026-09-12 14:48:57'),(2,'1002','001002','Budi Santoso','L',1,'Jl. Melati No.2, Krangkeng','Bpk. Santo','081200000002','aktif',35,40,'2026-09-12 14:48:57'),(3,'1003','001003','Citra Ayu','P',2,'Jl. Kenanga No.3, Krangkeng','Ibu Citra','081200000003','aktif',55,60,'2026-09-12 14:48:57'),(4,'1004','001004','Dedi Kurniawan','L',2,'Jl. Anggrek No.4, Krangkeng','Bpk. Kurna','081200000004','aktif',80,0,'2026-09-12 14:48:57'),(5,'1005','001005','Erik Maulana','L',3,'Jl. Dahlia No.5, Krangkeng','Bpk. Maul','081200000005','aktif',20,0,'2026-09-12 14:48:57'),(6,'1006','001006','Fitri Rahma','P',3,'Jl. Teratai No.6, Krangkeng','Ibu Rahma','081200000006','dikembalikan',76,0,'2026-09-12 14:48:57'),(7,'1007','001007','Galih Prakoso','L',1,'Jl. Flamboyan No.7, Krangkeng','Bpk. Prako','081200000007','aktif',30,0,'2026-09-12 14:48:57'),(8,'1008','001008','Hana Salsabila','P',2,'Jl. Cempaka No.8, Krangkeng','Ibu Salsa','081200000008','aktif',0,165,'2026-09-12 14:48:57'),(9,'1009','001009','Ilham Ramadhan','L',3,'Jl. Kamboja No.9, Krangkeng','Bpk. Rama','081200000009','aktif',20,105,'2026-09-12 14:48:57'),(10,'1010','001010','Sinta Dewi','P',1,'Jl. Sawo No.10, Krangkeng','Ibu Dewi','081200000010','aktif',60,130,'2026-09-12 14:48:57');
/*!40000 ALTER TABLE `siswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tindakan_disiplin`
--

DROP TABLE IF EXISTS `tindakan_disiplin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tindakan_disiplin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `siswa_id` int NOT NULL,
  `jenis` enum('SP1','SP2','SP3','PENGEMBALIAN') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_poin_saat_itu` int NOT NULL,
  `tanggal_cetak` date NOT NULL,
  `dicetak_oleh` int DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_tindak_siswa` (`siswa_id`),
  KEY `fk_tindak_user` (`dicetak_oleh`),
  CONSTRAINT `fk_tindak_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tindak_user` FOREIGN KEY (`dicetak_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tindakan_disiplin`
--

LOCK TABLES `tindakan_disiplin` WRITE;
/*!40000 ALTER TABLE `tindakan_disiplin` DISABLE KEYS */;
INSERT INTO `tindakan_disiplin` VALUES (1,2,'SP1',35,'2026-04-08',1,'Pembinaan tahap pertama oleh wali kelas.','2026-09-12 14:48:57'),(2,7,'SP1',30,'2026-08-20',1,'Pembinaan tahap pertama oleh wali kelas.','2026-09-12 14:48:57'),(3,3,'SP2',55,'2026-06-10',1,'Pemanggilan orang tua, perjanjian kedua.','2026-09-12 14:48:57'),(4,10,'SP2',60,'2026-02-25',1,'Pemanggilan orang tua, perjanjian kedua.','2026-09-12 14:48:57'),(5,4,'SP3',80,'2026-07-15',1,'Skorsing + pembinaan intensif BK.','2026-09-12 14:48:57'),(6,6,'PENGEMBALIAN',76,'2026-08-06',1,'Force majeure: dikembalikan kepada orang tua.','2026-09-12 14:48:57');
/*!40000 ALTER TABLE `tindakan_disiplin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin','kepsek','wakasek','wali_kelas','osis') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrator','admin','$2y$12$OB35S88p/xP4EBFn6jmtXOkrzewffdgd1P7a8480Ua80uPXsoAUsO','admin',1,'2026-09-12 14:00:09'),(2,'Kepala Sekolah','kepsek','$2y$12$yH5d5B87z7QJJDze0XXIKewzA/jP6ghIXwjJtFeTE9YlIx3TYnOuS','kepsek',1,'2026-09-12 14:00:09'),(3,'Wakasek Kesiswaan','wakasek','$2y$12$ADagWdwgJz.40wKPuLl3JOd4mpvulN.mKimjCTwQfsRwDhs.kukwq','wakasek',1,'2026-09-12 14:00:09'),(4,'OSIS','osis','$2y$12$uim6/k2lcXbKKNixDOoyJekD0zXAiNr/M3RjYAvSpU.RZsr34issi','osis',1,'2026-09-12 14:00:09'),(5,'Wali Kelas X','wali','$2y$12$tBqpjRwVVrZ34K5sWwcHVeM6Tba9wU06vglkzlbqHW/VxNgFBhqee','wali_kelas',1,'2026-09-12 14:05:18');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `v_rekap_siswa`
--

DROP TABLE IF EXISTS `v_rekap_siswa`;
/*!50001 DROP VIEW IF EXISTS `v_rekap_siswa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_rekap_siswa` AS SELECT 
 1 AS `id`,
 1 AS `nis`,
 1 AS `nama`,
 1 AS `kelas_id`,
 1 AS `nama_kelas`,
 1 AS `status`,
 1 AS `poin_pelanggaran`,
 1 AS `poin_penghargaan`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `v_rekap_siswa`
--

/*!50001 DROP VIEW IF EXISTS `v_rekap_siswa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_rekap_siswa` AS select `s`.`id` AS `id`,`s`.`nis` AS `nis`,`s`.`nama` AS `nama`,`s`.`kelas_id` AS `kelas_id`,`k`.`nama_kelas` AS `nama_kelas`,`s`.`status` AS `status`,coalesce((select sum(`laporan_pelanggaran`.`bobot_poin_saat_lapor`) from `laporan_pelanggaran` where ((`laporan_pelanggaran`.`siswa_id` = `s`.`id`) and (`laporan_pelanggaran`.`status_validasi` = 'divalidasi'))),0) AS `poin_pelanggaran`,coalesce((select sum(`laporan_penghargaan`.`bobot_poin_saat_lapor`) from `laporan_penghargaan` where ((`laporan_penghargaan`.`siswa_id` = `s`.`id`) and (`laporan_penghargaan`.`status_validasi` = 'divalidasi'))),0) AS `poin_penghargaan` from (`siswa` `s` left join `kelas` `k` on((`k`.`id` = `s`.`kelas_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-12 21:49:23

