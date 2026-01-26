<?php
/**
 * Bright of Amana – Add / Edit admin user
 */
require __DIR__ . '/includes/init.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$isEdit = $id > 0;
$error = '';
$success = '';

$name = $email = $phone = $role = '';
$status = 'active';
$passwordRequired = true;

if ($isEdit) {
    $stmt = $pdo->prepare("
        SELECT id, name, email, phone, role, status
        FROM users
        WHERE id = ? AND role IN ('super_admin', 'admin', 'staff')
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        header('Location: users.php');
        exit;
    }
    $name = $row['name'];
    $email = $row['email'];
    $phone = $row['phone'] ?? '';
    $role = $row['role'];
    $status = $row['status'];
    $passwordRequired = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $role = $_POST['role'] ?? 'admin';
    $status = $_POST['status'] ?? 'active';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$name || !$email) {
        $error = 'Name and email are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!in_array($role, ['super_admin', 'admin', 'staff'], true)) {
        $error = 'Invalid role selected.';
    } elseif (!$isEdit && !$password) {
        $error = 'Password is required for new admins.';
    } elseif (!$isEdit && strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif (!$isEdit && $password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } elseif ($isEdit && $password && strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($isEdit && $password && $password !== $password_confirm) {
        $error = 'Passwords do not match.';
    } else {
        try {
            if ($isEdit) {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$email, $id]);
            } else {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
            }
            if ($stmt->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                if ($isEdit) {
                    $sql = "UPDATE users SET name = ?, email = ?, phone = ?, role = ?, status = ? WHERE id = ?";
                    $params = [$name, $email, $phone ?: null, $role, $status, $id];
                    if ($password) {
                        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, role = ?, status = ?, password_hash = ? WHERE id = ?";
                        $params = [$name, $email, $phone ?: null, $role, $status, password_hash($password, PASSWORD_DEFAULT), $id];
                    }
                    $pdo->prepare($sql)->execute($params);
                    $success = 'Admin updated.';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $pdo->prepare("INSERT INTO users (name, email, phone, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)")
                        ->execute([$name, $email, $phone ?: null, $hash, $role, $status]);
                    $success = 'Admin created. They can sign in at the login page.';
                }
                if ($success && !$isEdit) {
                    header('Location: users.php?created=1');
                    exit;
                }
            }
        } catch (PDOException $e) {
            $error = 'Could not save. Please try again.';
        }
    }
}

$page = 'users';
$title = $isEdit ? 'Edit admin' : 'Add admin';
require __DIR__ . '/includes/header.php';
?>

<h1 class="page-title"><?= $isEdit ? 'Edit admin' : 'Add admin' ?></h1>

<?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card">
  <form method="post" action="">
    <div class="form-group">
      <label for="name">Name *</label>
      <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>
    </div>
    <div class="form-group">
      <label for="email">Email *</label>
      <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required <?= $isEdit ? '' : 'autocomplete="off"' ?>>
    </div>
    <div class="form-group">
      <label for="phone">Phone</label>
      <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">
    </div>
    <div class="form-group">
      <label for="role">Role *</label>
      <select id="role" name="role" required>
        <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
        <option value="staff" <?= $role === 'staff' ? 'selected' : '' ?>>Staff</option>
        <option value="super_admin" <?= $role === 'super_admin' ? 'selected' : '' ?>>Super Admin</option>
      </select>
    </div>
    <?php if ($isEdit): ?>
    <div class="form-group">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="active" <?= $status === 'active' ? 'selected' : '' ?>>Active</option>
        <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Inactive</option>
        <option value="suspended" <?= $status === 'suspended' ? 'selected' : '' ?>>Suspended</option>
      </select>
    </div>
    <?php endif; ?>
    <?php if ($isEdit): ?>
    <div class="form-group">
      <label for="password">New password <span style="color:#718096;font-weight:400">(leave blank to keep)</span></label>
      <input type="password" id="password" name="password" minlength="8" autocomplete="new-password">
    </div>
    <div class="form-group">
      <label for="password_confirm">Confirm new password</label>
      <input type="password" id="password_confirm" name="password_confirm" minlength="8" autocomplete="new-password">
    </div>
    <?php else: ?>
    <div class="form-group">
      <label for="password">Password *</label>
      <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password">
    </div>
    <div class="form-group">
      <label for="password_confirm">Confirm password *</label>
      <input type="password" id="password_confirm" name="password_confirm" required minlength="8" autocomplete="new-password">
    </div>
    <?php endif; ?>
    <div class="form-group form-actions">
      <button type="submit" class="btn"><?= $isEdit ? 'Update' : 'Create' ?></button>
      <a href="users.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
