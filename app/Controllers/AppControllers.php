<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Database;
use App\Core\View;
use App\Models\DashboardModel;
use App\Models\LaporanModel;
use App\Models\MasterModel;
use App\Models\SettingModel;
use App\Models\SiswaModel;
use App\Models\ValidasiModel;
use App\Helpers\PoinRule;
use App\Helpers\UploadHelper;

class AuthController
{
    public function loginForm(): void
    {
        Auth::start();
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function login(): void
    {
        Auth::start();
        $st = Database::get()->prepare('SELECT * FROM users WHERE username=? AND is_active=1');
        $st->execute([$_POST['username'] ?? '']);
        $u = $st->fetch();
        if ($u && password_verify($_POST['password'] ?? '', $u['password_hash'])) {
            unset($u['password_hash']);
            Auth::login($u);
            header('Location: /dashboard');
        } else {
            $error = 'Username / password salah';
            require __DIR__ . '/../Views/auth/login.php';
        }
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }
}

class DashboardController
{
    public function index(): void
    {
        $setting = SettingModel::get();
        $tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : (int)date('Y');
        $bulan = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? (int)$_GET['bulan'] : null;
        $tren = DashboardModel::trenBulanan($setting['tahun_ajaran_aktif'], $tahun);
        $pie = DashboardModel::pieJenisPelanggaran($tahun, $bulan);
        $top = DashboardModel::topSiswa($setting['tahun_ajaran_aktif']);
        $stat = DashboardModel::kartuStatistik();
        $sp = DashboardModel::siswaPerluSP();
        $tahunList = DashboardModel::listTahunGrafik();
        View::render('dashboard/index', compact('setting', 'tren', 'pie', 'top', 'stat', 'sp', 'tahun', 'bulan', 'tahunList'));
    }

    /** Endpoint JSON untuk Chart.js fetch (dipakai dashboard) */
    public function chartApi(): void
    {
        header('Content-Type: application/json');
        $setting = SettingModel::get();
        $tahun = isset($_GET['tahun']) && $_GET['tahun'] !== '' ? (int)$_GET['tahun'] : (int)date('Y');
        $bulan = isset($_GET['bulan']) && $_GET['bulan'] !== '' ? (int)$_GET['bulan'] : null;
        echo json_encode([
            'tren' => DashboardModel::trenBulanan($setting['tahun_ajaran_aktif'], $tahun),
            'pie' => DashboardModel::pieJenisPelanggaran($tahun, $bulan),
        ]);
        exit;
    }
}

class LaporanController
{
    // OSIS + Wali + Admin boleh lapor; validasi hanya admin/wali_kelas
    public function formPelanggaran(): void
    {
        $db = Database::get();
        $setting = SettingModel::get();
        $siswa = $db->query('SELECT id, nis, nama FROM siswa WHERE status="aktif" ORDER BY nama')->fetchAll();
        $jenis = $db->query('SELECT * FROM jenis_pelanggaran WHERE is_active=1')->fetchAll();
        View::render('laporan/form-pelanggaran', compact('setting', 'siswa', 'jenis'));
    }

    public function simpanPelanggaran(): void
    {
        Auth::requireLogin();
        $cfg = require __DIR__ . '/../../config/config.php';
        $path = null;
        if (!empty($_FILES['foto_bukti']['name'])) {
            $fname = UploadHelper::handle($_FILES['foto_bukti'], $cfg['upload']['bukti_dir'], $cfg['upload']['allowed'], $cfg['upload']['max_size']);
            $path = 'uploads/bukti/' . $fname; // path relatif dicatat di DB
        }
        $setting = SettingModel::get();
        LaporanModel::tambahPelanggaran([
            'siswa_id' => (int)$_POST['siswa_id'],
            'jenis_id' => (int)$_POST['jenis_id'],
            'pelapor_id' => Auth::user()['id'],
            'tanggal' => $_POST['tanggal'],
            'tahun_ajaran' => $setting['tahun_ajaran_aktif'],
            'kronologi' => $_POST['kronologi'] ?? null,
            'foto_bukti_path' => $path,
            'is_force_majeure' => isset($_POST['is_force_majeure']) ? 1 : 0,
        ]);
        header('Location: /dashboard?ok=laporan-terkirim');
        exit;
    }

