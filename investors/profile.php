<?php
/**
 * Bright of Amana – Investor Profile
 */
require __DIR__ . '/includes/init.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$name) {
        $error = 'Name is required.';
    } elseif ($password && strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password && $password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $sql = "UPDATE users SET name = ?, phone = ? WHERE id = ?";
            $params = [$name, $phone ?: null, $_SESSION['user_id']];

            if ($password) {
                $sql = "UPDATE users SET name = ?, phone = ?, password_hash = ? WHERE id = ?";
                $params = [$name, $phone ?: null, password_hash($password, PASSWORD_DEFAULT), $_SESSION['user_id']];
            }

            $pdo->prepare($sql)->execute($params);

            $_SESSION['user_name'] = $name;

            $stmt = $pdo->prepare("
                SELECT i.id, i.investor_code, i.join_date, i.notes,
                       u.name, u.email, u.phone, u.status
                FROM investors i
                JOIN users u ON u.id = i.user_id
                WHERE u.id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $investor = $stmt->fetch(PDO::FETCH_ASSOC);

            $success = 'Profile updated successfully.';
        } catch (PDOException $e) {
            $error = 'Could not update profile. Please try again.';
        }
    }
}

$page = 'profile';
$title = 'Profile';
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Profile</h1>
    <p class="page-subtitle">Manage your account</p>
  </div>
</div>

<?php if ($error): ?>
  <div class="error" role="alert"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card">
  <h2>Account Information</h2>
  <table>
    <tr><th style="width:180px;">Investor Code</th><td><?= htmlspecialchars($investor['investor_code']) ?></td></tr>
    <tr><th>Email</th><td><?= htmlspecialchars($investor['email']) ?></td></tr>
    <tr><th>Join Date</th><td><?= date('M j, Y', strtotime($investor['join_date'])) ?></td></tr>
    <tr><th>Status</th><td><span class="badge badge-<?= $investor['status'] === 'active' ? 'active' : 'inactive' ?>"><?= htmlspecialchars($investor['status']) ?></span></td></tr>
  </table>
</div>

<div class="card">
  <h2>Edit Profile</h2>
  <form method="post" action="">
    <div class="form-group">
      <label for="name">Name *</label>
      <input type="text" id="name" name="name" value="<?= htmlspecialchars($investor['name']) ?>" required>
    </div>
    <div class="form-group">
      <label for="phone">Phone</label>
      <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($investor['phone'] ?? '') ?>" placeholder="+91 98765 43210">
    </div>
    <div class="form-group">
      <label for="password">New Password <small style="color:var(--text-muted);font-weight:400">(leave blank to keep current)</small></label>
      <input type="password" id="password" name="password" minlength="8" autocomplete="new-password">
    </div>
    <div class="form-group">
      <label for="password_confirm">Confirm New Password</label>
      <input type="password" id="password_confirm" name="password_confirm" minlength="8" autocomplete="new-password">
    </div>
    <div class="form-group form-actions">
      <button type="submit" class="btn">Update Profile</button>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
