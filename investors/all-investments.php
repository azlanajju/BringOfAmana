<?php
/**
 * Bright of Amana – All Investments View
 * Shows all investments with total amounts, investor details, and dates
 */
require __DIR__ . '/includes/init.php';

// Get filter parameters
$month = isset($_GET['month']) ? (int) $_GET['month'] : null;
$year = isset($_GET['year']) ? (int) $_GET['year'] : null;
$statusFilter = $_GET['status'] ?? '';

// Get distinct years from investments
$stmt = $pdo->query("SELECT DISTINCT year FROM investments ORDER BY year DESC");
$availableYears = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Build query to get all investments with investor details
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
$investorCount = [];

foreach ($allInvestments as $inv) {
    $totalAmount += (float) $inv['amount'];
    if ($inv['status'] === 'approved') {
        $totalApproved += (float) $inv['amount'];
    } elseif ($inv['status'] === 'pending') {
        $totalPending += (float) $inv['amount'];
    } elseif ($inv['status'] === 'rejected') {
        $totalRejected += (float) $inv['amount'];
    }
    
    // Count unique investors
    if (!isset($investorCount[$inv['investor_id']])) {
        $investorCount[$inv['investor_id']] = [
            'name' => $inv['investor_name'],
            'code' => $inv['investor_code'],
            'count' => 0,
            'total' => 0
        ];
    }
    $investorCount[$inv['investor_id']]['count']++;
    $investorCount[$inv['investor_id']]['total'] += (float) $inv['amount'];
}

$uniqueInvestorCount = count($investorCount);

$page = 'all-investments';
$title = 'All Investments';
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">All Investments</h1>
    <p class="page-subtitle">Complete overview of all investment submissions</p>
  </div>
  <a href="submit.php" class="btn btn-outline">Submit Investment</a>
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
    <div class="value"><?= $uniqueInvestorCount ?></div>
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

<!-- Investments Table -->
<div class="card">
  <div class="toolbar">
    <h2>Investment Details</h2>
    <div style="font-size: 0.875rem; color: var(--text-muted);">
      Showing <?= count($allInvestments) ?> investment<?= count($allInvestments) !== 1 ? 's' : '' ?>
    </div>
  </div>
  <?php if (empty($allInvestments)): ?>
    <p class="empty-state">No investments found. <a href="submit.php">Submit your first investment</a>.</p>
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
            <th>Proof</th>
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
              <td><?= htmlspecialchars($inv['investor_code']) ?></td>
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
              <td>
                <?php if (!empty($inv['payment_proof_path'])):
                  $proofUrl = '../' . htmlspecialchars($inv['payment_proof_path']);
                  $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $inv['payment_proof_path']);
                  $isPdf = preg_match('/\.pdf$/i', $inv['payment_proof_path']);
                ?>
                  <button type="button" class="btn btn-sm btn-outline" onclick="openProofModal('<?= $proofUrl ?>', <?= $isImage ? 'true' : 'false' ?>, <?= $isPdf ? 'true' : 'false' ?>)">View</button>
                <?php else: ?>
                  —
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<!-- Investor Summary -->
<div class="card">
  <h2>Investor Summary</h2>
  <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Investor Name</th>
          <th>Investor Code</th>
          <th>Total Investments</th>
          <th>Total Amount</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        // Sort investors by total amount descending
        uasort($investorCount, function($a, $b) {
          return $b['total'] <=> $a['total'];
        });
        foreach ($investorCount as $invData): 
        ?>
          <tr>
            <td><strong><?= htmlspecialchars($invData['name']) ?></strong></td>
            <td><?= htmlspecialchars($invData['code']) ?></td>
            <td><?= $invData['count'] ?></td>
            <td><strong>₹ <?= number_format($invData['total'], 2) ?></strong></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Proof Modal -->
<div id="proofModal" class="modal" onclick="if(event.target === this) closeProofModal()">
  <div class="modal-content" style="max-width:800px;">
    <div class="modal-header">
      <h3 class="modal-title">Payment Proof</h3>
      <button type="button" class="modal-close" onclick="closeProofModal()" aria-label="Close">&times;</button>
    </div>
    <div class="modal-body" id="proofModalBody"></div>
  </div>
</div>

<script>
function openProofModal(url, isImage, isPdf) {
  var modal = document.getElementById('proofModal');
  var body = document.getElementById('proofModalBody');

  if (isImage) {
    body.innerHTML = '<img src="' + url + '" alt="Payment Proof" class="modal-image" onclick="event.stopPropagation()">';
  } else if (isPdf) {
    body.innerHTML = '<iframe src="' + url + '" class="modal-pdf" onclick="event.stopPropagation()"></iframe>';
  } else {
    body.innerHTML = '<p>Preview not available. <a href="' + url + '" target="_blank">Download file</a></p>';
  }

  modal.classList.add('active');
  document.body.style.overflow = 'hidden';
}

function closeProofModal() {
  var modal = document.getElementById('proofModal');
  modal.classList.remove('active');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') closeProofModal();
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
