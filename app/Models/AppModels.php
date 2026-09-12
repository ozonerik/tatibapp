<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class SettingModel
{
    public static function get(): array
    {
        $row = Database::get()->query('SELECT * FROM pengaturan_sekolah LIMIT 1')->fetch();
        return $row ?: ['nama_sekolah' => 'SMK Negeri 1 Krangkeng', 'logo_path' => null, 'tahun_ajaran_aktif' => '2025/2026'];
    }

    public static function update(array $d): void
    {
        $db = Database::get();
        $st = $db->prepare('UPDATE pengaturan_sekolah SET nama_sekolah=?, alamat=?, telepon=?, email=?, tahun_ajaran_aktif=?, semester_aktif=?, logo_path=COALESCE(?,logo_path), kepala_sekolah_nama=?, kepala_sekolah_nip=? WHERE id=?');
        $st->execute([$d['nama_sekolah'], $d['alamat'], $d['telepon'], $d['email'], $d['tahun_ajaran_aktif'], $d['semester_aktif'], $d['logo_path'] ?? null, $d['kepala_sekolah_nama'], $d['kepala_sekolah_nip'], $d['id']]);
    }
}

class DashboardModel
{
    public static function trenBulanan(string $tahunAjaran, ?int $tahun = null): array
    {
        // Tren 12 bulan pada tahun kalender terpilih untuk Chart.js line chart
        $db = Database::get();
        $tahun = $tahun ?: (int)date('Y');
        $labels = []; $langgar = []; $harga = [];
        for ($m = 1; $m <= 12; $m++) {
            $labels[] = date('M', mktime(0, 0, 0, $m, 1));
            $st = $db->prepare("SELECT COUNT(*) FROM laporan_pelanggaran WHERE MONTH(tanggal)=? AND YEAR(tanggal)=? AND status_validasi='divalidasi'");
            $st->execute([$m, $tahun]); $langgar[] = (int)$st->fetchColumn();
            $st = $db->prepare("SELECT COUNT(*) FROM laporan_penghargaan WHERE MONTH(tanggal)=? AND YEAR(tanggal)=? AND status_validasi='divalidasi'");
            $st->execute([$m, $tahun]); $harga[] = (int)$st->fetchColumn();
        }
        return compact('labels', 'langgar', 'harga');
    }

    public static function pieJenisPelanggaran(?int $tahun = null, ?int $bulan = null): array
    {
        $db = Database::get();
        $w = ["l.status_validasi='divalidasi'"]; $p = [];
        if ($tahun) { $w[] = 'YEAR(l.tanggal)=?'; $p[] = $tahun; }
        if ($bulan) { $w[] = 'MONTH(l.tanggal)=?'; $p[] = $bulan; }
        $st = $db->prepare('SELECT j.nama, COUNT(l.id) AS total FROM laporan_pelanggaran l JOIN jenis_pelanggaran j ON j.id=l.jenis_id WHERE ' . implode(' AND ', $w) . ' GROUP BY j.nama ORDER BY total DESC LIMIT 6');
        $st->execute($p);
        $rows = $st->fetchAll();
        return [
            'labels' => array_column($rows, 'nama'),
            'data' => array_map('intval', array_column($rows, 'total')),
        ];
    }

    /** Daftar tahun kalender yang ada datanya (untuk dropdown filter grafik) */
    public static function listTahunGrafik(): array
    {
        $db = Database::get();
        $a = $db->query('SELECT DISTINCT YEAR(tanggal) AS t FROM laporan_pelanggaran ORDER BY t DESC')->fetchAll(\PDO::FETCH_COLUMN);
        $b = $db->query('SELECT DISTINCT YEAR(tanggal) AS t FROM laporan_penghargaan ORDER BY t DESC')->fetchAll(\PDO::FETCH_COLUMN);
        $all = array_unique(array_merge($a, $b));
        rsort($all);
        $cur = (int)date('Y');
        if (!in_array($cur, $all, true)) array_unshift($all, $cur);
        return array_map('intval', $all);
    }

