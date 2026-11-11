<?php
declare(strict_types=1);

namespace App;

use PDO;

class Security
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 10;

    /**
     * Terapkan header keamanan HTTP & konfigurasi session aman
     */
    public static function init(): void
    {
        if (!headers_sent()) {
            // Sembunyikan versi PHP
            @header_remove('X-Powered-By');

            // Proteksi Clickjacking: hanya izinkan rendering dalam iframe dari domain yang sama
            header('X-Frame-Options: SAMEORIGIN');

            // Proteksi MIME sniffing
            header('X-Content-Type-Options: nosniff');

            // Proteksi XSS pada peramban jadul
            header('X-XSS-Protection: 1; mode=block');

            // Batasi pengiriman referer ke domain eksternal
            header('Referrer-Policy: strict-origin-when-cross-origin');

            // Izin akses perangkat: Izinkan mikrofon untuk rekam suara santri, blokir kamera & lokasi
            header('Permissions-Policy: camera=(), geolocation=(), payment=(), microphone=(self)');
        }

        // Konfigurasi session cookie yang aman
        if (session_status() === PHP_SESSION_NONE) {
            @ini_set('session.cookie_httponly', '1');
            @ini_set('session.use_only_cookies', '1');
            @ini_set('session.cookie_samesite', 'Lax');

            if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
                @ini_set('session.cookie_secure', '1');
            }
        }
    }

    /**
     * Dapatkan IP pengunjung yang aman
     */
    public static function getClientIp(): string
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        // Filter format IP valid
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            return $ip;
        }
        return '127.0.0.1';
    }

    /**
     * Generate atau ambil CSRF Token dari session
     */
    public static function csrfToken(): string
    {
        Auth::startSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * HTML input tersembunyi untuk CSRF form
     */
    public static function csrfField(): string
    {
        $token = htmlspecialchars(self::csrfToken(), ENT_QUOTES, 'UTF-8');
        return '<input type="hidden" name="csrf_token" value="' . $token . '">';
    }

    /**
     * Validasi token CSRF dari POST request atau Header HTTP
     */
    public static function verifyCsrf(?string $token = null): bool
    {
        Auth::startSession();
        $sessionToken = $_SESSION['csrf_token'] ?? '';

        if (empty($sessionToken)) {
            return false;
        }

        $submittedToken = $token 
            ?? $_POST['csrf_token'] 
            ?? $_SERVER['HTTP_X_CSRF_TOKEN'] 
            ?? '';

        if (!is_string($submittedToken) || empty($submittedToken)) {
            return false;
        }

        return hash_equals($sessionToken, $submittedToken);
    }

    /**
     * Cek rate limit percobaan login untuk mencegah serangan brute force
     */
    public static function checkRateLimit(string $ip, string $username = ''): array
    {
        try {
            $db = Database::getConnection();
            
            // Bersihkan riwayat lama yang sudah lewat dari masa lockout
            $cleanupStmt = $db->prepare("DELETE FROM login_attempts WHERE attempted_at < datetime('now', '-' || ? || ' minutes')");
            $cleanupStmt->execute([self::LOCKOUT_MINUTES]);

            // Hitung percobaan gagal dalam jendela waktu LOCKOUT_MINUTES
            $stmt = $db->prepare("
                SELECT COUNT(*) as attempts, 
                       strftime('%s', 'now') - strftime('%s', MAX(attempted_at)) as seconds_since_last
                FROM login_attempts 
                WHERE ip_address = ? OR (username = ? AND username != '')
            ");
            $stmt->execute([$ip, $username]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);

            $attempts = (int)($res['attempts'] ?? 0);
            $secondsSinceLast = (int)($res['seconds_since_last'] ?? 0);
            $lockoutSeconds = self::LOCKOUT_MINUTES * 60;

            if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
                $remainingSeconds = max(1, $lockoutSeconds - $secondsSinceLast);
                $remainingMinutes = ceil($remainingSeconds / 60);

                return [
                    'allowed' => false,
                    'attempts' => $attempts,
                    'remaining_seconds' => $remainingSeconds,
                    'remaining_minutes' => $remainingMinutes,
                    'message' => "Terlalu banyak percobaan login gagal. Demi keamanan, akun terkunci sementara selama {$remainingMinutes} menit."
                ];
            }

            return [
                'allowed' => true,
                'attempts' => $attempts,
                'remaining_attempts' => max(0, self::MAX_LOGIN_ATTEMPTS - $attempts)
            ];
        } catch (\Throwable $e) {
            // Fallback jika database belum ada tabel login_attempts
            return ['allowed' => true, 'attempts' => 0];
        }
    }

    /**
     * Catat percobaan login gagal
     */
    public static function recordFailedLogin(string $ip, string $username = ''): void
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("INSERT INTO login_attempts (ip_address, username, attempted_at) VALUES (?, ?, datetime('now'))");
            $stmt->execute([$ip, $username]);
        } catch (\Throwable $e) {}
    }

    /**
     * Bersihkan riwayat login gagal setelah berhasil login
     */
    public static function clearLoginAttempts(string $ip, string $username = ''): void
    {
        try {
            $db = Database::getConnection();
            $stmt = $db->prepare("DELETE FROM login_attempts WHERE ip_address = ? OR username = ?");
            $stmt->execute([$ip, $username]);
        } catch (\Throwable $e) {}
    }

    /**
     * Sanitasi string untuk output HTML (mencegah XSS)
     */
    public static function escape(?string $string): string
    {
        return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

