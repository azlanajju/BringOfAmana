<?php
/**
 * Bright of Amana – Database connection
 * Adjust host, user, password for your environment.
 */
$db_host = '193.203.184.167';
$db_name = 'u593219986_boa_db';
$db_user = 'u593219986_boa_user';
$db_pass = 'T2qGb1&N';

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
