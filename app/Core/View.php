<?php
namespace App\Core;

class View
{
    public static function render(string $view, array $data = []): void
    {
        extract($data);
        $user = Auth::user();
        $setting = $data['setting'] ?? \App\Models\SettingModel::get();
        $tampilan = $data['tampilan'] ?? \App\Models\TampilanModel::get();
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';
        require __DIR__ . '/../Views/layouts/header.php';
        require $viewFile;
        require __DIR__ . '/../Views/layouts/footer.php';
    }

    /** Render tanpa layout (untuk dokumen cetak SP/Sertifikat) */
    public static function renderPrint(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/' . $view . '.php';
    }
}
