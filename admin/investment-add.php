<?php
/**
 * Bright of Amana – Add investment (manual entry)
 */
require __DIR__ . '/includes/init.php';

$error = '';
$success = '';

$stmt = $pdo->query("SELECT id, investor_code FROM investors ORDER BY investor_code");
$investorsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$investor_id = '';
$month = (int) date('n');
$year = (int) date('Y');
$amount = '';
$payment_mode = '';
$transaction_ref = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $investor_id = (int) ($_POST['investor_id'] ?? 0);
    $month = (int) ($_POST['month'] ?? 0);
    $year = (int) ($_POST['year'] ?? 0);
    $amount = trim($_POST['amount'] ?? '');
    $payment_mode = trim($_POST['payment_mode'] ?? '');
    $transaction_ref = trim($_POST['transaction_ref'] ?? '');

    if (!$investor_id || $month < 1 || $month > 12 || $year < 2000 || $year > 2100 || !$amount) {
        $error = 'Investor, month, year and amount are required.';
    } elseif (!is_numeric($amount) || (float) $amount <= 0) {
        $error = 'Please enter a valid amount.';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM investments WHERE investor_id = ? AND month = ? AND year = ?");
            $stmt->execute([$investor_id, $month, $year]);
            if ($stmt->fetch()) {
                $error = 'An investment for this investor, month and year already exists.';
            } else {
                $pdo->prepare("
                    INSERT INTO investments (investor_id, month, year, amount, payment_mode, transaction_ref, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'pending')
                ")->execute([$investor_id, $month, $year, (float) $amount, $payment_mode ?: null, $transaction_ref ?: null]);
                $newId = (int) $pdo->lastInsertId();
                header('Location: ' . $base . '/admin/investment-view.php?id=' . $newId . '&done=1');
                exit;
            }
        } catch (PDOException $e) {
            $error = 'Could not create investment. Please try again.';
        }
    }
}

$page = 'investments';
$title = 'Add investment';
require __DIR__ . '/includes/header.php';
?>

<h1 class="page-title">Add investment</h1>

<?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<?php if (empty($investorsList)): ?>
  <div class="card">
    <p style="color:#718096;">No investors yet. <a href="<?= $base ?>/admin/investors.php">Add an investor</a> first.</p>
    <p><a href="<?= $base ?>/admin/investments.php" class="btn btn-outline">← Back to list</a></p>
  </div>
<?php else: ?>
<div class="card">
  <form method="post" action="">
    <div class="form-group">
      <label for="investor_id">Investor *</label>
      <select id="investor_id" name="investor_id" required>
        <option value="">Select investor</option>
        <?php foreach ($investorsList as $i): ?>
          <option value="<?= (int) $i['id'] ?>"<?= (int) $investor_id === (int) $i['id'] ? ' selected' : '' ?>><?= htmlspecialchars($i['investor_code']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
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
      <input type="number" id="amount" name="amount" value="<?= htmlspecialchars($amount) ?>" step="0.01" min="0.01" required>
    </div>
    <div class="form-group">
      <label for="payment_mode">Payment mode</label>
      <input type="text" id="payment_mode" name="payment_mode" value="<?= htmlspecialchars($payment_mode) ?>" placeholder="e.g. Bank transfer, UPI, Cash">
    </div>
    <div class="form-group">
      <label for="transaction_ref">Transaction reference</label>
      <input type="text" id="transaction_ref" name="transaction_ref" value="<?= htmlspecialchars($transaction_ref) ?>" placeholder="Optional">
    </div>
    <div class="form-group">
      <button type="submit" class="btn">Create investment</button>
      <a href="<?= $base ?>/admin/investments.php" class="btn btn-outline" style="margin-left:0.5rem;">Cancel</a>
    </div>
  </form>
</div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