    public static function topSiswa(string $tahunAjaran, int $limit = 10): array
    {
        $db = Database::get();
        // Anugerah Waluya Utama: skor penghargaan tertinggi tahunan (walau < 151 tetap difilter)
        $st = $db->prepare("SELECT s.id, s.nis, s.nama, k.nama_kelas, COALESCE(SUM(CASE WHEN h.status_validasi='divalidasi' THEN h.bobot_poin_saat_lapor ELSE 0 END),0) AS poin
            FROM siswa s LEFT JOIN kelas k ON k.id=s.kelas_id
            LEFT JOIN laporan_penghargaan h ON h.siswa_id=s.id AND h.tahun_ajaran=?
            WHERE s.status='aktif' GROUP BY s.id ORDER BY poin DESC LIMIT $limit");
        $st->execute([$tahunAjaran]);
        return $st->fetchAll();
    }

    public static function kartuStatistik(): array
    {
        $db = Database::get();
        return [
            'siswa' => (int)$db->query("SELECT COUNT(*) FROM siswa WHERE status='aktif'")->fetchColumn(),
            'pelanggaran_pending' => (int)$db->query("SELECT COUNT(*) FROM laporan_pelanggaran WHERE status_validasi='pending'")->fetchColumn(),
            'sp3' => (int)$db->query("SELECT COUNT(*) FROM tindakan_disiplin WHERE jenis='SP3'")->fetchColumn(),
            'waluya' => (int)$db->query("SELECT COUNT(*) FROM sertifikat_penghargaan WHERE kategori='WALUYA_UTAMA'")->fetchColumn(),
        ];
    }

    /** Kelompok siswa yang perlu pembinaan SP berdasarkan akumulasi poin tervalidasi */
    public static function siswaPerluSP(): array
    {
        $rows = Database::get()->query(
            "SELECT s.id, s.nis, s.nama, s.total_poin_pelanggaran, s.status, k.nama_kelas
             FROM siswa s LEFT JOIN kelas k ON k.id=s.kelas_id
             WHERE s.total_poin_pelanggaran >= 25
             ORDER BY s.total_poin_pelanggaran DESC LIMIT 100"
        )->fetchAll();
        $grup = ['SP1' => [], 'SP2' => [], 'SP3' => []];
        foreach ($rows as $r) {
            $t = \App\Helpers\PoinRule::tindakanUntuk((int)$r['total_poin_pelanggaran']);
            if (isset($grup[$t])) $grup[$t][] = $r;
        }
        return $grup;
    }
}

class LaporanModel
{
    public static function tambahPelanggaran(array $d): int
    {
        $db = Database::get();
        // Snapshot bobot master
        $st = $db->prepare('SELECT bobot_poin FROM jenis_pelanggaran WHERE id=?');
        $st->execute([$d['jenis_id']]);
        $bobot = (int)$st->fetchColumn();
        $st = $db->prepare('INSERT INTO laporan_pelanggaran (siswa_id, jenis_id, pelapor_id, tanggal, tahun_ajaran, kronologi, foto_bukti_path, bobot_poin_saat_lapor, is_force_majeure) VALUES (?,?,?,?,?,?,?,?,?)');
        $st->execute([$d['siswa_id'], $d['jenis_id'], $d['pelapor_id'], $d['tanggal'], $d['tahun_ajaran'], $d['kronologi'], $d['foto_bukti_path'], $bobot, $d['is_force_majeure']]);
        return (int)$db->lastInsertId();
    }

