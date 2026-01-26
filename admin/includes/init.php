<?php
/**
 * Bright of Amana – Admin init
 * Session, auth check, base path, DB.
 */
session_start();

$base = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
$base = $base ?: '/brightOfAmana';

if (empty($_SESSION['user_id']) || !in_array($_SESSION['user_role'] ?? '', ['super_admin', 'admin', 'staff'], true)) {
    header('Location: ../login/');
    exit;
}

require dirname(dirname(__DIR__)) . '/config/db.php';
