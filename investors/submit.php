<?php
/**
 * Bright of Amana – Submit Investment
 */
require __DIR__ . '/includes/init.php';

$error = '';
$success = '';

$month = (int) date('n');
$year = (int) date('Y');
$amount = '';
$payment_mode = '';
$transaction_ref = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $month = (int) ($_POST['month'] ?? 0);
    $year = (int) ($_POST['year'] ?? 0);
    $amount = trim($_POST['amount'] ?? '');
    $payment_mode = trim($_POST['payment_mode'] ?? '');
    $transaction_ref = trim($_POST['transaction_ref'] ?? '');
    $proofFile = $_FILES['payment_proof'] ?? null;

    if (!$month || $month < 1 || $month > 12 || !$year || $year < 2000 || $year > 2100 || !$amount) {
        $error = 'Month, year and amount are required.';
    } elseif (!is_numeric($amount) || (float) $amount <= 0) {
        $error = 'Please enter a valid amount.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM investments WHERE investor_id = ? AND month = ? AND year = ?");
            $stmt->execute([(int) $investor['id'], $month, $year]);
            if ($stmt->fetch()) {
                $error = 'An investment for ' . date('F', mktime(0, 0, 0, $month, 1)) . ' ' . $year . ' already exists.';
            } else {
                $proofPath = null;

                if ($proofFile && $proofFile['error'] === UPLOAD_ERR_OK) {
                    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
                    $maxSize = 5 * 1024 * 1024;

                    if (!in_array($proofFile['type'], $allowed)) {
                        $error = 'Invalid file type. Allowed: JPG, PNG, GIF, WebP, PDF.';
                    } elseif ($proofFile['size'] > $maxSize) {
                        $error = 'File size must be less than 5MB.';
                    } else {
                        $ext = pathinfo($proofFile['name'], PATHINFO_EXTENSION);
                        $filename = 'proof_' . $investor['id'] . '_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '_' . time() . '.' . $ext;
                        $uploadDir = dirname(__DIR__) . '/uploads/proofs/';

                        if (!is_dir($uploadDir)) {
                            mkdir($uploadDir, 0755, true);
                        }

                        $targetPath = $uploadDir . $filename;
                        if (move_uploaded_file($proofFile['tmp_name'], $targetPath)) {
                            $proofPath = 'uploads/proofs/' . $filename;
                        } else {
                            $error = 'Failed to upload file. Please try again.';
                        }
                    }
                }

                if (!$error) {
                    $pdo->prepare("
                        INSERT INTO investments (investor_id, month, year, amount, payment_mode, transaction_ref, payment_proof_path, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')
                    ")->execute([
                        (int) $investor['id'],
                        $month,
                        $year,
                        (float) $amount,
                        $payment_mode ?: null,
                        $transaction_ref ?: null,
                        $proofPath
                    ]);
                    $success = 'Investment submitted successfully! It will be reviewed by admin.';
                    $amount = $payment_mode = $transaction_ref = '';
                    $month = (int) date('n');
                    $year = (int) date('Y');
                }
            }
        } catch (PDOException $e) {
            $error = 'Could not submit investment. Please try again.';
        }
    }
}

$page = 'submit';
$title = 'Submit Investment';
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Submit Investment</h1>
    <p class="page-subtitle">Add a new monthly investment</p>
  </div>
  <a href="./" class="btn btn-outline">← Dashboard</a>
</div>

<?php if ($error): ?>
  <div class="error" role="alert"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card">
  <h2>Investment details</h2>
  <form method="post" action="" enctype="multipart/form-data">
    <div class="form-group">
      <label for="month">Month *</label>
      <select id="month" name="month" required>
        <?php for ($m = 1; $m <= 12; $m++):
            $mn = date('F', mktime(0, 0, 0, $m, 1));
            $sel = $month === $m ? ' selected' : '';
        ?>
          <option value="<?= $m ?>"<?= $sel ?>><?= $mn ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="year">Year *</label>
      <input type="number" id="year" name="year" value="<?= $year ?>" min="2000" max="2100" required>
    </div>
    <div class="form-group">
      <label for="amount">Amount *</label>
      <input type="number" id="amount" name="amount" value="<?= htmlspecialchars($amount) ?>" step="0.01" min="0.01" placeholder="0.00" required>
    </div>
    <div class="form-group">
      <label for="payment_mode">Payment Mode</label>
      <input type="text" id="payment_mode" name="payment_mode" value="<?= htmlspecialchars($payment_mode) ?>" placeholder="e.g. Bank transfer, UPI, Cash">
    </div>
    <div class="form-group">
      <label for="transaction_ref">Transaction Reference</label>
      <input type="text" id="transaction_ref" name="transaction_ref" value="<?= htmlspecialchars($transaction_ref) ?>" placeholder="Transaction ID or reference number">
    </div>
    <div class="form-group">
      <label for="payment_proof">Payment Proof (Optional)</label>
      <input type="file" id="payment_proof" name="payment_proof" accept="image/*,.pdf">
      <small>Max 5MB. Allowed: JPG, PNG, GIF, WebP, PDF</small>
    </div>
    <div class="form-group form-actions">
      <button type="submit" class="btn">Submit Investment</button>
      <a href="./" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
