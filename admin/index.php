<?php
/**
 * Bright of Amana – Admin Dashboard
 */
require __DIR__ . '/includes/init.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM investors");
$totalInvestors = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0) FROM investments
    WHERE status = 'approved' AND month = ? AND year = ?
");
$stmt->execute([(int) date('n'), (int) date('Y')]);
$receivedThisMonth = (float) $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM investments WHERE status = 'pending'");
$pendingCount = (int) $stmt->fetchColumn();

$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(amount), 0) FROM investments
    WHERE status = 'approved' AND month = ? AND year = ?
");
$stmt->execute([(int) date('n'), (int) date('Y')]);
$approvedThisMonth = (float) $stmt->fetchColumn();

// Last 6 months data for chart
$monthlyData = [];
$currentYear = (int) date('Y');
$currentMonth = (int) date('n');
for ($i = 5; $i >= 0; $i--) {
    $month = $currentMonth - $i;
    $year = $currentYear;
    if ($month <= 0) {
        $month += 12;
        $year--;
    }
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount), 0) FROM investments
        WHERE status = 'approved' AND month = ? AND year = ?
    ");
    $stmt->execute([$month, $year]);
    $monthlyData[] = [
        'month' => date('M', mktime(0, 0, 0, $month, 1)),
        'amount' => (float) $stmt->fetchColumn()
    ];
}

// Status distribution
$stmt = $pdo->query("
    SELECT status, COUNT(*) as count, COALESCE(SUM(amount), 0) as total
    FROM investments
    GROUP BY status
");
$statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);
$statusCounts = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
$statusTotals = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
foreach ($statusData as $row) {
    $statusCounts[$row['status']] = (int) $row['count'];
    $statusTotals[$row['status']] = (float) $row['total'];
}

// New investors per month (last 6 months)
$investorMonthlyData = [];
for ($i = 5; $i >= 0; $i--) {
    $month = $currentMonth - $i;
    $year = $currentYear;
    if ($month <= 0) {
        $month += 12;
        $year--;
    }
    $stmt = $pdo->prepare("
        SELECT COUNT(*) FROM investors
        WHERE MONTH(join_date) = ? AND YEAR(join_date) = ?
    ");
    $stmt->execute([$month, $year]);
    $investorMonthlyData[] = [
        'month' => date('M', mktime(0, 0, 0, $month, 1)),
        'count' => (int) $stmt->fetchColumn()
    ];
}

// Top investors by total approved amount
$stmt = $pdo->query("
    SELECT inv.investor_code, u.name,
           COALESCE(SUM(CASE WHEN i.status = 'approved' THEN i.amount ELSE 0 END), 0) AS total_approved
    FROM investors inv
    JOIN users u ON u.id = inv.user_id
    LEFT JOIN investments i ON i.investor_id = inv.id
    GROUP BY inv.id, inv.investor_code, u.name
    ORDER BY total_approved DESC
    LIMIT 8
");
$topInvestorsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT i.id, i.amount, i.month, i.year, i.status, i.submitted_at,
           inv.investor_code, u.name
    FROM investments i
    JOIN investors inv ON inv.id = i.investor_id
    JOIN users u ON u.id = inv.user_id
    ORDER BY i.submitted_at DESC
    LIMIT 10
");
$recent = $stmt->fetchAll(PDO::FETCH_ASSOC);

$page = 'dashboard';
$title = 'Dashboard';
require __DIR__ . '/includes/header.php';
?>

<div class="page-header">
  <h1 class="page-title">Dashboard</h1>
  <p class="page-subtitle">Monitor investors, investments, and approvals at a glance.</p>
</div>

<div class="stats">
  <div class="stat-card highlight">
    <div class="label">Total Investors</div>
    <div class="value"><?= $totalInvestors ?></div>
    <div class="stat-trend">Active investors</div>
    <div class="stat-icon">👥</div>
  </div>
  <div class="stat-card">
    <div class="label">Received This Month</div>
    <div class="value"><?= number_format($receivedThisMonth, 2) ?></div>
    <div class="stat-trend">Approved <?= date('F Y') ?></div>
    <div class="stat-icon">💰</div>
  </div>
  <div class="stat-card">
    <div class="label">Pending Approvals</div>
    <div class="value"><?= $pendingCount ?></div>
    <div class="stat-trend">Awaiting review</div>
    <div class="stat-icon">⏳</div>
  </div>
  <div class="stat-card">
    <div class="label">Approved This Month</div>
    <div class="value"><?= number_format($approvedThisMonth, 2) ?></div>
    <div class="stat-trend"><?= date('F Y') ?></div>
    <div class="stat-icon">✓</div>
  </div>
</div>

<div class="charts-grid">
  <div class="card">
    <h2>Monthly Investment Trends</h2>
    <div class="chart-container">
      <canvas id="monthlyChart"></canvas>
    </div>
  </div>
  <div class="card">
    <h2>Investment Status Distribution</h2>
    <div class="chart-container">
      <canvas id="statusChart"></canvas>
    </div>
  </div>
  <div class="card">
    <h2>New Investors per Month</h2>
    <div class="chart-container">
      <canvas id="investorsMonthlyChart"></canvas>
    </div>
  </div>
  <div class="card">
    <h2>Top Investors by Total Invested</h2>
    <div class="chart-container chart-container--tall">
      <canvas id="topInvestorsChart"></canvas>
    </div>
  </div>
</div>

<div class="card">
  <div class="toolbar">
    <h2 style="margin:0;">Recent Investments</h2>
    <a href="investments.php" class="btn">View All</a>
  </div>
  <?php if (empty($recent)): ?>
    <p style="color:var(--text-muted);">No investments yet.</p>
  <?php else: ?>
    <div class="table-responsive">
    <table>
      <thead>
        <tr>
          <th>Investor</th>
          <th>Code</th>
          <th>Amount</th>
          <th>Month / Year</th>
          <th>Status</th>
          <th>Submitted</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($recent as $r):
          $statusClass = $r['status'] === 'approved' ? 'approved' : ($r['status'] === 'rejected' ? 'rejected' : 'pending');
          $monthName = date('F', mktime(0, 0, 0, (int) $r['month'], 1));
        ?>
          <tr>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['investor_code']) ?></td>
            <td><?= number_format((float) $r['amount'], 2) ?></td>
            <td><?= $monthName ?> <?= $r['year'] ?></td>
            <td><span class="badge badge-<?= $statusClass ?>"><?= htmlspecialchars($r['status']) ?></span></td>
            <td><?= date('M j, Y H:i', strtotime($r['submitted_at'])) ?></td>
            <td><a href="investment-view.php?id=<?= (int) $r['id'] ?>" class="btn btn-sm btn-outline">View</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    </div>
  <?php endif; ?>
</div>

<script>
// Monthly Investment Trends Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($monthlyData, 'month')) ?>,
    datasets: [{
      label: 'Approved Amount',
      data: <?= json_encode(array_column($monthlyData, 'amount')) ?>,
      backgroundColor: '#268e45',
      borderRadius: 8,
      borderSkipped: false,
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          callback: function(value) {
            return value.toLocaleString();
          }
        }
      }
    }
  }
});

// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
  type: 'doughnut',
  data: {
    labels: ['Approved', 'Pending', 'Rejected'],
    datasets: [{
      data: [<?= $statusCounts['approved'] ?>, <?= $statusCounts['pending'] ?>, <?= $statusCounts['rejected'] ?>],
      backgroundColor: ['#268e45', '#ffb300', '#ff4444'],
      borderWidth: 0
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: {
        position: 'bottom'
      }
    }
  }
});

// New Investors per Month
const investorsMonthlyCtx = document.getElementById('investorsMonthlyChart').getContext('2d');
new Chart(investorsMonthlyCtx, {
  type: 'line',
  data: {
    labels: <?= json_encode(array_column($investorMonthlyData, 'month')) ?>,
    datasets: [{
      label: 'New Investors',
      data: <?= json_encode(array_column($investorMonthlyData, 'count')) ?>,
      borderColor: '#268e45',
      backgroundColor: 'rgba(38, 142, 69, 0.1)',
      fill: true,
      tension: 0.3,
      pointRadius: 6,
      pointBackgroundColor: '#268e45'
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false }
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: { stepSize: 1 }
      }
    }
  }
});

// Top Investors by Total Invested
const topInvestorsCtx = document.getElementById('topInvestorsChart').getContext('2d');
new Chart(topInvestorsCtx, {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_map(function ($r) { return $r['investor_code'] ?: $r['name']; }, $topInvestorsData)) ?>,
    datasets: [{
      label: 'Total Approved',
      data: <?= json_encode(array_column($topInvestorsData, 'total_approved')) ?>,
      backgroundColor: '#268e45',
      borderRadius: 6,
      borderSkipped: false,
    }]
  },
  options: {
    indexAxis: 'y',
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { display: false }
    },
    scales: {
      x: {
        beginAtZero: true,
        ticks: {
          callback: function(value) { return value.toLocaleString(); }
        }
      }
    }
  }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
