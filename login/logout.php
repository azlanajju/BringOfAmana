<?php
/**
 * Bright of Amana – Logout
 * Destroys session and redirects to centralized login.
 */
session_start();
$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}
session_destroy();

$base = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
$base = $base ?: '/brightOfAmana';
header('Location: index.php');
exit;
