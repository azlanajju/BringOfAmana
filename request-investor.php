<?php
/**
 * Request to Be an Investor – Bright of Amana Business Group
 * Form + contact details. Submissions stored in investor_requests.
 */
$base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
$base = $base ?: '/brightOfAmana';

$success = false;
$error = '';
$name = $email = $phone = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (!$name || !$email) {
        $error = 'Please enter your name and email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        try {
            require __DIR__ . '/config/db.php';

            $sql = "CREATE TABLE IF NOT EXISTS `investor_requests` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `name` VARCHAR(100) NOT NULL,
              `email` VARCHAR(150) NOT NULL,
              `phone` VARCHAR(20) NULL DEFAULT NULL,
              `message` TEXT NULL DEFAULT NULL,
              `status` ENUM('new','contacted','rejected') NOT NULL DEFAULT 'new',
              `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`),
              KEY `investor_requests_status_idx` (`status`),
              KEY `investor_requests_created_at_idx` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
            $pdo->exec($sql);

            $stmt = $pdo->prepare(
                "INSERT INTO investor_requests (name, email, phone, message) VALUES (?, ?, ?, ?)"
            );
            $stmt->execute([$name, $email, $phone ?: null, $message ?: null]);
            $success = true;
            $name = $email = $phone = $message = '';
        } catch (PDOException $e) {
            $error = 'Unable to submit your request. Please try again or contact us directly.';
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
  <title>Request to Be an Investor – Bright of Amana Business Group</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
      color: #0f172a;
      line-height: 1.6;
      overflow-x: hidden;
    }
    :root {
      --green: #268e45;
      --green-dark: #1e6f38;
      --green-light: #d4edda;
      --green-bg: #f0fdf4;
      --text: #0f172a;
      --text-muted: #64748b;
      --border: #e2e8f0;
      --bg: #f8fafc;
      --white: #ffffff;
      --error-bg: #fef2f2;
      --error-border: #fecaca;
      --error-text: #b91c1c;
    }
    .container { max-width: 1160px; margin: 0 auto; padding: 0 1.5rem; }

    header {
      background: var(--white);
      box-shadow: 0 1px 3px rgba(0,0,0,.04);
      position: sticky;
      top: 0;
      z-index: 100;
    }
    nav {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 1.5rem;
    }
    .logo {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      text-decoration: none;
      color: inherit;
    }
    .logo img { height: 44px; width: auto; display: block; }
    .logo-text { font-size: 1.2rem; font-weight: 700; color: var(--green); }
    .logo-tag { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; display: block; }
    .nav-links { display: flex; gap: 2rem; align-items: center; }
    .btn-nav {
      padding: 0.5rem 1.15rem;
      background: transparent;
      color: var(--green);
      border: 1.5px solid var(--green);
      border-radius: 10px;
      text-decoration: none;
      font-weight: 600;
      font-size: 0.9rem;
      transition: all .2s;
    }
    .btn-nav:hover {
      background: var(--green);
      color: #fff;
      border-color: transparent;
    }
    .btn-nav:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(38,142,69,.25); }

    .page-hero {
      background: linear-gradient(135deg, var(--green) 0%, var(--green-dark) 100%);
      color: #fff;
      padding: clamp(3rem, 6vw, 4.5rem) 1.5rem;
      text-align: center;
    }
    .page-hero h1 {
      font-size: clamp(1.75rem, 4vw, 2.25rem);
      font-weight: 800;
      margin-bottom: 0.5rem;
      letter-spacing: -0.02em;
    }
    .page-hero p {
      font-size: 1.05rem;
      opacity: .92;
    }

    .main-section {
      padding: clamp(3rem, 6vw, 4rem) 1.5rem;
    }
    .two-col {
      display: grid;
      grid-template-columns: 1fr 360px;
      gap: 3rem;
      max-width: 960px;
      margin: 0 auto;
      align-items: start;
    }
    .form-card {
      background: var(--white);
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,.08), 0 0 0 1px rgba(0,0,0,.06);
      padding: 2rem;
    }
    .form-card h2 {
      font-size: 1.35rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 1.5rem;
    }
    .field { margin-bottom: 1.25rem; }
    .field label {
      display: block;
      font-size: 0.9rem;
      font-weight: 600;
      color: var(--text);
      margin-bottom: 0.4rem;
    }
    .field input, .field textarea {
      width: 100%;
      padding: 0.75rem 1rem;
      font-size: 1rem;
      font-family: inherit;
      border: 1px solid var(--border);
      border-radius: 10px;
      transition: border-color .2s, box-shadow .2s;
    }
    .field input:focus, .field textarea:focus {
      outline: none;
      border-color: var(--green);
      box-shadow: 0 0 0 3px rgba(38,142,69,.15);
    }
    .field textarea { min-height: 120px; resize: vertical; }
    .btn-submit {
      width: 100%;
      padding: 0.9rem 1.25rem;
      font-size: 1rem;
      font-weight: 600;
      color: #fff;
      background: var(--green);
      border: none;
      border-radius: 10px;
      cursor: pointer;
      transition: background .2s;
    }
    .btn-submit:hover { background: var(--green-dark); }
    .btn-submit:focus-visible { outline: none; box-shadow: 0 0 0 3px rgba(38,142,69,.35); }
    .error-msg {
      padding: 0.85rem 1rem;
      font-size: 0.9rem;
      color: var(--error-text);
      background: var(--error-bg);
      border: 1px solid var(--error-border);
      border-radius: 10px;
      margin-bottom: 1.25rem;
    }
    .success-msg {
      padding: 1.25rem 1.5rem;
      font-size: 1rem;
      color: var(--green-dark);
      background: var(--green-light);
      border: 1px solid rgba(38,142,69,.3);
      border-radius: 12px;
      margin-bottom: 1.5rem;
    }
    .success-msg strong { display: block; margin-bottom: 0.35rem; }

    .contact-card {
      background: var(--green-bg);
      border-radius: 16px;
      border: 1px solid var(--green-light);
      padding: 2rem;
      position: sticky;
      top: 100px;
    }
    .contact-card h3 {
      font-size: 1.1rem;
      font-weight: 700;
      color: var(--text);
      margin-bottom: 1rem;
    }
    .contact-card p {
      font-size: 0.95rem;
      color: var(--text-muted);
      line-height: 1.6;
      margin-bottom: 1rem;
    }
    .contact-card .person {
      font-weight: 700;
      color: var(--green);
      font-size: 1.05rem;
      margin-bottom: 0.25rem;
    }
    .contact-card .role { font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1rem; }
    .contact-card a[href^="tel:"] {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      color: var(--green);
      font-weight: 600;
      text-decoration: none;
      font-size: 1.05rem;
      transition: color .2s;
    }
    .contact-card a[href^="tel:"]:hover { color: var(--green-dark); }
    .contact-card .divider {
      height: 1px;
      background: var(--border);
      margin: 1.25rem 0;
    }
    .back-link {
      display: inline-flex;
      align-items: center;
      gap: 0.35rem;
      color: var(--green);
      font-weight: 600;
      text-decoration: none;
      font-size: 0.9rem;
      margin-top: 1rem;
      transition: color .2s;
    }
    .back-link:hover { color: var(--green-dark); }

    footer {
      background: #0f172a;
      color: #fff;
      padding: 2rem 1.5rem;
      text-align: center;
    }
    footer a {
      color: rgba(255,255,255,.8);
      text-decoration: none;
      font-weight: 500;
      transition: color .2s;
    }
    footer a:hover { color: #fff; }
    footer p { color: rgba(255,255,255,.6); font-size: 0.9rem; margin-top: 0.5rem; }

    @media (max-width: 900px) {
      .two-col { grid-template-columns: 1fr; }
      .contact-card { position: static; }
    }
  </style>
</head>
<body>
  <header>
    <nav class="container">
        <a href="./" class="logo">
        <img src="assets/BABG_Logo.png" alt="Bright of Amana">
        <div>
          <span class="logo-text">Bright of Amana</span>
          <span class="logo-tag">Business Group</span>
        </div>
      </a>
      <div class="nav-links">
        <a href="login/" class="btn-nav">Sign In</a>
      </div>
    </nav>
  </header>

  <section class="page-hero">
    <div class="container">
      <h1>Request to Be an Investor</h1>
      <p>Share your contact details and we’ll get in touch to guide you through the process.</p>
    </div>
  </section>

  <section class="main-section">
    <div class="container">
      <div class="two-col">
        <div class="form-card">
          <h2>Submit your request</h2>
          <?php if ($success): ?>
            <div class="success-msg">
              <strong>Thank you for your interest.</strong>
              We’ve received your request and will contact you shortly.
            </div>
            <a href="./" class="back-link">← Back to home</a>
          <?php else: ?>
            <?php if ($error): ?>
              <div class="error-msg" role="alert"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post" action="">
              <div class="field">
                <label for="name">Full name <span style="color:var(--error-text)">*</span></label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>"
                       placeholder="Your full name" required autocomplete="name" autofocus>
              </div>
              <div class="field">
                <label for="email">Email <span style="color:var(--error-text)">*</span></label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>"
                       placeholder="you@example.com" required autocomplete="email">
              </div>
              <div class="field">
                <label for="phone">Phone</label>
                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>"
                       placeholder="+91 98765 43210" autocomplete="tel">
              </div>
              <div class="field">
                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Tell us about your investment goals or any questions..."><?= htmlspecialchars($message) ?></textarea>
              </div>
              <button type="submit" class="btn-submit">Submit request</button>
            </form>
          <?php endif; ?>
        </div>

        <div class="contact-card">
          <h3>Contact us</h3>
          <p>Prefer to reach out directly? Use the details below.</p>
          <div class="person">Mohammad Sinan</div>
          <div class="role">Founder &amp; CEO, Bright of Amana Business Group</div>
          <a href="tel:+919964396818">+91 99643 96818</a>
          <div class="divider"></div>
          <p>Already have an account?</p>
          <a href="login/" class="back-link">Sign in →</a>
        </div>
      </div>
    </div>
  </section>

  <footer>
    <div class="container">
      <a href="./">Bright of Amana Business Group</a>
      <p>&copy; <?= date('Y') ?> Bright of Amana Business Group. All rights reserved.</p>
    </div>
  </footer>
</body>
</html>
