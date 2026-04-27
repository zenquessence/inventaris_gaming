<?php
/**
 * config/database.php
 * PDO Singleton — Config Layer
 *
 * Security: ATTR_EMULATE_PREPARES = false enforces TRUE prepared statements,
 *           making SQL injection via bound parameters impossible.
 */

define('DB_HOST',    'localhost');
define('DB_NAME',    'db_inventaris_gaming');
define('DB_USER',    'root');
define('DB_PASS',    '');           // Change for production
define('DB_CHARSET', 'utf8mb4');

class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone()     {}

    /**
     * Returns the shared PDO instance (lazy init on first call).
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                DB_HOST, DB_NAME, DB_CHARSET
            );
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,  // ← strict prepared stmts
            ];
            try {
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                error_log('[DB Error] ' . $e->getMessage());
                die('<p style="font-family:sans-serif;color:#f87171;text-align:center;margin-top:4rem;">
                     Koneksi database gagal. Hubungi administrator.</p>');
            }
        }
        return self::$instance;
    }
}
