<?php
require_once __DIR__ . '/../core/bootstrap.php';

Auth::requireAdmin();

$pageTitle = 'Dashboard';
$stats     = getDashboardStats();
$chartData = getRevenueChartData();
$recentCases = getRecentCases(5);
$upcomingAppointments = getUpcomingAppointments(5);
$recentActivity = getRecentActivity(6);
$notifications = getRecentNotifications(Auth::id(), 5);

require __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Welcome back, <?= e(Auth::user()['first_name']) ?>! Here's what's happening today.</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-outline-primary">
            <i class="bi bi-download me-2"></i>Export Report
        </button>
        <button class="btn btn-primary">
            <i class="bi bi-plus-lg me-2"></i>New Case
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-primary">
            <div class="stat-card-body">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Total Clients</span>
                    <h3 class="stat-value"><?= number_format($stats['total_clients']) ?></h3>
                </div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-trend positive"><i class="bi bi-arrow-up"></i> Active accounts</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-secondary">
            <div class="stat-card-body">
                <div class="stat-icon"><i class="bi bi-briefcase-fill"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Active Cases</span>
                    <h3 class="stat-value"><?= number_format($stats['active_cases']) ?></h3>
                </div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-trend"><i class="bi bi-clock"></i> In progress</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-warning">
            <div class="stat-card-body">
                <div class="stat-icon"><i class="bi bi-receipt"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Pending Invoices</span>
                    <h3 class="stat-value"><?= number_format($stats['pending_invoices']) ?></h3>
                </div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-trend warning"><i class="bi bi-exclamation-circle"></i> Needs attention</span>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="stat-card stat-card-success">
            <div class="stat-card-body">
                <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                <div class="stat-info">
                    <span class="stat-label">Paid Invoices</span>
                    <h3 class="stat-value"><?= number_format($stats['paid_invoices']) ?></h3>
                </div>
            </div>
            <div class="stat-card-footer">
                <span class="stat-trend positive"><i class="bi bi-check2"></i> Completed</span>
            </div>
        </div>
    </div>
</div>

<!-- Revenue & Appointments Row -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card dashboard-card">
            <div class="card-header">
                <div>
                    <h5 class="card-title">Revenue Analytics</h5>
                    <p class="card-subtitle">Payment trends over the last 6 months</p>
                </div>
                <div class="revenue-summary">
                    <div class="revenue-stat">
                        <span>Total Revenue</span>
                        <strong><?= formatCurrency($stats['total_revenue']) ?></strong>
                    </div>
                    <div class="revenue-stat">
                        <span>This Month</span>
                        <strong class="text-primary"><?= formatCurrency($stats['monthly_revenue']) ?></strong>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card dashboard-card h-100">
            <div class="card-header">
                <div>
                    <h5 class="card-title">Upcoming Appointments</h5>
                    <p class="card-subtitle"><?= $stats['upcoming_appointments'] ?> scheduled</p>
                </div>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcomingAppointments)): ?>
                    <div class="empty-state py-4">
                        <i class="bi bi-calendar-x"></i>
                        <p>No upcoming appointments</p>
                    </div>
                <?php else: ?>
                    <div class="appointment-list">
                        <?php foreach ($upcomingAppointments as $appt): ?>
                            <div class="appointment-item">
                                <div class="appointment-date">
                                    <span class="day"><?= date('d', strtotime($appt['start_time'])) ?></span>
                                    <span class="month"><?= date('M', strtotime($appt['start_time'])) ?></span>
                                </div>
                                <div class="appointment-details">
                                    <h6><?= e($appt['title']) ?></h6>
                                    <p>
                                        <i class="bi bi-clock"></i>
                                        <?= formatDateTime($appt['start_time'], 'g:i A') ?>
                                        &middot;
                                        <?= e($appt['first_name'] . ' ' . $appt['last_name']) ?>
                                    </p>
                                </div>
                                <?= statusBadge($appt['status']) ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Cases, Notifications & Activity -->
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card dashboard-card">
            <div class="card-header">
                <div>
                    <h5 class="card-title">Recent Cases</h5>
                    <p class="card-subtitle">Latest case activity</p>
                </div>
                <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover dashboard-table mb-0">
                        <thead>
                            <tr>
                                <th>Case</th>
                                <th>Client</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentCases as $case): ?>
                                <tr>
                                    <td>
                                        <div class="case-cell">
                                            <strong><?= e($case['case_number']) ?></strong>
                                            <small><?= e($case['title']) ?></small>
                                        </div>
                                    </td>
                                    <td><?= e($case['first_name'] . ' ' . $case['last_name']) ?></td>
                                    <td><?= statusBadge($case['status']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="card dashboard-card">
            <div class="card-header">
                <div>
                    <h5 class="card-title">Notifications</h5>
                    <p class="card-subtitle">Recent alerts</p>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="notification-list">
                    <?php foreach ($notifications as $notif): ?>
                        <div class="notification-list-item <?= !$notif['is_read'] ? 'unread' : '' ?>">
                            <div class="notif-icon type-<?= e($notif['type']) ?>">
                                <i class="bi <?= notificationIcon($notif['type']) ?>"></i>
                            </div>
                            <div class="notif-body">
                                <strong><?= e($notif['title']) ?></strong>
                                <p><?= e(mb_strimwidth($notif['message'], 0, 60, '...')) ?></p>
                                <small><?= timeAgo($notif['created_at']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card dashboard-card">
            <div class="card-header">
                <div>
                    <h5 class="card-title">Recent Activity</h5>
                    <p class="card-subtitle">Audit log timeline</p>
                </div>
            </div>
            <div class="card-body">
                <div class="activity-timeline">
                    <?php foreach ($recentActivity as $activity): ?>
                        <div class="activity-item">
                            <div class="activity-dot"></div>
                            <div class="activity-content">
                                <p>
                                    <strong><?= e(($activity['first_name'] ?? 'System') . ' ' . ($activity['last_name'] ?? '')) ?></strong>
                                    <?= e(ucfirst($activity['action'])) ?>
                                    <?php if ($activity['entity_type']): ?>
                                        <span class="text-muted"><?= e($activity['entity_type']) ?></span>
                                    <?php endif; ?>
                                </p>
                                <small><?= timeAgo($activity['created_at']) ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$pageScripts = '<script>
document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById("revenueChart");
    if (ctx) {
        const primaryColor = getComputedStyle(document.documentElement).getPropertyValue("--primary").trim() || "#3aafa9";
        new Chart(ctx, {
            type: "line",
            data: {
                labels: ' . json_encode($chartData['labels']) . ',
                datasets: [{
                    label: "Revenue",
                    data: ' . json_encode($chartData['data']) . ',
                    borderColor: primaryColor,
                    backgroundColor: "rgba(58, 175, 169, 0.1)",
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: primaryColor,
                    pointBorderColor: "#fff",
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: "#00182c",
                        titleFont: { family: "Montserrat" },
                        bodyFont: { family: "Montserrat" },
                        callbacks: {
                            label: function(ctx) {
                                return "$" + ctx.parsed.y.toLocaleString("en-US", {minimumFractionDigits: 2});
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: "Montserrat", size: 12 } }
                    },
                    y: {
                        grid: { color: "rgba(0,0,0,0.05)" },
                        ticks: {
                            font: { family: "Montserrat", size: 12 },
                            callback: function(v) { return "$" + v.toLocaleString(); }
                        }
                    }
                }
            }
        });
    }
});
</script>';

require __DIR__ . '/../includes/footer.php';