    public static function validasi(int $id, int $validatorId, string $status): array
    {
        $db = Database::get();
        $db->beginTransaction();
        $st = $db->prepare('SELECT * FROM laporan_pelanggaran WHERE id=? FOR UPDATE');
        $st->execute([$id]);
        $lap = $st->fetch();
        if (!$lap) throw new \RuntimeException('Laporan tidak ditemukan');

        $db->prepare('UPDATE laporan_pelanggaran SET status_validasi=?, divalidasi_oleh=? WHERE id=?')->execute([$status, $validatorId, $id]);

        $tindakan = null;
        if ($status === 'divalidasi') {
            // Hitung ulang total tervalidasi
            $st = $db->prepare("SELECT COALESCE(SUM(bobot_poin_saat_lapor),0) FROM laporan_pelanggaran WHERE siswa_id=? AND status_validasi='divalidasi'");
            $st->execute([$lap['siswa_id']]);
            $total = (int)$st->fetchColumn();
            $db->prepare('UPDATE siswa SET total_poin_pelanggaran=? WHERE id=?')->execute([$total, $lap['siswa_id']]);

            $fm = (bool)$lap['is_force_majeure'];
            $tindakan = \App\Helpers\PoinRule::tindakanUntuk($total, $fm);
            if ($tindakan) {
                $db->prepare("INSERT INTO tindakan_disiplin (siswa_id, jenis, total_poin_saat_itu, tanggal_cetak, dicetak_oleh) VALUES (?,?,?,CURDATE(),?)")
                   ->execute([$lap['siswa_id'], $tindakan, $total, $validatorId]);
                if ($tindakan === 'PENGEMBALIAN') {
                    $db->prepare("UPDATE siswa SET status='dikembalikan' WHERE id=?")->execute([$lap['siswa_id']]);
                }
            }
        }
        $db->commit();
        return ['total' => $total ?? 0, 'tindakan' => $tindakan];
    }

    public static function tambahPenghargaan(array $d): int
    {
        $db = Database::get();
        $st = $db->prepare('SELECT bobot_poin FROM jenis_penghargaan WHERE id=?');
        $st->execute([$d['jenis_id']]);
        $bobot = (int)$st->fetchColumn();
        $st = $db->prepare('INSERT INTO laporan_penghargaan (siswa_id, jenis_id, pelapor_id, tanggal, tahun_ajaran, keterangan, foto_bukti_path, bobot_poin_saat_lapor) VALUES (?,?,?,?,?,?,?,?)');
        $st->execute([$d['siswa_id'], $d['jenis_id'], $d['pelapor_id'], $d['tanggal'], $d['tahun_ajaran'], $d['keterangan'], $d['foto_bukti_path'], $bobot]);
        return (int)$db->lastInsertId();
    }

    public static function validasiPenghargaan(int $id, int $validatorId, string $status): array
    {
        $db = Database::get();
        $db->beginTransaction();
        $st = $db->prepare('SELECT * FROM laporan_penghargaan WHERE id=? FOR UPDATE');
        $st->execute([$id]);
        $lap = $st->fetch();
        if (!$lap) throw new \RuntimeException('Laporan tidak ditemukan');
        $db->prepare('UPDATE laporan_penghargaan SET status_validasi=?, divalidasi_oleh=? WHERE id=?')->execute([$status, $validatorId, $id]);
        $total = 0; $kategori = null;
        if ($status === 'divalidasi') {
            $st = $db->prepare("SELECT COALESCE(SUM(bobot_poin_saat_lapor),0) FROM laporan_penghargaan WHERE siswa_id=? AND status_validasi='divalidasi'");
            $st->execute([$lap['siswa_id']]);
            $total = (int)$st->fetchColumn();
            $db->prepare('UPDATE siswa SET total_poin_penghargaan=? WHERE id=?')->execute([$total, $lap['siswa_id']]);
            $kategori = \App\Helpers\PoinRule::penghargaanUntuk($total);
            if ($kategori) {
                $db->prepare("INSERT INTO sertifikat_penghargaan (siswa_id, kategori, total_poin_saat_itu, tahun, tanggal_cetak, dicetak_oleh) VALUES (?,?,?, ?, CURDATE(), ?)")
                   ->execute([$lap['siswa_id'], $kategori, $total, $lap['tahun_ajaran'], $validatorId]);
            }
        }
        $db->commit();
        return ['total' => $total, 'kategori' => $kategori];
    }
}

class MasterModel
{
    // --- Jenis Pelanggaran ---
    public static function allPelanggaran(): array
    {
        return Database::get()->query('SELECT * FROM jenis_pelanggaran ORDER BY bobot_poin')->fetchAll();
    }
    public static function savePelanggaran(array $d): void
    {
        $db = Database::get();
        if (!empty($d['id'])) {
            $db->prepare('UPDATE jenis_pelanggaran SET kode=?, nama=?, kategori=?, bobot_poin=?, is_force_majeure_default=?, deskripsi=?, is_active=? WHERE id=?')
               ->execute([$d['kode'], $d['nama'], $d['kategori'], $d['bobot_poin'], $d['is_force_majeure_default'], $d['deskripsi'], $d['is_active'], $d['id']]);
        } else {
            $db->prepare('INSERT INTO jenis_pelanggaran (kode, nama, kategori, bobot_poin, is_force_majeure_default, deskripsi) VALUES (?,?,?,?,?,?)')
               ->execute([$d['kode'], $d['nama'], $d['kategori'], $d['bobot_poin'], $d['is_force_majeure_default'], $d['deskripsi']]);
        }
    }
    public static function deletePelanggaran(int $id): void
    {
        Database::get()->prepare('DELETE FROM jenis_pelanggaran WHERE id=?')->execute([$id]);
    }
    // --- Jenis Penghargaan ---
    public static function allPenghargaan(): array
    {
        return Database::get()->query('SELECT * FROM jenis_penghargaan ORDER BY bobot_poin')->fetchAll();
    }
    public static function savePenghargaan(array $d): void
    {
        $db = Database::get();
        if (!empty($d['id'])) {
            $db->prepare('UPDATE jenis_penghargaan SET kode=?, nama=?, bobot_poin=?, deskripsi=?, is_active=? WHERE id=?')
               ->execute([$d['kode'], $d['nama'], $d['bobot_poin'], $d['deskripsi'], $d['is_active'], $d['id']]);
        } else {
            $db->prepare('INSERT INTO jenis_penghargaan (kode, nama, bobot_poin, deskripsi) VALUES (?,?,?,?)')
               ->execute([$d['kode'], $d['nama'], $d['bobot_poin'], $d['deskripsi']]);
        }
    }
    public static function deletePenghargaan(int $id): void
    {
        Database::get()->prepare('DELETE FROM jenis_penghargaan WHERE id=?')->execute([$id]);
    }
}

class SiswaModel
{
    /** Wali kelas hanya melihat kelasnya sendiri */
    public static function kelasWali(int $userId): array
    {
        $st = Database::get()->prepare('SELECT * FROM kelas WHERE wali_kelas_id=?');
        $st->execute([$userId]);
        return $st->fetchAll();
    }

