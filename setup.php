<?php
/**
 * setup.php
 * Run this script ONCE to generate the admin user with password_hash().
 */
require_once __DIR__ . '/config/database.php';

try {
    $db = Database::getInstance();
    
    // Check if admin already exists
    $stmt = $db->prepare('SELECT id FROM users WHERE username = ?');
    $stmt->execute(['admin']);
    if ($stmt->fetch()) {
        die('Admin user already exists!');
    }

    // Generate hashed password
    $rawPassword = 'admin'; // Change this if needed
    $hashedPassword = password_hash($rawPassword, PASSWORD_BCRYPT);

    // Insert user
    $insertStmt = $db->prepare('
        INSERT INTO users (username, password, nama_lengkap, role) 
        VALUES (?, ?, ?, ?)
    ');
    
    $insertStmt->execute([
        'admin', 
        $hashedPassword, 
        'Administrator Utama', 
        'admin'
    ]);

    echo "✅ Admin user created successfully with password_hash()!<br>";
    echo "Username: admin<br>";
    echo "Password: admin<br>";
    echo "<a href='index.php?page=login'>Go to Login</a>";

} catch (Exception $e) {
    die("❌ Error setting up admin: " . $e->getMessage());
}
