<?php
/**
 * models/User.php
 * Handles all DB interactions for the `users` table.
 * Security: Prepared Statements only. No raw variables in SQL.
 */
require_once __DIR__ . '/../config/database.php';

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a user by username.
     * Returns the row as an associative array, or false if not found.
     */
    public function findByUsername(string $username): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT id, username, password, nama_lengkap, role
             FROM   users
             WHERE  username = ?
             LIMIT  1'
        );
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    /**
     * Verify a plaintext password against its bcrypt hash.
     * password_verify() is inherently timing-attack-safe.
     */
    public function verifyPassword(string $plaintext, string $hash): bool
    {
        return password_verify($plaintext, $hash);
    }
}
