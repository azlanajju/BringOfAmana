<?php
/**
 * Bright of Amana – Admins & Users
 * Shows admins and investors in separate sections
 */
require __DIR__ . '/includes/init.php';

$stmt = $pdo->query("
    SELECT id, name, email, phone, role, status, last_login_at, created_at
    FROM users
    WHERE role IN ('super_admin', 'admin', 'staff')
    ORDER BY 
        CASE role
            WHEN 'super_admin' THEN 1
            WHEN 'admin' THEN 2
            WHEN 'staff' THEN 3
        END,
        name
");
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT u.id, u.name, u.email, u.phone, u.status, u.last_login_at, u.created_at,
           i.id AS investor_id, i.investor_code, i.join_date
    FROM users u
    LEFT JOIN investors i ON i.user_id = u.id
    WHERE u.role = 'investor'
    ORDER BY u.name
");
$investors = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page = 'users';
$title = 'Admins & Users';
require __DIR__ . '/includes/header.php';
?>

<h1 class="page-title">Admins & Users</h1>

<div class="toolbar" style="margin-bottom: 1.5rem;">
  <div></div>
  <div style="display: flex; gap: 0.5rem;">
    <a href="<?= $base ?>/create.php" class="btn">Add admin</a>
    <a href="<?= $base ?>/admin/investor-form.php" class="btn btn-outline">Add investor</a>
  </div>
</div>

<div class="card">
  <h2>Administrators</h2>
  <?php if (empty($admins)): ?>
    <p style="color:#718096;">No administrators found.</p>
  <?php else: ?>
    <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Role</th>
          <th>Status</th>
          <th>Last login</th>
          <th>Created</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($admins as $admin): ?>
          <tr>
            <td><strong><?= htmlspecialchars($admin['name']) ?></strong></td>
            <td><?= htmlspecialchars($admin['email']) ?></td>
            <td><?= htmlspecialchars($admin['phone'] ?? '—') ?></td>
            <td>
              <?php
              $roleLabels = [
                  'super_admin' => 'Super Admin',
                  'admin' => 'Admin',
                  'staff' => 'Staff'
              ];
              $roleLabel = $roleLabels[$admin['role']] ?? $admin['role'];
              ?>
              <span class="badge" style="background:#2b6cb0;color:#fff;"><?= htmlspecialchars($roleLabel) ?></span>
            </td>
            <td><span class="badge badge-<?= $admin['status'] === 'active' ? 'active' : 'inactive' ?>"><?= htmlspecialchars($admin['status']) ?></span></td>
            <td><?= $admin['last_login_at'] ? date('M j, Y H:i', strtotime($admin['last_login_at'])) : 'Never' ?></td>
            <td><?= date('M j, Y', strtotime($admin['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <?php endif; ?>
</div>

<div class="card">
  <h2>Investors</h2>
  <?php if (empty($investors)): ?>
    <p style="color:#718096;">No investors found.</p>
  <?php else: ?>
    <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Investor code</th>
          <th>Join date</th>
          <th>Status</th>
          <th>Last login</th>
          <th>Created</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($investors as $inv): ?>
          <tr>
            <td><strong><?= htmlspecialchars($inv['name']) ?></strong></td>
            <td><?= htmlspecialchars($inv['email']) ?></td>
            <td><?= htmlspecialchars($inv['phone'] ?? '—') ?></td>
            <td><?= htmlspecialchars($inv['investor_code'] ?? '—') ?></td>
            <td><?= $inv['join_date'] ? date('M j, Y', strtotime($inv['join_date'])) : '—' ?></td>
            <td><span class="badge badge-<?= $inv['status'] === 'active' ? 'active' : 'inactive' ?>"><?= htmlspecialchars($inv['status']) ?></span></td>
            <td><?= $inv['last_login_at'] ? date('M j, Y H:i', strtotime($inv['last_login_at'])) : 'Never' ?></td>
            <td><?= date('M j, Y', strtotime($inv['created_at'])) ?></td>
            <td>
              <?php if (!empty($inv['investor_id'])): ?>
                <a href="<?= $base ?>/admin/investor-form.php?id=<?= (int) $inv['investor_id'] ?>" class="btn btn-sm btn-outline">Edit</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
