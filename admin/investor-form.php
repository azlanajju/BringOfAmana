<?php
/**
 * Bright of Amana – Add / Edit investor
 */
require __DIR__ . '/includes/init.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$isEdit = $id > 0;
$error = '';
$success = '';

$name = $email = $phone = $investor_code = $join_date = $notes = '';
$status = 'active';
$passwordRequired = true;

if ($isEdit) {
    $stmt = $pdo->prepare("
        SELECT i.id, i.investor_code, i.join_date, i.notes, i.user_id,
               u.name, u.email, u.phone, u.status
        FROM investors i
        JOIN users u ON u.id = i.user_id
        WHERE i.id = ?
    ");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        header('Location: ' . $base . '/admin/investors.php');
        exit;
    }
    $name = $row['name'];
    $email = $row['email'];
    $phone = $row['phone'] ?? '';
    $investor_code = $row['investor_code'];
    $join_date = $row['join_date'];
    $notes = $row['notes'] ?? '';
    $status = $row['status'];
    $passwordRequired = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $investor_code = trim($_POST['investor_code'] ?? '');
    $join_date = $_POST['join_date'] ?? '';
    $notes = trim($_POST['notes'] ?? '');
    $status = $_POST['status'] ?? 'active';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (!$name || !$email || !$investor_code || !$join_date) {
        $error = 'Name, email, investor code and join date are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (!$isEdit && !$password) {
        $error = 'Password is required for new investors.';
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
                $stmt->execute([$email, $row['user_id']]);
            } else {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
            }
            if ($stmt->fetch()) {
                $error = 'An account with this email already exists.';
            } else {
                $stmt = $pdo->prepare("SELECT id FROM investors WHERE investor_code = ? AND id != ?");
                $stmt->execute([$investor_code, $id ?: 0]);
                if ($stmt->fetch()) {
                    $error = 'Investor code already in use.';
                } else {
                    if ($isEdit) {
                        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, status = ? WHERE id = ?";
                        $params = [$name, $email, $phone ?: null, $status, $row['user_id']];
                        if ($password) {
                            $sql = "UPDATE users SET name = ?, email = ?, phone = ?, status = ?, password_hash = ? WHERE id = ?";
                            $params = [$name, $email, $phone ?: null, $status, password_hash($password, PASSWORD_DEFAULT), $row['user_id']];
                        }
                        $pdo->prepare($sql)->execute($params);
                        $pdo->prepare("UPDATE investors SET investor_code = ?, join_date = ?, notes = ? WHERE id = ?")
                            ->execute([$investor_code, $join_date, $notes ?: null, $id]);
                        $success = 'Investor updated.';
                    } else {
                        $hash = password_hash($password, PASSWORD_DEFAULT);
                        $pdo->prepare("INSERT INTO users (name, email, phone, password_hash, role, status) VALUES (?, ?, ?, ?, 'investor', ?)")
                            ->execute([$name, $email, $phone ?: null, $hash, $status]);
                        $uid = (int) $pdo->lastInsertId();
                        $pdo->prepare("INSERT INTO investors (user_id, investor_code, join_date, notes) VALUES (?, ?, ?, ?)")
                            ->execute([$uid, $investor_code, $join_date, $notes ?: null]);
                        $success = 'Investor created. They can sign in at the login page.';
                    }
                    if ($success && !$isEdit) {
                        header('Location: ' . $base . '/admin/investors.php?created=1');
                        exit;
                    }
                }
            }
        } catch (PDOException $e) {
            $error = 'Could not save. Please try again.';
        }
    }
}

$page = 'investors';
$title = $isEdit ? 'Edit investor' : 'Add investor';
require __DIR__ . '/includes/header.php';
?>

<h1 class="page-title"><?= $isEdit ? 'Edit investor' : 'Add investor' ?></h1>

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
      <label for="investor_code">Investor code *</label>
      <input type="text" id="investor_code" name="investor_code" value="<?= htmlspecialchars($investor_code) ?>" required>
    </div>
    <div class="form-group">
      <label for="join_date">Join date *</label>
      <input type="date" id="join_date" name="join_date" value="<?= htmlspecialchars($join_date) ?>" required>
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
    <div class="form-group">
      <label for="notes">Notes</label>
      <textarea id="notes" name="notes" rows="3"><?= htmlspecialchars($notes) ?></textarea>
    </div>
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
    <div class="form-group">
      <button type="submit" class="btn"><?= $isEdit ? 'Update' : 'Create' ?></button>
      <a href="<?= $base ?>/admin/investors.php" class="btn btn-outline" style="margin-left:0.5rem;">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
