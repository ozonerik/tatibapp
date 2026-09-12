-- =====================================================
-- SEED DUMMY SITATIB (idempotent, boleh dijalankan ulang)
-- Merapikan data transaksi + mengisi 10 siswa, laporan
-- tersebar Jan-Sep 2026, SP1/SP2/SP3, Force Majeure,
-- Berprestasi/Hadiah/Waluya Utama.
-- =====================================================
USE tatibapp;
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE log_aktivitas;
TRUNCATE TABLE sertifikat_penghargaan;
TRUNCATE TABLE tindakan_disiplin;
TRUNCATE TABLE laporan_penghargaan;
TRUNCATE TABLE laporan_pelanggaran;
TRUNCATE TABLE siswa;
TRUNCATE TABLE kelas;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------- Kelas (3) ----------
INSERT INTO kelas (id, nama_kelas, tingkat, jurusan, tahun_ajaran, wali_kelas_id) VALUES
(1,'X TKJ 1','X','TKJ','2025/2026',5),
(2,'XI TKJ 1','XI','TKJ','2025/2026',NULL),
(3,'XII TKJ 1','XII','TKJ','2025/2026',NULL);

-- ---------- Siswa (10) ----------
INSERT INTO siswa (id, nis, nisn, nama, jenis_kelamin, kelas_id, alamat, nama_ortu, no_hp_ortu, status, total_poin_pelanggaran, total_poin_penghargaan) VALUES
(1,'1001','001001','Andini Pratiwi','P',1,'Jl. Mawar No.1, Krangkeng','Bpk. Andi','081200000001','aktif',10,10),
(2,'1002','001002','Budi Santoso','L',1,'Jl. Melati No.2, Krangkeng','Bpk. Santo','081200000002','aktif',35,40),
(3,'1003','001003','Citra Ayu','P',2,'Jl. Kenanga No.3, Krangkeng','Ibu Citra','081200000003','aktif',55,60),
(4,'1004','001004','Dedi Kurniawan','L',2,'Jl. Anggrek No.4, Krangkeng','Bpk. Kurna','081200000004','aktif',80,0),
(5,'1005','001005','Erik Maulana','L',3,'Jl. Dahlia No.5, Krangkeng','Bpk. Maul','081200000005','aktif',20,0),
(6,'1006','001006','Fitri Rahma','P',3,'Jl. Teratai No.6, Krangkeng','Ibu Rahma','081200000006','dikembalikan',76,0),
(7,'1007','001007','Galih Prakoso','L',1,'Jl. Flamboyan No.7, Krangkeng','Bpk. Prako','081200000007','aktif',30,0),
(8,'1008','001008','Hana Salsabila','P',2,'Jl. Cempaka No.8, Krangkeng','Ibu Salsa','081200000008','aktif',0,165),
(9,'1009','001009','Ilham Ramadhan','L',3,'Jl. Kamboja No.9, Krangkeng','Bpk. Rama','081200000009','aktif',20,105),
(10,'1010','001010','Sinta Dewi','P',1,'Jl. Sawo No.10, Krangkeng','Ibu Dewi','081200000010','aktif',60,130);

-- ---------- Laporan Pelanggaran ----------
-- jenis: 1=R01(5) 2=R02(5) 3=S01(15) 4=S02(20) 5=B01(40) 6=H01(76)
-- user: 1=admin 4=osis 5=wali
INSERT INTO laporan_pelanggaran (siswa_id, jenis_id, pelapor_id, tanggal, tahun_ajaran, kronologi, foto_bukti_path, bobot_poin_saat_lapor, is_force_majeure, status_validasi, divalidasi_oleh) VALUES
-- Januari
(2,3,4,'2026-01-08','2025/2026','Bolhos tanpa keterangan pada jam pelajaran pertama.',NULL,15,0,'divalidasi',1),
(7,1,5,'2026-01-12','2025/2026','Terlambat masuk gerbang sekolah 20 menit.',NULL,5,0,'divalidasi',1),
(10,5,4,'2026-01-20','2025/2026','Terlibat perkelahian dengan siswa lain di kantin.',NULL,40,0,'divalidasi',1),
-- Februari
(1,1,4,'2026-02-03','2025/2026','Terlambat mengikuti upacara bendera.',NULL,5,0,'divalidasi',1),
(4,5,5,'2026-02-10','2025/2026','Perkelahian di lapangan saat istirahat.',NULL,40,0,'divalidasi',1),
(7,1,4,'2026-02-17','2025/2026','Terlambat masuk kelas setelah istirahat.',NULL,5,0,'divalidasi',1),
(10,4,4,'2026-02-24','2025/2026','Merokok di area parkir sekolah.',NULL,20,0,'divalidasi',1),
-- Maret
(1,2,5,'2026-03-05','2025/2026','Tidak memakai dasi dan topi saat upacara.',NULL,5,0,'divalidasi',1),
(5,4,4,'2026-03-11','2025/2026','Merokok di belakang laboratorium.',NULL,20,0,'divalidasi',1),
(7,2,4,'2026-03-19','2025/2026','Seragam tidak lengkap (tanpa badge).',NULL,5,0,'divalidasi',1),
-- April
(2,4,5,'2026-04-07','2025/2026','Merokok di toilet sekolah.',NULL,20,0,'divalidasi',1),
-- Mei
(3,5,4,'2026-05-06','2025/2026','Bullying verbal terhadap adik kelas.',NULL,40,0,'divalidasi',1),
-- Juni
(3,3,5,'2026-06-09','2025/2026','Bolhos saat jam praktik produktif.',NULL,15,0,'divalidasi',1),
(9,4,4,'2026-06-16','2025/2026','Merokok di lingkungan sekolah.',NULL,20,0,'divalidasi',1),
-- Juli
(4,5,4,'2026-07-14','2025/2026','Perkelahian ulang dengan siswa yang sama.',NULL,40,0,'divalidasi',1),
-- Agustus
(6,6,1,'2026-08-05','2025/2026','Force majeure: kasus hukum/asusila, langsung dikembalikan ke orang tua.',NULL,76,1,'divalidasi',1),
(7,3,5,'2026-08-19','2025/2026','Bolhos tanpa keterangan.',NULL,15,0,'divalidasi',1),
-- September (masih pending, belum dihitung)
(5,2,4,'2026-09-04','2025/2026','Tidak memakai atribut lengkap saat upacara.',NULL,5,0,'pending',NULL),
(9,3,4,'2026-09-07','2025/2026','Bolhos, menunggu konfirmasi wali kelas.',NULL,15,0,'pending',NULL);

