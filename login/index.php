<?php
/**
 * Bright of Amana – Centralized Login
 * Single entry for Admin & Investor. Redirects by role after auth.
 */
session_start();

$base = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
$base = $base ?: '/brightOfAmana';

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$email || !$password) {
        $error = 'Please enter email and password.';
    } else {
        try {
            require dirname(__DIR__) . '/config/db.php';
            $stmt = $pdo->prepare(
                "SELECT id, name, email, password_hash, role, status FROM users WHERE email = ? LIMIT 1"
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                $error = 'Invalid email or password.';
            } elseif ($user['status'] !== 'active') {
                $error = 'Your account is not active. Please contact support.';
            } elseif (!password_verify($password, $user['password_hash'])) {
                $error = 'Invalid email or password.';
            } else {
                $_SESSION['user_id']   = (int) $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                $stmt = $pdo->prepare("UPDATE users SET last_login_at = NOW() WHERE id = ?");
                $stmt->execute([$user['id']]);

                $isAdmin = in_array($user['role'], ['super_admin', 'admin', 'staff'], true);
                $redirect = $isAdmin ? '../admin/' : '../investors/';
                header('Location: ' . $redirect);
                exit;
            }
        } catch (PDOException $e) {
            // Keep user-facing message generic, but log root cause for debugging.
            error_log(
                sprintf(
                    '[LOGIN_ERROR] email=%s ip=%s message=%s',
                    $email,
                    $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                    $e->getMessage()
                )
            );
            $error = 'Unable to sign in right now (system error). Please try again.';
        }
    }
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign in – Bright of Amana Business Group</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    :root {
      --green: #268e45;
      --green-dark: #1e6f38;
      --green-light: #d4edda;
      --text: #1a202c;
      --text-muted: #718096;
      --border: #e2e8f0;
      --error-bg: #fef2f2;
      --error-border: #fecaca;
      --error-text: #b91c1c;
    }
    body {
      min-height: 100vh;
      font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
      background: #fff;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 1.5rem;
      color: var(--text);
    }
    .back-link {
      position: absolute;
      top: 1.25rem;
      left: 1.5rem;
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      color: var(--text-muted);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: color .2s;
    }
    .back-link:hover { color: var(--text); }
    .back-link svg { width: 18px; height: 18px; flex-shrink: 0; }
    .wrap {
      width: 100%;
      max-width: 420px;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 1rem;
    }
    .card {
      width: 100%;
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,.08), 0 0 0 1px rgba(0,0,0,.06);
      padding: 2.25rem 2rem;
    }
    .card-logo {
      text-align: center;
      margin-bottom: 1.25rem;
    }
    .card-logo img {
      width: 72px;
      height: auto;
      display: block;
      margin: 0 auto;
    }
    .card-header {
      text-align: center;
      margin-bottom: 1.75rem;
    }
    .card-header h1 {
      font-size: 1.35rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 0.25rem;
    }
    .card-header p {
      font-size: 0.9rem;
      color: var(--text-muted);
      margin: 0;
    }
    .badge {
      display: inline-block;
      margin-top: 0.75rem;
      padding: 0.25rem 0.75rem;
      font-size: 0.75rem;
      font-weight: 600;
      color: var(--green);
      background: var(--green-light);
      border-radius: 999px;
    }
    form { display: flex; flex-direction: column; gap: 1.25rem; }
    .field {
      display: flex;
      flex-direction: column;
      gap: 0.4rem;
    }
    label {
      font-size: 0.875rem;
      font-weight: 600;
      color: var(--text);
    }
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 0.85rem 1rem;
      font-size: 1rem;
      border: 1px solid var(--border);
      border-radius: 10px;
      transition: border-color .2s, box-shadow .2s;
      background: #fff;
    }
    input::placeholder { color: #a0aec0; }
    input[type="email"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: var(--green);
      box-shadow: 0 0 0 3px rgba(38, 142, 69, .15);
    }
    .btn {
      margin-top: 0.25rem;
      padding: 0.9rem 1.25rem;
      font-size: 1rem;
      font-weight: 600;
      color: #fff;
      background: var(--green);
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background .2s, transform .05s;
    }
    .btn:hover { background: var(--green-dark); }
    .btn:active { transform: scale(0.99); }
    .btn:focus-visible {
      outline: none;
      box-shadow: 0 0 0 3px rgba(38, 142, 69, .35);
    }
    .error {
      padding: 0.85rem 1rem;
      font-size: 0.875rem;
      color: var(--error-text);
      background: var(--error-bg);
      border: 1px solid var(--error-border);
      border-radius: 10px;
      margin-bottom: 0.25rem;
    }
    .card-footer {
      margin-top: 1.5rem;
      padding-top: 1.25rem;
      border-top: 1px solid var(--border);
      text-align: center;
    }
    .card-footer a {
      color: var(--green);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: color .2s;
    }
    .card-footer a:hover { color: var(--green-dark); }
    .home-link {
      margin-top: 0.25rem;
      font-size: 0.85rem;
      color: var(--text-muted);
      text-decoration: none;
      transition: color .2s;
    }
    .home-link:hover { color: var(--text); }
    @media (max-width: 480px) {
      .card { padding: 1.75rem 1.5rem; }
      .back-link { left: 1rem; font-size: 0.85rem; }
    }
  </style>
</head>
<body>
  <a href="../" class="back-link" aria-label="Back to home">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    Back to home
  </a>

  <div class="wrap">
    <div class="card">
      <div class="card-logo">
        <img src="../assets/BABG_Logo.png" alt="Bright of Amana" width="72" height="auto">
      </div>
      <div class="card-header">
        <h1>Sign in</h1>
        <p>Business Group – Investment Management</p>
        <span class="badge">Admin &amp; Investors</span>
      </div>

      <?php if ($error): ?>
        <div class="error" role="alert"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="">
        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>"
                 placeholder="you@example.com" required autocomplete="email" autofocus>
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="••••••••"
                 required autocomplete="current-password">
        </div>
        <button type="submit" class="btn">Sign in</button>
      </form>

      <div class="card-footer">
        <a href="#">Forgot password?</a>
      </div>
    </div>

    <a href="../" class="home-link">← Bright of Amana Business Group</a>
  </div>
</body>
</html>
