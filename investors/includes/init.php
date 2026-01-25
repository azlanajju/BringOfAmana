<?php
/**
 * Bright of Amana – Investor init
 * Session, auth check, base path, DB, get investor info.
 */
session_start();

$base = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
$base = $base ?: '/brightOfAmana';

if (empty($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'investor') {
    header('Location: ' . $base . '/login/');
    exit;
}

require dirname(dirname(__DIR__)) . '/config/db.php';

// Get investor record
$stmt = $pdo->prepare("
    SELECT i.id, i.investor_code, i.join_date, i.notes,
           u.name, u.email, u.phone, u.status
    FROM investors i
    JOIN users u ON u.id = i.user_id
    WHERE u.id = ?
");
$stmt->execute([$_SESSION['user_id']]);
$investor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$investor) {
    header('Location: ' . $base . '/login/logout.php');
    exit;
}