    public static function all(?int $waliUserId = null, ?string $q = null): array
    {
        $db = Database::get();
        $where = '1=1'; $params = [];
        if ($waliUserId) {
            $where .= ' AND k.wali_kelas_id=?';
            $params[] = $waliUserId;
        }
        if ($q) {
            $where .= ' AND (s.nama LIKE ? OR s.nis LIKE ?)';
            $params[] = "%$q%"; $params[] = "%$q%";
        }
        $st = $db->prepare("SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON k.id=s.kelas_id WHERE $where ORDER BY s.nama LIMIT 200");
        $st->execute($params);
        return $st->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $st = Database::get()->prepare('SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON k.id=s.kelas_id WHERE s.id=?');
        $st->execute([$id]);
        return $st->fetch() ?: null;
    }

    public static function save(array $d): void
    {
        $db = Database::get();
        if (!empty($d['id'])) {
            $db->prepare('UPDATE siswa SET nis=?, nisn=?, nama=?, jenis_kelamin=?, kelas_id=?, alamat=?, nama_ortu=?, no_hp_ortu=?, status=? WHERE id=?')
               ->execute([$d['nis'], $d['nisn'] ?? null, $d['nama'], $d['jenis_kelamin'], $d['kelas_id'] ?: null, $d['alamat'], $d['nama_ortu'], $d['no_hp_ortu'], $d['status'], $d['id']]);
        } else {
            $db->prepare('INSERT INTO siswa (nis, nisn, nama, jenis_kelamin, kelas_id, alamat, nama_ortu, no_hp_ortu) VALUES (?,?,?,?,?,?,?,?)')
               ->execute([$d['nis'], $d['nisn'] ?? null, $d['nama'], $d['jenis_kelamin'], $d['kelas_id'] ?: null, $d['alamat'], $d['nama_ortu'], $d['no_hp_ortu']]);
        }
    }

    public static function delete(int $id): void
    {
        Database::get()->prepare('DELETE FROM siswa WHERE id=?')->execute([$id]);
    }

