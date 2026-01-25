<?php
/**
 * Bright of Amana – Investment History
 */
require __DIR__ . '/includes/init.php';

$investorId = (int) $investor['id'];

$stmt = $pdo->prepare("
    SELECT id, amount, month, year, payment_mode, transaction_ref, payment_proof_path, status, submitted_at, processed_at, admin_remark
    FROM investments
    WHERE investor_id = ?
    ORDER BY submitted_at DESC
");
$stmt->execute([$investorId]);
$investments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page = 'history';
$title = 'Investment History';
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <div>
    <h1 class="page-title">Investment History</h1>
    <p class="page-subtitle">All your investment submissions</p>
  </div>
  <a href="<?= $base ?>/investors/submit.php" class="btn btn-outline">Submit Investment</a>
</div>

<div class="card">
  <?php if (empty($investments)): ?>
    <p class="empty-state">No investments yet. <a href="<?= $base ?>/investors/submit.php">Submit your first investment</a>.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>Month / Year</th>
            <th>Amount</th>
            <th>Payment Mode</th>
            <th>Transaction Ref</th>
            <th>Status</th>
            <th>Submitted</th>
            <th>Processed</th>
            <th>Remarks</th>
            <th>Proof</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($investments as $inv):
            $statusClass = $inv['status'] === 'approved' ? 'approved' : ($inv['status'] === 'rejected' ? 'rejected' : 'pending');
            $monthName = date('F', mktime(0, 0, 0, (int) $inv['month'], 1));
          ?>
            <tr>
              <td><strong><?= $monthName ?> <?= $inv['year'] ?></strong></td>
              <td>₹ <?= number_format((float) $inv['amount'], 2) ?></td>
              <td><?= htmlspecialchars($inv['payment_mode'] ?? '—') ?></td>
              <td><?= htmlspecialchars($inv['transaction_ref'] ?? '—') ?></td>
              <td><span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($inv['status']) ?></span></td>
              <td><?= date('M j, Y H:i', strtotime($inv['submitted_at'])) ?></td>
              <td><?= $inv['processed_at'] ? date('M j, Y H:i', strtotime($inv['processed_at'])) : '—' ?></td>
              <td><?= !empty($inv['admin_remark']) ? htmlspecialchars($inv['admin_remark']) : '—' ?></td>
              <td>
                <?php if (!empty($inv['payment_proof_path'])):
                  $proofUrl = $base . '/' . htmlspecialchars($inv['payment_proof_path']);
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
