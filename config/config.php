<?php
// Konfigurasi utama aplikasi
define('BASE_URL', 'http://tatibapp.test'); // sesuaikan host Laragon
define('APP_NAME', 'SITATIB');

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'name' => 'tatibapp',
        'user' => 'root',
        'pass' => '', // default Laragon kosong
        'charset' => 'utf8mb4',
    ],
    'upload' => [
        'bukti_dir' => __DIR__ . '/../public/uploads/bukti',
        'logo_dir'  => __DIR__ . '/../public/uploads/logo',
        'max_size'  => 2 * 1024 * 1024, // 2 MB
        'allowed'   => ['jpg','jpeg','png','webp'],
    ],
];
