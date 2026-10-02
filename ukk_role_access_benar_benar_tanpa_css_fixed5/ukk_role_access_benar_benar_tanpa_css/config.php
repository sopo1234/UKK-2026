<?php
$host = '127.0.0.1';
$db   = 'db_ukk_2026';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Koneksi database gagal: ' . htmlspecialchars($e->getMessage()));
}

/*
 * Pastikan t_user yang dipakai PHP adalah t_user di database db_ukk_2026.
 * Jika tabel/kolom belum lengkap, tambahkan kolom yang dibutuhkan aplikasi.
 * Ini menghindari error "Unknown column name/email" karena database lama.
 */
try {
    $exists = $pdo->query("SHOW TABLES LIKE 't_user'")->fetchColumn();

    if (!$exists) {
        $pdo->exec("CREATE TABLE t_user (
            id INT(11) NOT NULL AUTO_INCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            email_verified_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            password VARCHAR(255) NOT NULL,
            remember_token VARCHAR(100) NOT NULL DEFAULT '',
            role VARCHAR(255) NOT NULL DEFAULT 'guru',
            created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci");
    } else {
        $cols = [];
        foreach ($pdo->query("SHOW COLUMNS FROM t_user")->fetchAll() as $c) {
            $cols[strtolower($c['Field'])] = $c;
        }

        // Add missing columns without changing existing data.
        if (!isset($cols['id'])) {
            // A new id column can only be AUTO_INCREMENT if it is a key.
            $pdo->exec("ALTER TABLE t_user ADD COLUMN id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
        }
        $cols = [];
        foreach ($pdo->query("SHOW COLUMNS FROM t_user")->fetchAll() as $c) {
            $cols[strtolower($c['Field'])] = $c;
        }
        if (!isset($cols['name'])) {
            $pdo->exec("ALTER TABLE t_user ADD COLUMN name VARCHAR(255) NOT NULL DEFAULT '' AFTER id");
        }
        if (!isset($cols['email'])) {
            $pdo->exec("ALTER TABLE t_user ADD COLUMN email VARCHAR(255) NULL AFTER name");
        }
        if (!isset($cols['password'])) {
            $pdo->exec("ALTER TABLE t_user ADD COLUMN password VARCHAR(255) NULL AFTER email");
        }
        if (!isset($cols['role'])) {
            $pdo->exec("ALTER TABLE t_user ADD COLUMN role VARCHAR(255) NOT NULL DEFAULT 'guru' AFTER password");
        }
    }

    // Final real query: if this fails, PHP tells exactly which database/table is active.
    $pdo->query('SELECT `id`, `name`, `email`, `password`, `role` FROM `t_user` LIMIT 0');
} catch (PDOException $e) {
    $currentDb = $pdo->query('SELECT DATABASE()')->fetchColumn();
    die(
        'Database/tabel t_user belum bisa dipakai. ' .
        'PHP sedang memakai database: <strong>' . htmlspecialchars($currentDb ?: $db) . '</strong>. ' .
        'Detail: ' . htmlspecialchars($e->getMessage())
    );
}
