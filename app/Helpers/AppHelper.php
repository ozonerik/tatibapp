<?php
namespace App\Helpers;

class PoinRule
{
    /**
     * Logika Tindakan Pelanggaran (referensi):
     *  25–50  => SP1
     *  51–75  => SP2
     *  >=76   => SP3
     *  Force majeure (hukum/asusila) => PENGEMBALIAN langsung
     */
    public static function tindakanUntuk(int $totalPoin, bool $forceMajeure = false): ?string
    {
        if ($forceMajeure) return 'PENGEMBALIAN';
        if ($totalPoin >= 76) return 'SP3';
        if ($totalPoin >= 51) return 'SP2';
        if ($totalPoin >= 25) return 'SP1';
        return null;
    }

    /**
     * Logika Penghargaan:
     *  100–125 => BERPRESTASI (sertifikat)
     *  126–150 => HADIAH (sertifikat + hadiah)
     *  >=151   => WALUYA_UTAMA (sertifikat + hadiah + gelar Anugerah Waluya Utama)
     */
    public static function penghargaanUntuk(int $totalPoin): ?string
    {
        if ($totalPoin >= 151) return 'WALUYA_UTAMA';
        if ($totalPoin >= 126) return 'HADIAH';
        if ($totalPoin >= 100) return 'BERPRESTASI';
        return null;
    }
}

class UploadHelper
{
    public static function handle(array $file, string $targetDir, array $allowed, int $maxSize): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
        if ($file['error'] !== UPLOAD_ERR_OK) throw new \RuntimeException('Upload gagal (kode ' . $file['error'] . ')');
        if ($file['size'] > $maxSize) throw new \RuntimeException('Ukuran file melebihi batas 2 MB');

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) throw new \RuntimeException('Format file tidak diizinkan');

        // Validasi mime image
        $info = @getimagesize($file['tmp_name']);
        if ($info === false) throw new \RuntimeException('File bukan gambar valid');

        if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
        $name = uniqid('img-', true) . '.' . $ext;
        $dest = rtrim($targetDir, '/\\') . DIRECTORY_SEPARATOR . $name;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new \RuntimeException('Gagal menyimpan file upload');
        }
        return $name; // panggil yang menyimpan path relatif ke DB
    }
}

/**
 * Matriks Role & Permission terpusat (sumber kebenaran tunggal).
 * Sinkron dengan role ENUM di tabel users & cek Auth::requireRole di public/index.php.
 */
class Permissions
{
    public const ROLES = ['admin', 'kepsek', 'wakasek', 'wali_kelas', 'osis'];

    public const ROLE_LABELS = [
        'admin' => 'Admin',
        'kepsek' => 'Kepala Sekolah',
        'wakasek' => 'Wakil Kepala Sekolah',
        'wali_kelas' => 'Wali Kelas',
        'osis' => 'OSIS',
    ];

    /** fitur => roles yang boleh */
    public const MATRIX = [
        'Dashboard & Grafik' => ['admin', 'kepsek', 'wakasek', 'wali_kelas', 'osis'],
        'Lapor Pelanggaran' => ['admin', 'kepsek', 'wakasek', 'wali_kelas', 'osis'],
        'Laporan (daftar & cetak)' => ['admin', 'kepsek', 'wakasek', 'wali_kelas', 'osis'],
        'Data Siswa (lihat)' => ['admin', 'kepsek', 'wakasek', 'wali_kelas'],
        'Data Siswa (tambah/edit)' => ['admin', 'wali_kelas'],
        'Validasi Laporan' => ['admin', 'wali_kelas'],
        'Cetak SP / Sertifikat' => ['admin', 'wali_kelas'],
        'Master Kelas / Pelanggaran / Penghargaan' => ['admin'],
        'Pengaturan Sekolah' => ['admin'],
        'Kustomisasi Tampilan' => ['admin'],
        'Manajemen User' => ['admin'],
    ];

    public static function can(string $role, string $feature): bool
    {
        return in_array($role, self::MATRIX[$feature] ?? [], true);
    }

    public static function label(string $role): string
    {
        return self::ROLE_LABELS[$role] ?? $role;
    }
}