    public function validasi(): void
    {
        // hanya admin & wali_kelas
        Auth::requireRole(['admin', 'wali_kelas']);
        $hasil = LaporanModel::validasi((int)$_POST['id'], Auth::user()['id'], $_POST['status']);
        header('Location: /dashboard?ok=validasi&tindakan=' . ($hasil['tindakan'] ?? '-'));
        exit;
    }
}

class SettingController
{
    public function index(): void
    {
        View::render('setting/index', ['setting' => SettingModel::get()]);
    }

    public function update(): void
    {
        $cfg = require __DIR__ . '/../../config/config.php';
        $logoPath = null;
        if (!empty($_FILES['logo']['name'])) {
            $fname = UploadHelper::handle($_FILES['logo'], $cfg['upload']['logo_dir'], $cfg['upload']['allowed'], $cfg['upload']['max_size']);
            $logoPath = 'uploads/logo/' . $fname;
        }
        SettingModel::update([
            'id' => (int)$_POST['id'],
            'nama_sekolah' => $_POST['nama_sekolah'],
            'alamat' => $_POST['alamat'] ?? null,
            'telepon' => $_POST['telepon'] ?? null,
            'email' => $_POST['email'] ?? null,
            'tahun_ajaran_aktif' => $_POST['tahun_ajaran_aktif'],
            'semester_aktif' => $_POST['semester_aktif'],
            'logo_path' => $logoPath,
            'kepala_sekolah_nama' => $_POST['kepala_sekolah_nama'] ?? null,
            'kepala_sekolah_nip' => $_POST['kepala_sekolah_nip'] ?? null,
        ]);
        header('Location: /setting?ok=saved');
        exit;
    }
}

class CetakController
{
    public function sp(int $siswaId = 0): void
    {
        $siswaId = $siswaId ?: (int)($_GET['siswa_id'] ?? 0);
        $db = Database::get();
        $st = $db->prepare('SELECT s.*, k.nama_kelas FROM siswa s LEFT JOIN kelas k ON k.id=s.kelas_id WHERE s.id=?');
        $st->execute([$siswaId]);
        $siswa = $st->fetch();
        $jenis = PoinRule::tindakanUntuk((int)$siswa['total_poin_pelanggaran']);
        $setting = SettingModel::get();
        View::renderPrint('cetak/sp', compact('siswa', 'jenis', 'setting'));
    }

