<?php
/**
 * Bright of Amana – Investors list
 */
require __DIR__ . '/includes/init.php';

$stmt = $pdo->query("
    SELECT i.id, i.investor_code, i.join_date, i.notes,
           u.name, u.email, u.phone, u.status
    FROM investors i
    JOIN users u ON u.id = i.user_id
    ORDER BY i.investor_code
");
$investors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page = 'investors';
$title = 'Investors';
require __DIR__ . '/includes/header.php';
?>

<h1 class="page-title">Investors</h1>

<div class="toolbar">
  <div></div>
  <a href="<?= $base ?>/admin/investor-form.php" class="btn">Add investor</a>
</div>

<div class="card">
  <?php if (empty($investors)): ?>
    <p style="color:#718096;">No investors yet. <a href="<?= $base ?>/admin/investor-form.php">Add one</a>.</p>
  <?php else: ?>
    <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Code</th>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Join date</th>
          <th>Status</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($investors as $inv): ?>
          <tr>
            <td><?= htmlspecialchars($inv['investor_code']) ?></td>
            <td><?= htmlspecialchars($inv['name']) ?></td>
            <td><?= htmlspecialchars($inv['email']) ?></td>
            <td><?= htmlspecialchars($inv['phone'] ?? '—') ?></td>
            <td><?= date('M j, Y', strtotime($inv['join_date'])) ?></td>
            <td><span class="badge badge-<?= $inv['status'] === 'active' ? 'active' : 'inactive' ?>"><?= htmlspecialchars($inv['status']) ?></span></td>
            <td><a href="<?= $base ?>/admin/investor-form.php?id=<?= (int) $inv['id'] ?>" class="btn btn-sm btn-outline">Edit</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
