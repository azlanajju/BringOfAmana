<?php
/**
 * Bright of Amana – Database connection
 * Adjust host, user, password for your environment.
 */
$db_host = '127.0.0.1';
$db_name = 'bright_of_amana';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO(
        "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $e) {
    throw new PDOException('Database connection failed: ' . $e->getMessage());
}
