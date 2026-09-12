<?php
namespace App\Core;

class Auth
{
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public static function user(): ?array
    {
        self::start();
        return $_SESSION['user'] ?? null;
    }

    public static function login(array $user): void
    {
        self::start();
        $_SESSION['user'] = $user;
    }

    public static function logout(): void
    {
        self::start();
        session_destroy();
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function requireLogin(): void
    {
        if (!self::check()) {
            header('Location: /login');
            exit;
        }
    }

    /** @param string[] $roles */
    public static function requireRole(array $roles): void
    {
        $user = self::user();
        if (!$user || !in_array($user['role'], $roles, true)) {
            http_response_code(403);
            echo '403 - Akses ditolak untuk role: ' . htmlspecialchars($user['role'] ?? 'guest');
            exit;
        }
    }

    public static function isWaliKelas(): bool
    {
        return (self::user()['role'] ?? '') === 'wali_kelas';
    }
}
