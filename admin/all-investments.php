<?php
/**
 * Bright of Amana – All Investments with Investor Status
 * Shows who invested and who didn't for selected month/year
 */
require __DIR__ . '/includes/init.php';

// Get filter parameters
$month = isset($_GET['month']) ? (int) $_GET['month'] : null;
$year = isset($_GET['year']) ? (int) $_GET['year'] : null;
$statusFilter = $_GET['status'] ?? '';

// Get distinct years from investments
$stmt = $pdo->query("SELECT DISTINCT year FROM investments ORDER BY year DESC");
$availableYears = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get all investors
$stmt = $pdo->query("
    SELECT i.id, i.investor_code, i.join_date,
           u.id AS user_id, u.name, u.email, u.phone, u.status
    FROM investors i
    JOIN users u ON u.id = i.user_id
    WHERE u.status = 'active'
    ORDER BY i.investor_code
");
$allInvestors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build query to get investments
$sql = "
    SELECT 
        i.id, 
        i.amount, 
        i.month, 
        i.year, 
        i.payment_mode, 
        i.transaction_ref, 
        i.payment_proof_path,
        i.status, 
        i.submitted_at, 
        i.processed_at,
        i.admin_remark,
        inv.investor_code,
        inv.id AS investor_id,
        u.name AS investor_name,
        u.email AS investor_email,
        u.phone AS investor_phone,
        admin_user.name AS admin_name
    FROM investments i
    JOIN investors inv ON inv.id = i.investor_id
    JOIN users u ON u.id = inv.user_id
    LEFT JOIN users admin_user ON admin_user.id = i.admin_id
    WHERE 1=1
";
$params = [];

// Apply filters
if ($month >= 1 && $month <= 12) {
    $sql .= " AND i.month = ?";
    $params[] = $month;
}
if ($year > 0) {
    $sql .= " AND i.year = ?";
    $params[] = $year;
}
if (in_array($statusFilter, ['pending', 'approved', 'rejected'], true)) {
    $sql .= " AND i.status = ?";
    $params[] = $statusFilter;
}

$sql .= " ORDER BY i.submitted_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$allInvestments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate totals
$totalAmount = 0;
$totalApproved = 0;
$totalPending = 0;
$totalRejected = 0;

// Map investors who invested in selected period
$investedInvestorIds = [];
$investmentsByInvestor = [];

foreach ($allInvestments as $inv) {
    $totalAmount += (float) $inv['amount'];
    if ($inv['status'] === 'approved') {
        $totalApproved += (float) $inv['amount'];
    } elseif ($inv['status'] === 'pending') {
        $totalPending += (float) $inv['amount'];
    } elseif ($inv['status'] === 'rejected') {
        $totalRejected += (float) $inv['amount'];
    }
    
    $investedInvestorIds[$inv['investor_id']] = true;
    if (!isset($investmentsByInvestor[$inv['investor_id']])) {
        $investmentsByInvestor[$inv['investor_id']] = [];
    }
    $investmentsByInvestor[$inv['investor_id']][] = $inv;
}

// Separate investors into who invested and who didn't
$investedInvestors = [];
$notInvestedInvestors = [];

foreach ($allInvestors as $investor) {
    if (isset($investedInvestorIds[$investor['id']])) {
        $investedInvestors[] = $investor;
    } else {
        $notInvestedInvestors[] = $investor;
    }
}

$page = 'all-investments';
$title = 'All Investments';
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">All Investments</h1>
    <p class="page-subtitle">Complete overview with investor status tracking</p>
  </div>
  <a href="investment-add.php" class="btn">+ Add Investment</a>
</div>

<!-- Summary Statistics -->
<div class="stats">
  <div class="stat-card highlight">
    <div class="label">Total Amount</div>
    <div class="value">₹ <?= number_format($totalAmount, 2) ?></div>
    <div class="stat-trend">All investments</div>
  </div>
  <div class="stat-card">
    <div class="label">Approved</div>
    <div class="value">₹ <?= number_format($totalApproved, 2) ?></div>
    <div class="stat-trend">Verified investments</div>
  </div>
  <div class="stat-card">
    <div class="label">Pending</div>
    <div class="value">₹ <?= number_format($totalPending, 2) ?></div>
    <div class="stat-trend">Awaiting approval</div>
  </div>
  <div class="stat-card">
    <div class="label">Total Investors</div>
    <div class="value"><?= count($allInvestors) ?></div>
    <div class="stat-trend">Active investors</div>
  </div>
</div>

<!-- Filters -->
<div class="card">
  <h2>Filters</h2>
  <form method="get" action="" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
      <label for="month">Month</label>
      <select id="month" name="month">
        <option value="">All Months</option>
        <?php for ($m = 1; $m <= 12; $m++):
            $mn = date('F', mktime(0, 0, 0, $m, 1));
            $sel = $month === $m ? ' selected' : '';
        ?>
          <option value="<?= $m ?>"<?= $sel ?>><?= $mn ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
      <label for="year">Year</label>
      <select id="year" name="year">
        <option value="">All Years</option>
        <?php foreach ($availableYears as $yr): ?>
          <option value="<?= (int) $yr ?>"<?= $year === (int) $yr ? ' selected' : '' ?>><?= (int) $yr ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group" style="margin-bottom: 0; flex: 1; min-width: 150px;">
      <label for="status">Status</label>
      <select id="status" name="status">
        <option value="">All Status</option>
        <option value="pending"<?= $statusFilter === 'pending' ? ' selected' : '' ?>>Pending</option>
        <option value="approved"<?= $statusFilter === 'approved' ? ' selected' : '' ?>>Approved</option>
        <option value="rejected"<?= $statusFilter === 'rejected' ? ' selected' : '' ?>>Rejected</option>
      </select>
    </div>
    <div class="form-group" style="margin-bottom: 0;">
      <button type="submit" class="btn">Apply Filters</button>
      <a href="all-investments.php" class="btn btn-outline" style="margin-left: 0.5rem;">Clear</a>
    </div>
  </form>
</div>

<?php if ($month && $year): ?>
<!-- Investor Status for Selected Month/Year -->
<div class="card">
  <h2>
    Investor Status for <?= date('F', mktime(0, 0, 0, $month, 1)) ?> <?= $year ?>
    <span style="font-weight:400; color:var(--text-muted); font-size:0.875rem; margin-left:0.5rem;">
      (<?= count($investedInvestors) ?> invested, <?= count($notInvestedInvestors) ?> didn't invest)
    </span>
  </h2>
  
  <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1rem;">
    <!-- Investors Who Invested -->
    <div>
      <h3 style="margin:0 0 1rem; font-size:1rem; font-weight:600; color:var(--green); display:flex; align-items:center; gap:0.5rem;">
        <span style="display:inline-block; width:12px; height:12px; background:var(--green); border-radius:50%;"></span>
        Invested (<?= count($investedInvestors) ?>)
      </h3>
      <?php if (empty($investedInvestors)): ?>
        <p style="color:var(--text-muted); padding:1rem; background:#f7fafc; border-radius:var(--radius);">No investors invested this month.</p>
      <?php else: ?>
        <div style="max-height:400px; overflow-y:auto; border:1px solid #e2e8f0; border-radius:var(--radius);">
          <table style="margin:0;">
            <thead style="position:sticky; top:0; background:#fff; z-index:1;">
              <tr>
                <th style="padding:0.75rem; font-size:0.75rem;">Investor</th>
                <th style="padding:0.75rem; font-size:0.75rem;">Amount</th>
                <th style="padding:0.75rem; font-size:0.75rem;">Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($investedInvestors as $inv): 
                $invData = $investmentsByInvestor[$inv['id']][0] ?? null;
                $statusClass = $invData && $invData['status'] === 'approved' ? 'approved' : ($invData && $invData['status'] === 'rejected' ? 'rejected' : 'pending');
                $amount = $invData ? (float) $invData['amount'] : 0;
              ?>
                <tr>
                  <td style="padding:0.75rem;">
                    <strong><?= htmlspecialchars($inv['name']) ?></strong><br>
                    <small style="color:var(--text-muted);"><?= htmlspecialchars($inv['investor_code']) ?></small>
                  </td>
                  <td style="padding:0.75rem; font-weight:600;">₹ <?= number_format($amount, 2) ?></td>
                  <td style="padding:0.75rem;">
                    <?php if ($invData): ?>
                      <span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($invData['status']) ?></span>
                    <?php else: ?>
                      <span class="badge badge-pending">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
    
    <!-- Investors Who Didn't Invest -->
    <div style="background:var(--red-bg); border:2px solid var(--red); border-radius:var(--radius-lg); padding:1rem;">
      <h3 style="margin:0 0 1rem; font-size:1rem; font-weight:600; color:var(--red); display:flex; align-items:center; gap:0.5rem;">
        <span style="display:inline-block; width:12px; height:12px; background:var(--red); border-radius:50%;"></span>
        Didn't Invest (<?= count($notInvestedInvestors) ?>)
      </h3>
      <?php if (empty($notInvestedInvestors)): ?>
        <p style="color:var(--text-muted); padding:1rem; background:#fff; border-radius:var(--radius);">All investors have invested this month!</p>
      <?php else: ?>
        <div style="max-height:400px; overflow-y:auto; border:1px solid var(--red); border-radius:var(--radius); background:#fff;">
          <table style="margin:0;">
            <thead style="position:sticky; top:0; background:var(--red-bg); z-index:1; border-bottom:2px solid var(--red);">
              <tr>
                <th style="padding:0.75rem; font-size:0.75rem; color:var(--red); font-weight:600;">Investor</th>
                <th style="padding:0.75rem; font-size:0.75rem; color:var(--red); font-weight:600;">Contact</th>
                <th style="padding:0.75rem; font-size:0.75rem; color:var(--red); font-weight:600;">Join Date</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($notInvestedInvestors as $inv): ?>
                <tr style="background:#fff;">
                  <td style="padding:0.75rem;">
                    <strong style="color:var(--red);"><?= htmlspecialchars($inv['name']) ?></strong><br>
                    <small style="color:var(--text-muted);"><?= htmlspecialchars($inv['investor_code']) ?></small>
                  </td>
                  <td style="padding:0.75rem;">
                    <?php if ($inv['email']): ?>
                      <small><?= htmlspecialchars($inv['email']) ?></small><br>
                    <?php endif; ?>
                    <?php if ($inv['phone']): ?>
                      <small style="color:var(--text-muted);"><?= htmlspecialchars($inv['phone']) ?></small>
                    <?php endif; ?>
                  </td>
                  <td style="padding:0.75rem; font-size:0.875rem; color:var(--text-muted);">
                    <?= date('M j, Y', strtotime($inv['join_date'])) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php else: ?>
<div class="card">
  <p style="color:var(--text-muted); padding:1rem; background:#f7fafc; border-radius:var(--radius);">
    <strong>Note:</strong> Select a specific month and year to see which investors have invested and which haven't.
  </p>
</div>
<?php endif; ?>

<!-- All Investments Table -->
<div class="card">
  <div class="toolbar">
    <h2>Investment Details</h2>
    <div style="font-size: 0.875rem; color: var(--text-muted);">
      Showing <?= count($allInvestments) ?> investment<?= count($allInvestments) !== 1 ? 's' : '' ?>
    </div>
  </div>
  <?php if (empty($allInvestments)): ?>
    <p class="empty-state">No investments found. <a href="investment-add.php">Add a new investment</a>.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Investor</th>
            <th>Investor Code</th>
            <th>Month / Year</th>
            <th>Amount</th>
            <th>Payment Mode</th>
            <th>Transaction Ref</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Processed</th>
            <th>Processed By</th>
            <th>Remarks</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($allInvestments as $inv):
            $statusClass = $inv['status'] === 'approved' ? 'approved' : ($inv['status'] === 'rejected' ? 'rejected' : 'pending');
            $monthName = date('F', mktime(0, 0, 0, (int) $inv['month'], 1));
          ?>
            <tr>
              <td>
                <strong><?= htmlspecialchars($inv['investor_name']) ?></strong>
                <?php if ($inv['investor_email']): ?>
                  <br><small style="color: var(--text-muted);"><?= htmlspecialchars($inv['investor_email']) ?></small>
                <?php endif; ?>
                <?php if ($inv['investor_phone']): ?>
                  <br><small style="color: var(--text-muted);"><?= htmlspecialchars($inv['investor_phone']) ?></small>
                <?php endif; ?>
              </td>
              <td><code style="background:#f7fafc; padding:0.25rem 0.5rem; border-radius:4px; font-size:0.85rem;"><?= htmlspecialchars($inv['investor_code']) ?></code></td>
              <td><strong><?= $monthName ?> <?= $inv['year'] ?></strong></td>
              <td><strong>₹ <?= number_format((float) $inv['amount'], 2) ?></strong></td>
              <td><?= htmlspecialchars($inv['payment_mode'] ?? '—') ?></td>
              <td><?= htmlspecialchars($inv['transaction_ref'] ?? '—') ?></td>
              <td><span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($inv['status']) ?></span></td>
              <td><?= date('M j, Y', strtotime($inv['submitted_at'])) ?><br><small style="color: var(--text-muted);"><?= date('H:i', strtotime($inv['submitted_at'])) ?></small></td>
              <td>
                <?php if ($inv['processed_at']): ?>
                  <?= date('M j, Y', strtotime($inv['processed_at'])) ?><br><small style="color: var(--text-muted);"><?= date('H:i', strtotime($inv['processed_at'])) ?></small>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
              <td><?= $inv['admin_name'] ? htmlspecialchars($inv['admin_name']) : '—' ?></td>
              <td><?= !empty($inv['admin_remark']) ? htmlspecialchars($inv['admin_remark']) : '—' ?></td>
              <td><a href="investment-view.php?id=<?= (int) $inv['id'] ?>" class="btn btn-sm btn-outline">View</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
