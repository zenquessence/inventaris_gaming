<?php
/**
 * controllers/AuthController.php
 *
 * Handles: showLogin, processLogin, logout, requireAuth (session guard).
 * Security:
 *   - CSRF token (bin2hex/random_bytes) validated on every POST
 *   - session_regenerate_id() on login (prevents session fixation)
 *   - Complete session destruction on logout
 *   - Generic error messages (prevents username enumeration)
 *   - bcrypt via User::verifyPassword()
 */
require_once __DIR__ . '/../models/User.php';

class AuthController
{
    // ----------------------------------------------------------------
    // Session Guard
    // ----------------------------------------------------------------

    /**
     * Redirect unauthenticated users to the login page.
     * Call at the top of every protected controller method.
     */
    public static function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = 'Silakan login terlebih dahulu.';
            header('Location: index.php?page=login');
            exit;
        }
    }

    // ----------------------------------------------------------------
    // Actions
    // ----------------------------------------------------------------

    /** Display the login form. */
    public static function showLogin(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: index.php?page=barang');
            exit;
        }
        self::generateCsrfToken();
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /** Process the POST login form. */
    public static function processLogin(): void
    {
        // 1. CSRF check
        if (
            empty($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'])
        ) {
            $_SESSION['flash_error'] = 'Token keamanan tidak valid. Muat ulang halaman.';
            header('Location: index.php?page=login');
            exit;
        }

        // 2. Server-side validation
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';   // Never trim passwords

        $errors = [];
        if ($username === '')        $errors[] = 'Username tidak boleh kosong.';
        if ($password === '')        $errors[] = 'Password tidak boleh kosong.';
        if (strlen($username) > 50)  $errors[] = 'Username terlalu panjang.';

        if (!empty($errors)) {
            $_SESSION['flash_error'] = implode(' ', $errors);
            header('Location: index.php?page=login');
            exit;
        }

        // 3. Lookup & verify
        $userModel = new User();
        $user      = $userModel->findByUsername($username);

        if ($user && $userModel->verifyPassword($password, $user['password'])) {
            // 4. Prevent session fixation
            session_regenerate_id(true);

            $_SESSION['user_id']      = (int) $user['id'];
            $_SESSION['username']     = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role']         = $user['role'];
            unset($_SESSION['csrf_token']);  // invalidate used token

            $_SESSION['flash_success'] = 'Selamat datang, ' . $user['nama_lengkap'] . '!';
            header('Location: index.php?page=barang');
        } else {
            $_SESSION['flash_error'] = 'Username atau password salah.';
            header('Location: index.php?page=login');
        }
        exit;
    }

    /** Destroy the session and redirect to login. */
    public static function logout(): void
    {
        self::requireAuth();

        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                      $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();

        session_start();
        $_SESSION['flash_success'] = 'Anda telah berhasil logout.';
        header('Location: index.php?page=login');
        exit;
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /** Generate (or retrieve) a CSRF token for the current session. */
    public static function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
}