    public static function riwayat(int $siswaId): array
    {
        $db = Database::get();
        $st = $db->prepare("SELECT l.*, j.nama AS jenis_nama, u.nama AS pelapor FROM laporan_pelanggaran l JOIN jenis_pelanggaran j ON j.id=l.jenis_id LEFT JOIN users u ON u.id=l.pelapor_id WHERE l.siswa_id=? ORDER BY l.tanggal DESC");
        $st->execute([$siswaId]);
        $langgar = $st->fetchAll();
        $st = $db->prepare("SELECT l.*, j.nama AS jenis_nama FROM laporan_penghargaan l JOIN jenis_penghargaan j ON j.id=l.jenis_id WHERE l.siswa_id=? ORDER BY l.tanggal DESC");
        $st->execute([$siswaId]);
        $harga = $st->fetchAll();
        $st = $db->prepare('SELECT * FROM tindakan_disiplin WHERE siswa_id=? ORDER BY id DESC');
        $st->execute([$siswaId]);
        $tindak = $st->fetchAll();
        return compact('langgar', 'harga', 'tindak');
    }
}

class ValidasiModel
{
    public static function pendingPelanggaran(?int $waliUserId = null): array
    {
        $db = Database::get();
        $where = "l.status_validasi='pending'"; $params = [];
        if ($waliUserId) { $where .= ' AND k.wali_kelas_id=?'; $params[] = $waliUserId; }
        $st = $db->prepare("SELECT l.*, s.nis, s.nama AS siswa_nama, k.nama_kelas, j.nama AS jenis_nama, j.bobot_poin, u.nama AS pelapor_nama
            FROM laporan_pelanggaran l JOIN siswa s ON s.id=l.siswa_id LEFT JOIN kelas k ON k.id=s.kelas_id
            JOIN jenis_pelanggaran j ON j.id=l.jenis_id LEFT JOIN users u ON u.id=l.pelapor_id
            WHERE $where ORDER BY l.created_at DESC LIMIT 200");
        $st->execute($params);
        return $st->fetchAll();
    }

    public static function pendingPenghargaan(): array
    {
        return Database::get()->query("SELECT l.*, s.nis, s.nama AS siswa_nama, j.nama AS jenis_nama, u.nama AS pelapor_nama
            FROM laporan_penghargaan l JOIN siswa s ON s.id=l.siswa_id JOIN jenis_penghargaan j ON j.id=l.jenis_id LEFT JOIN users u ON u.id=l.pelapor_id
            WHERE l.status_validasi='pending' ORDER BY l.created_at DESC LIMIT 200")->fetchAll();
    }
}

class KelasModel
{
    public static function all(): array
    {
        return Database::get()->query(
            'SELECT k.*, u.nama AS wali_nama, (SELECT COUNT(*) FROM siswa s WHERE s.kelas_id=k.id) AS jml_siswa
             FROM kelas k LEFT JOIN users u ON u.id=k.wali_kelas_id ORDER BY k.tingkat, k.nama_kelas'
        )->fetchAll();
    }

    public static function listWali(): array
    {
        return Database::get()->query("SELECT id, nama, username FROM users WHERE role='wali_kelas' AND is_active=1 ORDER BY nama")->fetchAll();
    }

    public static function save(array $d): void
    {
        $db = Database::get();
        if (!empty($d['id'])) {
            $db->prepare('UPDATE kelas SET nama_kelas=?, tingkat=?, jurusan=?, tahun_ajaran=?, wali_kelas_id=? WHERE id=?')
               ->execute([$d['nama_kelas'], $d['tingkat'], $d['jurusan'] ?: null, $d['tahun_ajaran'], $d['wali_kelas_id'] ?: null, $d['id']]);
        } else {
            $db->prepare('INSERT INTO kelas (nama_kelas, tingkat, jurusan, tahun_ajaran, wali_kelas_id) VALUES (?,?,?,?,?)')
               ->execute([$d['nama_kelas'], $d['tingkat'], $d['jurusan'] ?: null, $d['tahun_ajaran'], $d['wali_kelas_id'] ?: null]);
        }
    }

    public static function delete(int $id): void
    {
        // Siswa di kelas yang dihapus jadi tanpa kelas (FK SET NULL), kelas aman dihapus
        Database::get()->prepare('DELETE FROM kelas WHERE id=?')->execute([$id]);
    }
}

class ReportModel
{
    /** Ambil daftar tahun ajaran unik untuk filter */
    public static function listTahunAjaran(): array
    {
        $db = Database::get();
        $a = $db->query('SELECT DISTINCT tahun_ajaran FROM laporan_pelanggaran ORDER BY tahun_ajaran DESC')->fetchAll(\PDO::FETCH_COLUMN);
        $b = $db->query('SELECT DISTINCT tahun_ajaran FROM laporan_penghargaan ORDER BY tahun_ajaran DESC')->fetchAll(\PDO::FETCH_COLUMN);
        $c = $db->query('SELECT DISTINCT tahun_ajaran FROM kelas ORDER BY tahun_ajaran DESC')->fetchAll(\PDO::FETCH_COLUMN);
        $all = array_unique(array_merge($a, $b, $c));
        rsort($all);
        return $all ?: [SettingModel::get()['tahun_ajaran_aktif'] ?? '2025/2026'];
    }

    /** IDs kelas milik wali (untuk scope). null = tanpa batas (admin/kepsek). */
    public static function kelasIdsWali(?int $waliUserId): ?array
    {
        if (!$waliUserId) return null;
        $st = Database::get()->prepare('SELECT id FROM kelas WHERE wali_kelas_id=?');
        $st->execute([$waliUserId]);
        return $st->fetchAll(\PDO::FETCH_COLUMN) ?: [-1]; // -1 agar tidak bocor bila wali belum punya kelas
    }

    private static function buildWhere(array $f, ?array $waliKelasIds, string $aliasL = 'l'): array
    {
        $w = ['1=1']; $p = [];
        if (!empty($f['tahun_ajaran'])) { $w[] = "$aliasL.tahun_ajaran=?"; $p[] = $f['tahun_ajaran']; }
        if (!empty($f['dari'])) { $w[] = "$aliasL.tanggal>=?"; $p[] = $f['dari']; }
        if (!empty($f['sampai'])) { $w[] = "$aliasL.tanggal<=?"; $p[] = $f['sampai']; }
        if (!empty($f['status'])) { $w[] = "$aliasL.status_validasi=?"; $p[] = $f['status']; }
        if (!empty($f['q'])) { $w[] = '(s.nama LIKE ? OR s.nis LIKE ?)'; $p[] = "%{$f['q']}%"; $p[] = "%{$f['q']}%"; }
        if (!empty($f['kelas_id'])) { $w[] = 's.kelas_id=?'; $p[] = (int)$f['kelas_id']; }
        elseif ($waliKelasIds !== null) {
            $in = implode(',', array_map('intval', $waliKelasIds));
            $w[] = "s.kelas_id IN ($in)";
        }
        return [$w, $p];
    }

    public static function pelanggaran(array $f, ?array $waliKelasIds): array
    {
        [$w, $p] = self::buildWhere($f, $waliKelasIds);
        $sql = 'SELECT l.*, s.nis, s.nama AS siswa_nama, k.nama_kelas, j.nama AS jenis_nama, j.kategori,
                       u1.nama AS pelapor_nama, u2.nama AS validator_nama
                FROM laporan_pelanggaran l
                JOIN siswa s ON s.id=l.siswa_id
                LEFT JOIN kelas k ON k.id=s.kelas_id
                JOIN jenis_pelanggaran j ON j.id=l.jenis_id
                LEFT JOIN users u1 ON u1.id=l.pelapor_id
                LEFT JOIN users u2 ON u2.id=l.divalidasi_oleh
                WHERE ' . implode(' AND ', $w) . ' ORDER BY l.tanggal DESC, l.id DESC LIMIT 500';
        $st = Database::get()->prepare($sql);
        $st->execute($p);
        return $st->fetchAll();
    }

    public static function penghargaan(array $f, ?array $waliKelasIds): array
    {
        [$w, $p] = self::buildWhere($f, $waliKelasIds);
        $sql = 'SELECT l.*, s.nis, s.nama AS siswa_nama, k.nama_kelas, j.nama AS jenis_nama, u1.nama AS pelapor_nama
                FROM laporan_penghargaan l
                JOIN siswa s ON s.id=l.siswa_id
                LEFT JOIN kelas k ON k.id=s.kelas_id
                JOIN jenis_penghargaan j ON j.id=l.jenis_id
                LEFT JOIN users u1 ON u1.id=l.pelapor_id
                WHERE ' . implode(' AND ', $w) . ' ORDER BY l.tanggal DESC, l.id DESC LIMIT 500';
        $st = Database::get()->prepare($sql);
        $st->execute($p);
        return $st->fetchAll();
    }

    /** Rekap per siswa: akumulasi poin tervalidasi + jumlah kasus (menghormati filter tanggal/kelas) */
    public static function rekapSiswa(array $f, ?array $waliKelasIds): array
    {
        $wKelas = '';
        $params = [];
        if (!empty($f['kelas_id'])) { $wKelas = 'AND s.kelas_id=?'; $params[] = (int)$f['kelas_id']; }
        elseif ($waliKelasIds !== null) {
            $in = implode(',', array_map('intval', $waliKelasIds));
            $wKelas = "AND s.kelas_id IN ($in)";
        }
        $q = '';
        if (!empty($f['q'])) { $q = 'AND (s.nama LIKE ? OR s.nis LIKE ?)'; $params[] = "%{$f['q']}%"; $params[] = "%{$f['q']}%"; }
        $tglLanggar = '';
        $tglHarga = '';
        $p2 = $params;
        if (!empty($f['dari'])) { $tglLanggar .= ' AND tanggal>=?'; $tglHarga .= ' AND tanggal>=?'; }
        if (!empty($f['sampai'])) { $tglLanggar .= ' AND tanggal<=?'; $tglHarga .= ' AND tanggal<=?'; }
        if (!empty($f['tahun_ajaran'])) { $tglLanggar .= ' AND tahun_ajaran=?'; $tglHarga .= ' AND tahun_ajaran=?'; }
        $extra = [];
        if (!empty($f['dari'])) $extra[] = $f['dari'];
        if (!empty($f['sampai'])) $extra[] = $f['sampai'];
        if (!empty($f['tahun_ajaran'])) $extra[] = $f['tahun_ajaran'];
        $sql = "SELECT s.id, s.nis, s.nama, s.status, k.nama_kelas,
                  (SELECT COUNT(*) FROM laporan_pelanggaran l WHERE l.siswa_id=s.id AND l.status_validasi='divalidasi' $tglLanggar) AS jml_langgar,
                  (SELECT COALESCE(SUM(l.bobot_poin_saat_lapor),0) FROM laporan_pelanggaran l WHERE l.siswa_id=s.id AND l.status_validasi='divalidasi' $tglLanggar) AS poin_langgar,
                  (SELECT COUNT(*) FROM laporan_penghargaan h WHERE h.siswa_id=s.id AND h.status_validasi='divalidasi' $tglHarga) AS jml_harga,
                  (SELECT COALESCE(SUM(h.bobot_poin_saat_lapor),0) FROM laporan_penghargaan h WHERE h.siswa_id=s.id AND h.status_validasi='divalidasi' $tglHarga) AS poin_harga
                FROM siswa s LEFT JOIN kelas k ON k.id=s.kelas_id
                WHERE 1=1 $wKelas $q
                ORDER BY poin_langgar DESC, poin_harga DESC LIMIT 300";
        $st = Database::get()->prepare($sql);
        $st->execute(array_merge($params, $extra, $extra));
        return $st->fetchAll();
    }
}

class UserModel
{
    public static function all(?string $q = null): array
    {
        $db = Database::get();
        if ($q) {
            $st = $db->prepare("SELECT u.*, (SELECT COUNT(*) FROM kelas k WHERE k.wali_kelas_id=u.id) AS jml_kelas FROM users u WHERE u.nama LIKE ? OR u.username LIKE ? OR u.role LIKE ? ORDER BY u.role, u.nama");
            $st->execute(["%$q%", "%$q%", "%$q%"]);
            return $st->fetchAll();
        }
        return $db->query('SELECT u.*, (SELECT COUNT(*) FROM kelas k WHERE k.wali_kelas_id=u.id) AS jml_kelas FROM users u ORDER BY u.role, u.nama')->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $st = Database::get()->prepare('SELECT * FROM users WHERE id=?');
        $st->execute([$id]);
        $row = $st->fetch();
        if ($row) unset($row['password_hash']);
        return $row ?: null;
    }

    public static function usernameExists(string $username, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE username=?';
        $params = [$username];
        if ($exceptId) { $sql .= ' AND id!=?'; $params[] = $exceptId; }
        $st = Database::get()->prepare($sql);
        $st->execute($params);
        return (bool)$st->fetchColumn();
    }

    /** $d: id?, nama, username, password (kosong = tidak diubah saat edit), role, is_active */
    public static function save(array $d): void
    {
        $db = Database::get();
        if (!in_array($d['role'], \App\Helpers\Permissions::ROLES, true)) {
            throw new \RuntimeException('Role tidak valid');
        }
        if (!empty($d['id'])) {
            if (!empty($d['password'])) {
                $db->prepare('UPDATE users SET nama=?, username=?, password_hash=?, role=?, is_active=? WHERE id=?')
                   ->execute([$d['nama'], $d['username'], password_hash($d['password'], PASSWORD_BCRYPT), $d['role'], $d['is_active'], $d['id']]);
            } else {
                $db->prepare('UPDATE users SET nama=?, username=?, role=?, is_active=? WHERE id=?')
                   ->execute([$d['nama'], $d['username'], $d['role'], $d['is_active'], $d['id']]);
            }
        } else {
            if (empty($d['password'])) throw new \RuntimeException('Password wajib diisi untuk user baru');
            $db->prepare('INSERT INTO users (nama, username, password_hash, role, is_active) VALUES (?,?,?,?,?)')
               ->execute([$d['nama'], $d['username'], password_hash($d['password'], PASSWORD_BCRYPT), $d['role'], $d['is_active']]);
        }
    }

    public static function delete(int $id, int $currentUserId): void
    {
        if ($id === $currentUserId) throw new \RuntimeException('Tidak bisa menghapus akun sendiri');
        Database::get()->prepare('DELETE FROM users WHERE id=?')->execute([$id]);
    }

    public static function toggle(int $id, int $currentUserId): void
    {
        if ($id === $currentUserId) throw new \RuntimeException('Tidak bisa menonaktifkan akun sendiri');
        Database::get()->prepare('UPDATE users SET is_active = 1 - is_active WHERE id=?')->execute([$id]);
    }
}

class TampilanModel
{
    public const FONTS = [
        'sistem' => 'Sistem (bawaan)',
        'inter' => 'Inter',
        'jakarta' => 'Plus Jakarta Sans',
        'poppins' => 'Poppins',
    ];

    public static function get(): array
    {
        $row = Database::get()->query('SELECT * FROM pengaturan_tampilan WHERE id=1')->fetch();
        return $row ?: ['warna_primer' => '#0284c7', 'font_family' => 'sistem', 'mode_default' => 'sistem', 'custom_css' => null];
    }

    public static function update(array $d): void
    {
        if (!preg_match('/^#[0-9a-fA-F]{6}$/', $d['warna_primer'])) {
            throw new \RuntimeException('Format warna tidak valid');
        }
        if (!isset(self::FONTS[$d['font_family']])) $d['font_family'] = 'sistem';
        if (!in_array($d['mode_default'], ['sistem', 'terang', 'gelap'], true)) $d['mode_default'] = 'sistem';
        Database::get()->prepare('UPDATE pengaturan_tampilan SET warna_primer=?, font_family=?, mode_default=?, custom_css=? WHERE id=1')
            ->execute([$d['warna_primer'], $d['font_family'], $d['mode_default'], $d['custom_css'] ?: null]);
    }

    /** CSS font-family untuk key font terpilih ('' = bawaan Tailwind) */
    public static function fontCss(string $key): string
    {
        return match ($key) {
            'inter' => "'Inter', ui-sans-serif, system-ui, sans-serif",
            'jakarta' => "'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif",
            'poppins' => "'Poppins', ui-sans-serif, system-ui, sans-serif",
            default => '',
        };
    }

    /** URL Google Fonts untuk key font ('' jika tidak perlu) */
    public static function fontUrl(string $key): string
    {
        return match ($key) {
            'inter' => 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap',
            'jakarta' => 'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap',
            'poppins' => 'https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap',
            default => '',
        };
    }
}
