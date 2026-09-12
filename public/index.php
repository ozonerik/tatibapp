<?php
// Front Controller — DocumentRoot Laragon arahkan ke folder public/
// Jalankan: buat VirtualHost tatibapp.test -> C:/laragon/www/tatibapp/public
require __DIR__ . '/../app/Core/Database.php';
require __DIR__ . '/../app/Core/Auth.php';
require __DIR__ . '/../app/Core/Router.php';
require __DIR__ . '/../app/Core/View.php';
require __DIR__ . '/../app/Helpers/AppHelper.php';
require __DIR__ . '/../app/Helpers/XlsxHelper.php';
require __DIR__ . '/../app/Models/AppModels.php';
require __DIR__ . '/../app/Controllers/AppControllers.php';

use App\Core\Router;
use App\Controllers\{AuthController, DashboardController, LaporanController, MasterController, ReportController, SiswaController, ValidasiController, SettingController, CetakController};

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $rel = str_replace('\\', '/', substr($class, strlen($prefix)));
        $file = __DIR__ . '/../app/' . $rel . '.php';
        // File digabung (AppModels/AppControllers) sehingga autoload hanya fallback
        if (is_file($file)) require $file;
    }
});

$router = new Router();

// Publik
$router->get('/login', [AuthController::class, 'loginForm']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);

// Dashboard — semua role login (kepsek/wakasek read-only di view)
$all = ['admin','kepsek','wakasek','wali_kelas','osis'];
$router->get('/dashboard', [DashboardController::class, 'index'], $all);
$router->get('/api/chart', [DashboardController::class, 'chartApi'], $all);

// Pelaporan — OSIS boleh lapor, validasi khusus admin/wali
$router->get('/lapor-pelanggaran', [LaporanController::class, 'formPelanggaran'], $all);
$router->post('/lapor-pelanggaran', [LaporanController::class, 'simpanPelanggaran'], $all);
$router->post('/validasi', [LaporanController::class, 'validasi'], ['admin','wali_kelas']);

// Pengaturan sekolah — admin saja
$router->get('/setting', [SettingController::class, 'index'], ['admin']);
$router->post('/setting', [SettingController::class, 'update'], ['admin']);

// Cetak — wali_kelas & admin
$router->get('/cetak-sp', [CetakController::class, 'sp'], ['admin','wali_kelas']);
$router->get('/cetak-sertifikat', [CetakController::class, 'sertifikat'], ['admin','wali_kelas']);

// Master Data Dinamis — admin saja
$router->get('/master-pelanggaran', [MasterController::class, 'pelanggaran'], ['admin']);
$router->post('/master-pelanggaran', [MasterController::class, 'simpanPelanggaran'], ['admin']);
$router->get('/master-pelanggaran-hapus', [MasterController::class, 'hapusPelanggaran'], ['admin']);
$router->get('/master-penghargaan', [MasterController::class, 'penghargaan'], ['admin']);
$router->post('/master-penghargaan', [MasterController::class, 'simpanPenghargaan'], ['admin']);
$router->get('/master-penghargaan-hapus', [MasterController::class, 'hapusPenghargaan'], ['admin']);
$router->get('/master-kelas', [MasterController::class, 'kelas'], ['admin']);
$router->post('/master-kelas', [MasterController::class, 'simpanKelas'], ['admin']);
$router->get('/master-kelas-hapus', [MasterController::class, 'hapusKelas'], ['admin']);

// Data Siswa — admin full, wali_kelas scope kelasnya
$router->get('/siswa', [SiswaController::class, 'index'], ['admin','wali_kelas','kepsek','wakasek']);
$router->get('/siswa-form', [SiswaController::class, 'form'], ['admin','wali_kelas']);
$router->post('/siswa-form', [SiswaController::class, 'simpan'], ['admin','wali_kelas']);
$router->get('/siswa-hapus', [SiswaController::class, 'hapus'], ['admin']);
$router->get('/siswa-detail', [SiswaController::class, 'detail'], ['admin','wali_kelas','kepsek','wakasek']);
$router->get('/siswa-import', [SiswaController::class, 'importForm'], ['admin','wali_kelas']);
$router->post('/siswa-import', [SiswaController::class, 'importProses'], ['admin','wali_kelas']);

// Validasi laporan — admin & wali_kelas
$router->get('/validasi', [ValidasiController::class, 'index'], ['admin','wali_kelas']);
$router->post('/validasi-pelanggaran', [ValidasiController::class, 'putuskanPelanggaran'], ['admin','wali_kelas']);
$router->post('/validasi-penghargaan', [ValidasiController::class, 'putuskanPenghargaan'], ['admin','wali_kelas']);

// Laporan daftar pelanggaran & penghargaan — semua role login (wali otomatis scope kelasnya)
$router->get('/laporan', [ReportController::class, 'index'], $all);
$router->get('/laporan-cetak', [ReportController::class, 'cetak'], $all);
$router->get('/laporan-export', [ReportController::class, 'export'], $all);

// Manajemen User, Role & Permission — admin saja
$router->get('/users', [\App\Controllers\UserController::class, 'index'], ['admin']);
$router->get('/users-form', [\App\Controllers\UserController::class, 'form'], ['admin']);
$router->post('/users-form', [\App\Controllers\UserController::class, 'simpan'], ['admin']);
$router->get('/users-hapus', [\App\Controllers\UserController::class, 'hapus'], ['admin']);
$router->get('/users-toggle', [\App\Controllers\UserController::class, 'toggle'], ['admin']);

// Kustomisasi Tampilan — admin saja
$router->get('/tampilan', [\App\Controllers\TampilanController::class, 'index'], ['admin']);
$router->post('/tampilan', [\App\Controllers\TampilanController::class, 'update'], ['admin']);
$router->get('/tampilan-reset', [\App\Controllers\TampilanController::class, 'reset'], ['admin']);

$router->dispatch();
