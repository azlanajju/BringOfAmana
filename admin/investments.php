<?php
/**
 * Bright of Amana – Investments list with filters
 */
require __DIR__ . '/includes/init.php';

$month = isset($_GET['month']) ? (int) $_GET['month'] : null;
$year = isset($_GET['year']) ? (int) $_GET['year'] : null;
$investorId = isset($_GET['investor']) ? (int) $_GET['investor'] : null;
$statusFilter = $_GET['status'] ?? '';

$sql = "
    SELECT i.id, i.amount, i.month, i.year, i.payment_mode, i.transaction_ref, i.status, i.submitted_at, i.processed_at,
           inv.investor_code, inv.id AS investor_id,
           u.name AS investor_name
    FROM investments i
    JOIN investors inv ON inv.id = i.investor_id
    JOIN users u ON u.id = inv.user_id
    WHERE 1=1
";
$params = [];

if ($month >= 1 && $month <= 12) {
    $sql .= " AND i.month = ?";
    $params[] = $month;
}
if ($year > 0) {
    $sql .= " AND i.year = ?";
    $params[] = $year;
}
if ($investorId > 0) {
    $sql .= " AND i.investor_id = ?";
    $params[] = $investorId;
}
if (in_array($statusFilter, ['pending', 'approved', 'rejected'], true)) {
    $sql .= " AND i.status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY i.submitted_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$investments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT id, investor_code FROM investors ORDER BY investor_code");
$investorsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page = 'investments';
$title = 'Investments';
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Investments</h1>
    <p class="page-subtitle">View and manage all investment submissions from investors.</p>
  </div>
  <a href="<?= $base ?>/admin/investment-add.php" class="btn">+ Add Investment</a>
</div>

<div class="card">
  <div class="toolbar">
    <h2 style="margin:0; font-size:1rem; font-weight:600; color:var(--text);">Filters</h2>
  </div>
  <form class="filters" method="get" action="" style="margin-top:0.5rem;">
    <select name="month">
      <option value="">All Months</option>
      <?php for ($m = 1; $m <= 12; $m++):
          $mn = date('F', mktime(0, 0, 0, $m, 1));
          $sel = $month === $m ? ' selected' : '';
      ?>
        <option value="<?= $m ?>"<?= $sel ?>><?= $mn ?></option>
      <?php endfor; ?>
    </select>
    <select name="year">
      <option value="">All Years</option>
      <?php
      $currentYear = (int) date('Y');
      for ($y = $currentYear; $y >= $currentYear - 5; $y--):
          $sel = $year === $y ? ' selected' : '';
      ?>
        <option value="<?= $y ?>"<?= $sel ?>><?= $y ?></option>
      <?php endfor; ?>
    </select>
    <select name="investor">
      <option value="">All Investors</option>
      <?php foreach ($investorsList as $inv): ?>
        <option value="<?= (int) $inv['id'] ?>"<?= $investorId === (int) $inv['id'] ? ' selected' : '' ?>><?= htmlspecialchars($inv['investor_code']) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="status">
      <option value="">All Statuses</option>
      <option value="pending"<?= $statusFilter === 'pending' ? ' selected' : '' ?>>Pending</option>
      <option value="approved"<?= $statusFilter === 'approved' ? ' selected' : '' ?>>Approved</option>
      <option value="rejected"<?= $statusFilter === 'rejected' ? ' selected' : '' ?>>Rejected</option>
    </select>
    <button type="submit" class="btn btn-sm">Apply Filters</button>
    <?php if ($month || $year || $investorId || $statusFilter): ?>
      <a href="<?= $base ?>/admin/investments.php" class="btn btn-sm btn-outline">Clear</a>
    <?php endif; ?>
  </form>
</div>

<div class="card">
  <div class="toolbar" style="margin-bottom:1rem;">
    <h2 style="margin:0; font-size:1rem; font-weight:600; color:var(--text);">
      Investment Records
      <?php if (!empty($investments)): ?>
        <span style="font-weight:400; color:var(--text-muted); font-size:0.875rem; margin-left:0.5rem;">
          (<?= count($investments) ?> <?= count($investments) === 1 ? 'record' : 'records' ?>)
        </span>
      <?php endif; ?>
    </h2>
  </div>
  <?php if (empty($investments)): ?>
    <div style="text-align:center; padding:3rem 1rem; color:var(--text-muted);">
      <p style="margin:0; font-size:1rem;">No investments match your filters.</p>
      <p style="margin:0.5rem 0 0; font-size:0.875rem;">Try adjusting your filter criteria or <a href="<?= $base ?>/admin/investment-add.php" style="color:var(--green); text-decoration:none;">add a new investment</a>.</p>
    </div>
  <?php else: ?>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Investor</th>
            <th>Code</th>
            <th>Amount</th>
            <th>Month / Year</th>
            <th>Payment Mode</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($investments as $r):
            $statusClass = $r['status'] === 'approved' ? 'approved' : ($r['status'] === 'rejected' ? 'rejected' : 'pending');
            $monthName = date('F', mktime(0, 0, 0, (int) $r['month'], 1));
          ?>
            <tr>
              <td style="font-weight:500;"><?= htmlspecialchars($r['investor_name']) ?></td>
              <td><code style="background:#f7fafc; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.85rem; color:var(--text);"><?= htmlspecialchars($r['investor_code']) ?></code></td>
              <td style="font-weight:600; color:var(--text);"><?= number_format((float) $r['amount'], 2) ?></td>
              <td><?= $monthName ?> <?= $r['year'] ?></td>
              <td><?= htmlspecialchars($r['payment_mode'] ?? '—') ?></td>
              <td><span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($r['status']) ?></span></td>
              <td style="color:var(--text-muted); font-size:0.875rem;"><?= date('M j, Y H:i', strtotime($r['submitted_at'])) ?></td>
              <td><a href="<?= $base ?>/admin/investment-view.php?id=<?= (int) $r['id'] ?>" class="btn btn-sm btn-outline">View</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
