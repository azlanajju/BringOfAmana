<?php
/**
 * Bright of Amana – View investment, approve / reject
 */
require __DIR__ . '/includes/init.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$id) {
    header('Location: ' . $base . '/admin/investments.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT i.*, inv.investor_code, inv.id AS investor_id,
           u.name AS investor_name, u.email AS investor_email, u.phone AS investor_phone
    FROM investments i
    JOIN investors inv ON inv.id = i.investor_id
    JOIN users u ON u.id = inv.user_id
    WHERE i.id = ?
");
$stmt->execute([$id]);
$inv = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$inv) {
    header('Location: ' . $base . '/admin/investments.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $remark = trim($_POST['admin_remark'] ?? '');

    if (!in_array($action, ['approved', 'rejected'], true)) {
        $error = 'Invalid action.';
    } elseif ($inv['status'] !== 'pending') {
        $error = 'This investment has already been processed.';
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("
                UPDATE investments SET status = ?, admin_id = ?, admin_remark = ?, processed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$action, $_SESSION['user_id'], $remark ?: null, $id]);

            $logStmt = $pdo->prepare("
                INSERT INTO admin_actions_log (admin_id, investment_id, action, remark, ip_address, user_agent)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $logStmt->execute([
                $_SESSION['user_id'],
                $id,
                $action,
                $remark ?: null,
                $_SERVER['REMOTE_ADDR'] ?? null,
                substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            ]);
            $pdo->commit();
            header('Location: ' . $base . '/admin/investment-view.php?id=' . $id . '&done=1');
            exit;
        } catch (PDOException $e) {
            $pdo->rollBack();
            $error = 'Could not update. Please try again.';
        }
    }
}

if (!empty($_GET['done'])) {
    $success = 'Investment has been updated.';
}

$monthName = date('F', mktime(0, 0, 0, (int) $inv['month'], 1));
$statusClass = $inv['status'] === 'approved' ? 'approved' : ($inv['status'] === 'rejected' ? 'rejected' : 'pending');

$page = 'investments';
$title = 'Investment #' . $id;
require __DIR__ . '/includes/header.php';
?>

<h1 class="page-title">Investment #<?= $id ?></h1>

<?php if ($error): ?>
  <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if ($success): ?>
  <div class="success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>

<div class="card">
  <h2>Details</h2>
  <table>
    <tr><th style="width:180px;">Investor</th><td><?= htmlspecialchars($inv['investor_name']) ?></td></tr>
    <tr><th>Investor code</th><td><?= htmlspecialchars($inv['investor_code']) ?></td></tr>
    <tr><th>Email</th><td><?= htmlspecialchars($inv['investor_email']) ?></td></tr>
    <tr><th>Phone</th><td><?= htmlspecialchars($inv['investor_phone'] ?? '—') ?></td></tr>
    <tr><th>Amount</th><td><strong><?= number_format((float) $inv['amount'], 2) ?></strong></td></tr>
    <tr><th>Month / Year</th><td><?= $monthName ?> <?= $inv['year'] ?></td></tr>
    <tr><th>Payment mode</th><td><?= htmlspecialchars($inv['payment_mode'] ?? '—') ?></td></tr>
    <tr><th>Transaction reference</th><td><?= htmlspecialchars($inv['transaction_ref'] ?? '—') ?></td></tr>
    <tr><th>Payment proof</th>
        <td>
          <?php if (!empty($inv['payment_proof_path'])): 
            $proofUrl = $base . '/' . htmlspecialchars($inv['payment_proof_path']);
            $isImage = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $inv['payment_proof_path']);
            $isPdf = preg_match('/\.pdf$/i', $inv['payment_proof_path']);
          ?>
            <button type="button" class="btn btn-sm btn-outline" onclick="openProofModal('<?= $proofUrl ?>', <?= $isImage ? 'true' : 'false' ?>, <?= $isPdf ? 'true' : 'false' ?>)">View Proof</button>
          <?php else: ?>
            <span style="color:var(--text-muted);">No proof uploaded</span>
          <?php endif; ?>
        </td>
    </tr>
    <tr><th>Status</th><td><span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($inv['status']) ?></span></td></tr>
    <tr><th>Submitted</th><td><?= date('M j, Y H:i', strtotime($inv['submitted_at'])) ?></td></tr>
    <?php if ($inv['processed_at']): ?>
      <tr><th>Processed</th><td><?= date('M j, Y H:i', strtotime($inv['processed_at'])) ?></td></tr>
    <?php endif; ?>
    <?php if (!empty($inv['admin_remark'])): ?>
      <tr><th>Admin remark</th><td><?= nl2br(htmlspecialchars($inv['admin_remark'])) ?></td></tr>
    <?php endif; ?>
  </table>
</div>

<?php if ($inv['status'] === 'pending'): ?>
<div class="card">
  <h2>Approve or reject</h2>
  <form method="post" action="">
    <div class="form-group">
      <label for="admin_remark">Remark (optional)</label>
      <textarea id="admin_remark" name="admin_remark" rows="3" placeholder="e.g. Payment verified from bank statement"><?= htmlspecialchars($_POST['admin_remark'] ?? '') ?></textarea>
    </div>
    <div class="form-group form-actions">
      <button type="submit" name="action" value="approved" class="btn btn-success">Approve</button>
      <button type="submit" name="action" value="rejected" class="btn btn-danger">Reject</button>
      <a href="<?= $base ?>/admin/investments.php" class="btn btn-outline">Back to list</a>
    </div>
  </form>
</div>
<?php else: ?>
  <p><a href="<?= $base ?>/admin/investments.php" class="btn btn-outline">← Back to list</a></p>
<?php endif; ?>

<!-- Proof Modal -->
<div id="proofModal" class="modal" onclick="if(event.target === this) closeProofModal()">
  <div class="modal-content" style="max-width:800px;">
    <div class="modal-header">
      <h3 class="modal-title">Payment Proof</h3>
      <button type="button" class="modal-close" onclick="closeProofModal()" aria-label="Close">&times;</button>
    </div>
    <div class="modal-body" id="proofModalBody">
      <!-- Content will be inserted here -->
    </div>
  </div>
</div>

<script>
function openProofModal(url, isImage, isPdf) {
  const modal = document.getElementById('proofModal');
  const body = document.getElementById('proofModalBody');
  
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
  const modal = document.getElementById('proofModal');
  modal.classList.remove('active');
  document.body.style.overflow = '';
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    closeProofModal();
  }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
