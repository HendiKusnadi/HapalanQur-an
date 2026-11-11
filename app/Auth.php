<?php
declare(strict_types=1);

namespace App;

use PDO;

class Auth
{
    public static function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            $localDir = dirname(__DIR__) . '/database/sessions';
            if (!is_dir($localDir)) {
                @mkdir($localDir, 0777, true);
            }
            if (is_dir($localDir)) {
                @session_save_path($localDir);
            }
            @session_start();
        }
    }

    public static function check(): bool
    {
        self::startSession();
        return !empty($_SESSION['admin_user_id']);
    }

    public static function user(): ?array
    {
        self::startSession();
        return $_SESSION['admin_user'] ?? null;
    }

    public static function id(): ?int
    {
        self::startSession();
        return isset($_SESSION['admin_user_id']) ? (int)$_SESSION['admin_user_id'] : null;
    }

    public static function name(): string
    {
        self::startSession();
        return $_SESSION['admin_user']['nama_lengkap'] ?? 'Ustadz Pembimbing';
    }

    public static function role(): string
    {
        self::startSession();
        return $_SESSION['admin_user']['role'] ?? 'admin';
    }

    public static function login(string $username, string $password): array
    {
        self::startSession();
        $db = Database::getConnection();

        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([trim($username)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'Username tidak ditemukan!'
            ];
        }

        if (!password_verify($password, $user['password'])) {
            return [
                'success' => false,
                'message' => 'Kata sandi yang Anda masukkan salah!'
            ];
        }

        // Login Berhasil
        $_SESSION['admin_user_id'] = (int)$user['id'];
        $_SESSION['admin_user'] = [
            'id' => (int)$user['id'],
            'username' => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'role' => $user['role']
        ];

        // Update waktu login terakhir
        $updateStmt = $db->prepare("UPDATE users SET terakhir_login = datetime('now', 'localtime') WHERE id = ?");
        $updateStmt->execute([$user['id']]);

        return [
            'success' => true,
            'message' => 'Login berhasil!'
        ];
    }

    public static function logout(): void
    {
        self::startSession();
        unset($_SESSION['admin_user_id'], $_SESSION['admin_user'], $_SESSION['redirect_after_login']);
    }

    public static function requireLogin(?string $redirectUrl = null): void
    {
        self::startSession();
        if (!self::check()) {
            if ($redirectUrl) {
                $_SESSION['redirect_after_login'] = $redirectUrl;
            } else {
                $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? 'dashboard';
            }
            header('Location: login');
            exit;
        }
    }

    public static function changePassword(int $userId, string $oldPassword, string $newPassword): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $currentHash = $stmt->fetchColumn();

        if (!$currentHash || !password_verify($oldPassword, $currentHash)) {
            return [
                'success' => false,
                'message' => 'Kata sandi lama tidak sesuai!'
            ];
        }

        if (strlen($newPassword) < 5) {
            return [
                'success' => false,
                'message' => 'Kata sandi baru minimal 5 karakter!'
            ];
        }

        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        $updateStmt = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
        $updateStmt->execute([$newHash, $userId]);

        return [
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui!'
        ];
    }

    public static function updateProfile(int $userId, string $username, string $namaLengkap): array
    {
        $db = Database::getConnection();
        
        // Cek duplikasi username untuk user lain
        $checkStmt = $db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkStmt->execute([$username, $userId]);
        if ($checkStmt->fetch()) {
            return [
                'success' => false,
                'message' => 'Username tersebut sudah digunakan oleh akun lain!'
            ];
        }

        $stmt = $db->prepare("UPDATE users SET username = ?, nama_lengkap = ? WHERE id = ?");
        $stmt->execute([$username, $namaLengkap, $userId]);

        self::startSession();
        if (isset($_SESSION['admin_user'])) {
            $_SESSION['admin_user']['username'] = $username;
            $_SESSION['admin_user']['nama_lengkap'] = $namaLengkap;
        }

        return [
            'success' => true,
            'message' => 'Profil admin berhasil diperbarui!'
        ];
    }
}