    public function sertifikat(int $siswaId = 0): void
    {
        $siswaId = $siswaId ?: (int)($_GET['siswa_id'] ?? 0);
        $db = Database::get();
        $st = $db->prepare('SELECT * FROM siswa WHERE id=?');
        $st->execute([$siswaId]);
        $siswa = $st->fetch();
        $kategori = PoinRule::penghargaanUntuk((int)$siswa['total_poin_penghargaan']);
        $setting = SettingModel::get();
        View::renderPrint('cetak/sertifikat', compact('siswa', 'kategori', 'setting'));
    }
}

class MasterController
{
    public function pelanggaran(): void
    {
        View::render('master/pelanggaran', ['setting' => SettingModel::get(), 'rows' => MasterModel::allPelanggaran()]);
    }
    public function simpanPelanggaran(): void
    {
        MasterModel::savePelanggaran([
            'id' => $_POST['id'] ?: null, 'kode' => trim($_POST['kode']),
            'nama' => trim($_POST['nama']), 'kategori' => $_POST['kategori'],
            'bobot_poin' => (int)$_POST['bobot_poin'],
            'is_force_majeure_default' => isset($_POST['is_force_majeure_default']) ? 1 : 0,
            'deskripsi' => $_POST['deskripsi'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);
        header('Location: /master-pelanggaran?ok=saved'); exit;
    }
    public function hapusPelanggaran(): void
    {
        MasterModel::deletePelanggaran((int)($_GET['id'] ?? 0));
        header('Location: /master-pelanggaran?ok=deleted'); exit;
    }
    public function penghargaan(): void
    {
        View::render('master/penghargaan', ['setting' => SettingModel::get(), 'rows' => MasterModel::allPenghargaan()]);
    }
    public function simpanPenghargaan(): void
    {
        MasterModel::savePenghargaan([
            'id' => $_POST['id'] ?: null, 'kode' => trim($_POST['kode']),
            'nama' => trim($_POST['nama']), 'bobot_poin' => (int)$_POST['bobot_poin'],
            'deskripsi' => $_POST['deskripsi'] ?? null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);
        header('Location: /master-penghargaan?ok=saved'); exit;
    }
    public function hapusPenghargaan(): void
    {
        MasterModel::deletePenghargaan((int)($_GET['id'] ?? 0));
        header('Location: /master-penghargaan?ok=deleted'); exit;
    }
    public function kelas(): void
    {
        View::render('master/kelas', [
            'setting' => SettingModel::get(),
            'rows' => \App\Models\KelasModel::all(),
            'wali' => \App\Models\KelasModel::listWali(),
            'tahunAktif' => SettingModel::get()['tahun_ajaran_aktif'] ?? '2025/2026',
        ]);
    }
    public function simpanKelas(): void
    {
        \App\Models\KelasModel::save([
            'id' => $_POST['id'] ?: null,
            'nama_kelas' => trim($_POST['nama_kelas']),
            'tingkat' => $_POST['tingkat'],
            'jurusan' => trim($_POST['jurusan'] ?? ''),
            'tahun_ajaran' => trim($_POST['tahun_ajaran']),
            'wali_kelas_id' => $_POST['wali_kelas_id'] ?: null,
        ]);
        header('Location: /master-kelas?ok=saved'); exit;
    }
    public function hapusKelas(): void
    {
        \App\Models\KelasModel::delete((int)($_GET['id'] ?? 0));
        header('Location: /master-kelas?ok=deleted'); exit;
    }
}

class SiswaController
{
    private function waliScope(): ?int
    {
        $u = Auth::user();
        return ($u['role'] === 'wali_kelas') ? (int)$u['id'] : null;
    }

    public function index(): void
    {
        $q = $_GET['q'] ?? null;
        $rows = SiswaModel::all($this->waliScope(), $q);
        View::render('siswa/index', ['setting' => SettingModel::get(), 'rows' => $rows, 'q' => $q]);
    }

    public function form(): void
    {
        $db = Database::get();
        $kelas = $db->query('SELECT * FROM kelas ORDER BY nama_kelas')->fetchAll();
        // Wali hanya bisa pilih kelasnya sendiri
        if ($this->waliScope()) {
            $kelas = array_values(array_filter($kelas, fn($k) => (int)$k['wali_kelas_id'] === $this->waliScope()));
        }
        $data = ['id' => '', 'nis' => '', 'nama' => '', 'jenis_kelamin' => 'L', 'kelas_id' => '', 'alamat' => '', 'nama_ortu' => '', 'no_hp_ortu' => '', 'status' => 'aktif'];
        if (!empty($_GET['id'])) $data = SiswaModel::find((int)$_GET['id']) ?? $data;
        View::render('siswa/form', ['setting' => SettingModel::get(), 'kelas' => $kelas, 'data' => $data]);
    }

    public function simpan(): void
    {
        SiswaModel::save([
            'id' => $_POST['id'] ?: null, 'nis' => trim($_POST['nis']), 'nisn' => trim($_POST['nisn'] ?? '') ?: null, 'nama' => trim($_POST['nama']),
            'jenis_kelamin' => $_POST['jenis_kelamin'], 'kelas_id' => $_POST['kelas_id'] ?: null,
            'alamat' => $_POST['alamat'] ?? null, 'nama_ortu' => $_POST['nama_ortu'] ?? null,
            'no_hp_ortu' => $_POST['no_hp_ortu'] ?? null, 'status' => $_POST['status'] ?? 'aktif',
        ]);
        header('Location: /siswa?ok=saved'); exit;
    }

    public function hapus(): void
    {
        Auth::requireRole(['admin']); // hanya admin boleh hapus
        SiswaModel::delete((int)($_GET['id'] ?? 0));
        header('Location: /siswa?ok=deleted'); exit;
    }

    public function importForm(): void
    {
        View::render('siswa/import', ['setting' => SettingModel::get(), 'hasil' => null]);
    }

    public function importProses(): void
    {
        $hasil = ['masuk' => 0, 'lewat' => 0, 'errors' => []];
        try {
            $f = $_FILES['file_xlsx'] ?? null;
            if (!$f || ($f['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
                throw new \RuntimeException('Pilih dulu file .xlsx yang akan diupload');
            }
            if ($f['error'] !== UPLOAD_ERR_OK) throw new \RuntimeException('Upload gagal (kode ' . $f['error'] . ')');
            if ($f['size'] > 5 * 1024 * 1024) throw new \RuntimeException('Ukuran file melebihi 5 MB');
            if (strtolower(pathinfo($f['name'], PATHINFO_EXTENSION)) !== 'xlsx') {
                throw new \RuntimeException('File harus berformat .xlsx');
            }
            $rows = \App\Helpers\XlsxReader::read($f['tmp_name']);
            if (count($rows) < 2) throw new \RuntimeException('File kosong (minimal ada baris header + 1 data)');

            // Petakan header (case-insensitive, '*' opsional)
            $norm = fn($s) => strtolower(trim(str_replace('*', '', (string)$s)));
            $map = [];
            foreach ($rows[0] as $ci => $h) {
                switch ($norm($h)) {
                    case 'nis': $map['nis'] = $ci; break;
                    case 'nisn': $map['nisn'] = $ci; break;
                    case 'nama lengkap': case 'nama': $map['nama'] = $ci; break;
                    case 'jk (l/p)': case 'jk': case 'jenis kelamin': $map['jk'] = $ci; break;
                    case 'kelas': $map['kelas'] = $ci; break;
                    case 'alamat': $map['alamat'] = $ci; break;
                    case 'nama orang tua': case 'nama ortu': $map['nama_ortu'] = $ci; break;
                    case 'no hp ortu': case 'no hp orang tua': $map['no_hp_ortu'] = $ci; break;
                }
            }
            foreach (['nis', 'nama', 'jk', 'kelas'] as $wajib) {
                if (!isset($map[$wajib])) throw new \RuntimeException('Header kolom wajib tidak ditemukan: ' . strtoupper($wajib));
            }

            $db = Database::get();
            // Daftar kelas yang boleh (wali hanya kelasnya sendiri)
            $kelasRows = $db->query('SELECT id, nama_kelas, wali_kelas_id FROM kelas')->fetchAll();
            if ($this->waliScope()) {
                $kelasRows = array_values(array_filter($kelasRows, fn($k) => (int)$k['wali_kelas_id'] === $this->waliScope()));
            }
            $kelasByName = [];
            foreach ($kelasRows as $k) $kelasByName[strtolower(trim($k['nama_kelas']))] = (int)$k['id'];
            $nisDb = $db->query('SELECT nis FROM siswa')->fetchAll(\PDO::FETCH_COLUMN);
            $nisDb = array_map(fn($n) => strtolower(trim((string)$n)), $nisDb);
            $nisFile = [];

            $jkMap = ['l' => 'L', 'p' => 'P', 'laki-laki' => 'L', 'perempuan' => 'P', 'lakilaki' => 'L'];

            for ($i = 1; $i < count($rows); $i++) {
                $no = $i + 1; // nomor baris di Excel
                $cell = fn($k) => trim((string)($rows[$i][$map[$k] ?? -1] ?? ''));
                // Lewati baris kosong total
                if ($cell('nis') === '' && $cell('nama') === '') { $hasil['lewat']++; continue; }
                $nis = $cell('nis');
                $nama = $cell('nama');
                $jkRaw = strtolower($cell('jk'));
                $kelasRaw = strtolower($cell('kelas'));
                if ($nis === '' || $nama === '') {
                    $hasil['errors'][] = "Baris $no: NIS dan Nama wajib diisi";
                    continue;
                }
                if (in_array(strtolower($nis), $nisDb, true) || in_array(strtolower($nis), $nisFile, true)) {
                    $hasil['errors'][] = "Baris $no: NIS '$nis' sudah terdaftar";
                    continue;
                }
                if (!isset($jkMap[$jkRaw])) {
                    $hasil['errors'][] = "Baris $no: JK harus L/P (isi: '{$cell('jk')}')";
                    continue;
                }
                if (!isset($kelasByName[$kelasRaw])) {
                    $hasil['errors'][] = "Baris $no: Kelas '{$cell('kelas')}' tidak dikenal";
                    continue;
                }
                SiswaModel::save([
                    'id' => null, 'nis' => $nis, 'nisn' => $cell('nisn') ?: null, 'nama' => $nama,
                    'jenis_kelamin' => $jkMap[$jkRaw], 'kelas_id' => $kelasByName[$kelasRaw],
                    'alamat' => $cell('alamat') ?: null, 'nama_ortu' => $cell('nama_ortu') ?: null,
                    'no_hp_ortu' => $cell('no_hp_ortu') ?: null, 'status' => 'aktif',
                ]);
                $nisFile[] = strtolower($nis);
                $hasil['masuk']++;
            }
        } catch (\RuntimeException $e) {
            $hasil['errors'][] = $e->getMessage();
        }
        View::render('siswa/import', ['setting' => SettingModel::get(), 'hasil' => $hasil]);
    }

    public function detail(): void
    {
        $s = SiswaModel::find((int)($_GET['id'] ?? 0));
        if (!$s) { http_response_code(404); echo 'Siswa tidak ditemukan'; return; }
        $riwayat = SiswaModel::riwayat($s['id']);
        $tindakan = PoinRule::tindakanUntuk((int)$s['total_poin_pelanggaran']);
        $kat = PoinRule::penghargaanUntuk((int)$s['total_poin_penghargaan']);
        View::render('siswa/detail', ['setting' => SettingModel::get(), 's' => $s, 'riwayat' => $riwayat, 'tindakan' => $tindakan, 'kat' => $kat]);
    }
}

class ValidasiController
{
    public function index(): void
    {
        $u = Auth::user();
        $wali = ($u['role'] === 'wali_kelas') ? (int)$u['id'] : null;
        View::render('validasi/index', [
            'setting' => SettingModel::get(),
            'pelanggaran' => ValidasiModel::pendingPelanggaran($wali),
            'penghargaan' => ValidasiModel::pendingPenghargaan(),
        ]);
    }

    public function putuskanPelanggaran(): void
    {
        $h = LaporanModel::validasi((int)$_POST['id'], Auth::user()['id'], $_POST['status']);
        header('Location: /validasi?ok=done&tindakan=' . ($h['tindakan'] ?? '-')); exit;
    }

    public function putuskanPenghargaan(): void
    {
        $h = LaporanModel::validasiPenghargaan((int)$_POST['id'], Auth::user()['id'], $_POST['status']);
        header('Location: /validasi?ok=done&kategori=' . ($h['kategori'] ?? '-')); exit;
    }
}

class ReportController
{
    private function filters(): array
    {
        return [
            'tab' => $_GET['tab'] ?? 'pelanggaran',
            'kelas_id' => $_GET['kelas_id'] ?? '',
            'tahun_ajaran' => $_GET['tahun_ajaran'] ?? '',
            'dari' => $_GET['dari'] ?? '',
            'sampai' => $_GET['sampai'] ?? '',
            'status' => $_GET['status'] ?? '',
            'q' => trim($_GET['q'] ?? ''),
        ];
    }

    private function waliKelasIds(): ?array
    {
        $u = Auth::user();
        if (($u['role'] ?? '') === 'wali_kelas') {
            return \App\Models\ReportModel::kelasIdsWali((int)$u['id']);
        }
        return null;
    }

    public function index(): void
    {
        $f = $this->filters();
        $waliIds = $this->waliKelasIds();
        $db = Database::get();
        $kelas = $db->query('SELECT id, nama_kelas FROM kelas ORDER BY nama_kelas')->fetchAll();
        if ($waliIds !== null) {
            $kelas = array_values(array_filter($kelas, fn($k) => in_array((int)$k['id'], $waliIds, true)));
        }
        $data = [
            'setting' => SettingModel::get(),
            'f' => $f,
            'kelas' => $kelas,
            'tahunList' => \App\Models\ReportModel::listTahunAjaran(),
            'pelanggaran' => \App\Models\ReportModel::pelanggaran($f, $waliIds),
            'penghargaan' => \App\Models\ReportModel::penghargaan($f, $waliIds),
            'rekap' => \App\Models\ReportModel::rekapSiswa($f, $waliIds),
        ];
        View::render('laporan/index', $data);
    }

    public function cetak(): void
    {
        $f = $this->filters();
        $waliIds = $this->waliKelasIds();
        View::renderPrint('laporan/cetak', [
            'setting' => SettingModel::get(),
            'f' => $f,
            'pelanggaran' => \App\Models\ReportModel::pelanggaran($f, $waliIds),
            'penghargaan' => \App\Models\ReportModel::penghargaan($f, $waliIds),
            'rekap' => \App\Models\ReportModel::rekapSiswa($f, $waliIds),
        ]);
    }

    public function export(): void
    {
        $f = $this->filters();
        $tab = $f['tab'] === 'penghargaan' ? 'penghargaan' : 'pelanggaran';
        $waliIds = $this->waliKelasIds();
        $rows = $tab === 'penghargaan'
            ? \App\Models\ReportModel::penghargaan($f, $waliIds)
            : \App\Models\ReportModel::pelanggaran($f, $waliIds);
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="laporan-' . $tab . '-' . date('Ymd') . '.csv"');
        $out = fopen('php://output', 'w');
        fputs($out, "\xEF\xBB\xBF"); // BOM agar Excel baca UTF-8
        if ($tab === 'penghargaan') {
            fputcsv($out, ['Tanggal', 'NIS', 'Nama', 'Kelas', 'Jenis', 'Poin', 'Status', 'Pelapor', 'Tahun Ajaran']);
            foreach ($rows as $r) fputcsv($out, [$r['tanggal'], $r['nis'], $r['siswa_nama'], $r['nama_kelas'] ?? '-', $r['jenis_nama'], $r['bobot_poin_saat_lapor'], $r['status_validasi'], $r['pelapor_nama'] ?? '-', $r['tahun_ajaran']]);
        } else {
            fputcsv($out, ['Tanggal', 'NIS', 'Nama', 'Kelas', 'Jenis', 'Kategori', 'Poin', 'Force Majeure', 'Status', 'Pelapor', 'Tahun Ajaran']);
            foreach ($rows as $r) fputcsv($out, [$r['tanggal'], $r['nis'], $r['siswa_nama'], $r['nama_kelas'] ?? '-', $r['jenis_nama'], $r['kategori'], $r['bobot_poin_saat_lapor'], $r['is_force_majeure'] ? 'YA' : '-', $r['status_validasi'], $r['pelapor_nama'] ?? '-', $r['tahun_ajaran']]);
        }
        fclose($out);
        exit;
    }
}

class UserController
{
    public function index(): void
    {
        View::render('users/index', [
            'setting' => SettingModel::get(),
            'rows' => \App\Models\UserModel::all($_GET['q'] ?? null),
            'q' => $_GET['q'] ?? '',
            'matrix' => \App\Helpers\Permissions::MATRIX,
            'roles' => \App\Helpers\Permissions::ROLES,
        ]);
    }

    public function form(): void
    {
        $data = ['id' => '', 'nama' => '', 'username' => '', 'role' => 'wali_kelas', 'is_active' => 1];
        if (!empty($_GET['id'])) {
            $data = \App\Models\UserModel::find((int)$_GET['id']) ?? $data;
        }
        View::render('users/form', [
            'setting' => SettingModel::get(),
            'data' => $data,
            'error' => $_GET['err'] ?? null,
        ]);
    }

    public function simpan(): void
    {
        try {
            $id = $_POST['id'] ?: null;
            $username = trim($_POST['username'] ?? '');
            if ($username === '') throw new \RuntimeException('Username wajib diisi');
            if (\App\Models\UserModel::usernameExists($username, $id ? (int)$id : null)) {
                throw new \RuntimeException('Username sudah dipakai');
            }
            if (!$id && empty($_POST['password'])) throw new \RuntimeException('Password wajib diisi untuk user baru');
            \App\Models\UserModel::save([
                'id' => $id,
                'nama' => trim($_POST['nama'] ?? ''),
                'username' => $username,
                'password' => $_POST['password'] ?? '',
                'role' => $_POST['role'] ?? 'osis',
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
            ]);
            header('Location: /users?ok=saved');
        } catch (\RuntimeException $e) {
            header('Location: /users-form' . (!empty($_POST['id']) ? '?id=' . (int)$_POST['id'] : '') . '&err=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function hapus(): void
    {
        try {
            \App\Models\UserModel::delete((int)($_GET['id'] ?? 0), (int)Auth::user()['id']);
            header('Location: /users?ok=deleted');
        } catch (\RuntimeException $e) {
            header('Location: /users?err=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function toggle(): void
    {
        try {
            \App\Models\UserModel::toggle((int)($_GET['id'] ?? 0), (int)Auth::user()['id']);
            header('Location: /users?ok=saved');
        } catch (\RuntimeException $e) {
            header('Location: /users?err=' . urlencode($e->getMessage()));
        }
        exit;
    }
}

class TampilanController
{
    public function index(): void
    {
        View::render('setting/tampilan', [
            'setting' => SettingModel::get(),
            'tampilan' => \App\Models\TampilanModel::get(),
            'fonts' => \App\Models\TampilanModel::FONTS,
        ]);
    }

    public function update(): void
    {
        try {
            \App\Models\TampilanModel::update([
                'warna_primer' => trim($_POST['warna_primer'] ?? '#0284c7'),
                'font_family' => $_POST['font_family'] ?? 'sistem',
                'mode_default' => $_POST['mode_default'] ?? 'sistem',
                'custom_css' => trim($_POST['custom_css'] ?? ''),
            ]);
            header('Location: /tampilan?ok=saved');
        } catch (\RuntimeException $e) {
            header('Location: /tampilan?err=' . urlencode($e->getMessage()));
        }
        exit;
    }

    public function reset(): void
    {
        \App\Models\TampilanModel::update([
            'warna_primer' => '#0284c7', 'font_family' => 'sistem',
            'mode_default' => 'sistem', 'custom_css' => '',
        ]);
        header('Location: /tampilan?ok=reset');
        exit;
    }
}
