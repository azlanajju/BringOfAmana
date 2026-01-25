<?php
/**
 * Bright of Amana – Investor Dashboard
 */
require __DIR__ . '/includes/init.php';

$investorId = (int) $investor['id'];

// Total invested (approved)
$stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM investments WHERE investor_id = ? AND status = 'approved'");
$stmt->execute([$investorId]);
$totalInvested = (float) $stmt->fetchColumn();

// This year total
$currentYear = (int) date('Y');
$stmt = $pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM investments WHERE investor_id = ? AND status = 'approved' AND year = ?");
$stmt->execute([$investorId, $currentYear]);
$thisYearTotal = (float) $stmt->fetchColumn();

// Pending count
$stmt = $pdo->prepare("SELECT COUNT(*) FROM investments WHERE investor_id = ? AND status = 'pending'");
$stmt->execute([$investorId]);
$pendingCount = (int) $stmt->fetchColumn();

// Last payment status
$stmt = $pdo->prepare("
    SELECT status, month, year, amount, submitted_at
    FROM investments
    WHERE investor_id = ?
    ORDER BY submitted_at DESC
    LIMIT 1
");
$stmt->execute([$investorId]);
$lastPayment = $stmt->fetch(PDO::FETCH_ASSOC);

// Recent investments
$stmt = $pdo->prepare("
    SELECT id, amount, month, year, payment_mode, status, submitted_at, admin_remark
    FROM investments
    WHERE investor_id = ?
    ORDER BY submitted_at DESC
    LIMIT 10
");
$stmt->execute([$investorId]);
$recent = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page = 'dashboard';
$title = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Overview of your investments</p>
  </div>
  <a href="<?= $base ?>/investors/submit.php" class="btn">Submit New Investment</a>
</div>

<div class="stats">
  <div class="stat-card highlight">
    <div class="label">Total Invested</div>
    <div class="value">₹ <?= number_format($totalInvested, 2) ?></div>
  </div>
  <div class="stat-card">
    <div class="label">This Year (<?= $currentYear ?>)</div>
    <div class="value">₹ <?= number_format($thisYearTotal, 2) ?></div>
  </div>
  <div class="stat-card">
    <div class="label">Pending Approvals</div>
    <div class="value"><?= $pendingCount ?></div>
  </div>
  <div class="stat-card">
    <div class="label">Last Payment</div>
    <div class="value" style="font-size:1rem;">
      <?php if ($lastPayment):
          $statusClass = $lastPayment['status'] === 'approved' ? 'approved' : ($lastPayment['status'] === 'rejected' ? 'rejected' : 'pending');
          $monthName = date('F', mktime(0, 0, 0, (int) $lastPayment['month'], 1));
      ?>
        <span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($lastPayment['status']) ?></span>
        <div class="stat-trend"><?= $monthName ?> <?= $lastPayment['year'] ?></div>
      <?php else: ?>
        <span style="color:var(--text-muted);font-weight:500;">No payments yet</span>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="card">
  <div class="toolbar">
    <h2>Recent Investments</h2>
    <a href="<?= $base ?>/investors/submit.php" class="btn btn-sm btn-outline">Submit Investment</a>
  </div>
  <?php if (empty($recent)): ?>
    <p class="empty-state">No investments yet. <a href="<?= $base ?>/investors/submit.php">Submit your first investment</a>.</p>
  <?php else: ?>
    <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Month / Year</th>
          <th>Amount</th>
          <th>Payment Mode</th>
          <th>Status</th>
          <th>Submitted</th>
          <th>Remarks</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recent as $r):
          $statusClass = $r['status'] === 'approved' ? 'approved' : ($r['status'] === 'rejected' ? 'rejected' : 'pending');
          $monthName = date('F', mktime(0, 0, 0, (int) $r['month'], 1));
        ?>
          <tr>
            <td><?= $monthName ?> <?= $r['year'] ?></td>
            <td>₹ <?= number_format((float) $r['amount'], 2) ?></td>
            <td><?= htmlspecialchars($r['payment_mode'] ?? '—') ?></td>
            <td><span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($r['status']) ?></span></td>
            <td><?= date('M j, Y H:i', strtotime($r['submitted_at'])) ?></td>
            <td><?= !empty($r['admin_remark']) ? htmlspecialchars($r['admin_remark']) : '—' ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
    <p style="margin-top:1.25rem;"><a href="<?= $base ?>/investors/history.php" class="btn btn-sm btn-outline">View all investments →</a></p>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