-- ---------- Laporan Penghargaan ----------
-- jenis: 1=P01(50) 2=P02(30) 3=P03(10) 4=P04(75)
INSERT INTO laporan_penghargaan (siswa_id, jenis_id, pelapor_id, tanggal, tahun_ajaran, keterangan, foto_bukti_path, bobot_poin_saat_lapor, status_validasi, divalidasi_oleh) VALUES
(10,1,5,'2026-01-15','2025/2026','Juara 1 lomba web design tingkat kabupaten.',NULL,50,'divalidasi',1),
(8,2,5,'2026-02-12','2025/2026','Juara 2 lomba pidato tingkat kabupaten.',NULL,30,'divalidasi',1),
(8,1,1,'2026-03-10','2025/2026','Juara 1 olimpiade matematika tingkat kabupaten.',NULL,50,'divalidasi',1),
(10,1,5,'2026-03-22','2025/2026','Juara 1 lomba jaringan komputer tingkat kabupaten.',NULL,50,'divalidasi',1),
(3,1,5,'2026-04-08','2025/2026','Juara 1 lomba desain poster tingkat kabupaten.',NULL,50,'divalidasi',1),
(2,3,5,'2026-05-04','2025/2026','Petugas upacara teladan bulan Mei.',NULL,10,'divalidasi',1),
(3,3,5,'2026-05-20','2025/2026','Petugas upacara teladan.',NULL,10,'divalidasi',1),
(9,4,1,'2026-05-27','2025/2026','Juara 1 lomba robotik tingkat provinsi.',NULL,75,'divalidasi',1),
(1,3,5,'2026-06-03','2025/2026','Petugas upacara teladan.',NULL,10,'divalidasi',1),
(8,4,1,'2026-06-24','2025/2026','Juara 1 olimpiade sains tingkat provinsi.',NULL,75,'divalidasi',1),
(2,2,5,'2026-07-08','2025/2026','Juara 3 lomba cerdas cermat tingkat kabupaten.',NULL,30,'divalidasi',1),
(10,2,5,'2026-07-21','2025/2026','Juara 2 lomba karya tulis tingkat kabupaten.',NULL,30,'divalidasi',1),
(8,3,5,'2026-08-11','2025/2026','Duta literasi sekolah.',NULL,10,'divalidasi',1),
(9,2,5,'2026-08-26','2025/2026','Juara 2 lomba fotografi tingkat kabupaten.',NULL,30,'divalidasi',1),
(1,3,5,'2026-09-02','2025/2026','Petugas upacara (menunggu validasi).',NULL,10,'pending',NULL),
(4,3,4,'2026-09-05','2025/2026','Usulan OSIS: relawan kebersihan (menunggu validasi).',NULL,10,'pending',NULL);

-- ---------- Tindakan Disiplin (hasil validasi) ----------
INSERT INTO tindakan_disiplin (siswa_id, jenis, total_poin_saat_itu, tanggal_cetak, dicetak_oleh, keterangan) VALUES
(2,'SP1',35,'2026-04-08',1,'Pembinaan tahap pertama oleh wali kelas.'),
(7,'SP1',30,'2026-08-20',1,'Pembinaan tahap pertama oleh wali kelas.'),
(3,'SP2',55,'2026-06-10',1,'Pemanggilan orang tua, perjanjian kedua.'),
(10,'SP2',60,'2026-02-25',1,'Pemanggilan orang tua, perjanjian kedua.'),
(4,'SP3',80,'2026-07-15',1,'Skorsing + pembinaan intensif BK.'),
(6,'PENGEMBALIAN',76,'2026-08-06',1,'Force majeure: dikembalikan kepada orang tua.');

-- ---------- Sertifikat Penghargaan ----------
INSERT INTO sertifikat_penghargaan (siswa_id, kategori, total_poin_saat_itu, tahun, tanggal_cetak, dicetak_oleh) VALUES
(9,'BERPRESTASI',105,'2025/2026','2026-08-27',1),
(10,'HADIAH',130,'2025/2026','2026-07-22',1),
(8,'WALUYA_UTAMA',165,'2025/2026','2026-08-12',1);

-- ---------- Log aktivitas contoh ----------
INSERT INTO log_aktivitas (user_id, aksi, detail) VALUES
(1,'login','Admin masuk ke aplikasi.'),
(4,'lapor-pelanggaran','OSIS melaporkan Sinta Dewi (B01).'),
(1,'validasi','Admin memvalidasi laporan #3.'),
(5,'cetak-sp','Wali mencetak SP1 Budi Santoso.'),
(1,'update-tampilan','Admin mengganti warna primer tema.');
