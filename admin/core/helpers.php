<?php

declare(strict_types=1);

function url(string $path = ''): string
{
    $config = require __DIR__ . '/../config/config.php';
    return rtrim($config['app_url'], '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function old(string $key, string $default = ''): string
{
    return e($_SESSION['old'][$key] ?? $default);
}

function setOld(array $data): void
{
    $_SESSION['old'] = $data;
}

function clearOld(): void
{
    unset($_SESSION['old']);
}

function formatCurrency(float $amount): string
{
    return '$' . number_format($amount, 2);
}

function formatDate(?string $date, string $format = 'M d, Y'): string
{
    if (!$date) {
        return '—';
    }
    return date($format, strtotime($date));
}

function formatDateTime(?string $datetime, string $format = 'M d, Y g:i A'): string
{
    if (!$datetime) {
        return '—';
    }
    return date($format, strtotime($datetime));
}

function statusBadge(string $status): string
{
    $map = [
        'pending'            => 'badge-pending',
        'in_progress'        => 'badge-progress',
        'waiting_for_client' => 'badge-waiting',
        'completed'          => 'badge-completed',
        'closed'             => 'badge-closed',
        'paid'               => 'badge-paid',
        'partially_paid'     => 'badge-partial',
        'overdue'            => 'badge-overdue',
        'scheduled'          => 'badge-scheduled',
        'confirmed'          => 'badge-confirmed',
    ];

    $class = $map[$status] ?? 'badge-default';
    $label = ucwords(str_replace('_', ' ', $status));

    return sprintf('<span class="status-badge %s">%s</span>', $class, e($label));
}

function timeAgo(string $datetime): string
{
    $time  = strtotime($datetime);
    $diff  = time() - $time;

    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . 'm ago';
    if ($diff < 86400) return floor($diff / 3600) . 'h ago';
    if ($diff < 604800) return floor($diff / 86400) . 'd ago';

    return formatDate($datetime);
}

function getCompanySettings(): array
{
    static $settings = null;

    if ($settings === null) {
        $settings = Database::fetch('SELECT * FROM company_settings LIMIT 1') ?? [
            'company_name'    => 'Notary Management',
            'primary_color'   => '#3aafa9',
            'secondary_color' => '#00182c',
            'dark_accent'     => '#000000',
            'font_family'     => 'Montserrat',
            'logo'            => null,
        ];
    }

    return $settings;
}

function getDashboardStats(): array
{
    $totalClients = Database::fetch('SELECT COUNT(*) AS count FROM clients')['count'] ?? 0;

    $activeCases = Database::fetch(
        "SELECT COUNT(*) AS count FROM cases WHERE status IN ('pending', 'in_progress', 'waiting_for_client')"
    )['count'] ?? 0;

    $pendingInvoices = Database::fetch(
        "SELECT COUNT(*) AS count FROM invoices WHERE status IN ('pending', 'overdue', 'partially_paid')"
    )['count'] ?? 0;

    $paidInvoices = Database::fetch(
        "SELECT COUNT(*) AS count FROM invoices WHERE status = 'paid'"
    )['count'] ?? 0;

    $upcomingAppointments = Database::fetch(
        "SELECT COUNT(*) AS count FROM appointments WHERE start_time >= NOW() AND status IN ('scheduled', 'confirmed')"
    )['count'] ?? 0;

    $totalRevenue = Database::fetch(
        "SELECT COALESCE(SUM(amount), 0) AS total FROM payments WHERE status = 'completed'"
    )['total'] ?? 0;

    $monthlyRevenue = Database::fetch(
        "SELECT COALESCE(SUM(amount), 0) AS total FROM payments WHERE status = 'completed' AND MONTH(paid_at) = MONTH(NOW()) AND YEAR(paid_at) = YEAR(NOW())"
    )['total'] ?? 0;

    return [
        'total_clients'         => (int) $totalClients,
        'active_cases'          => (int) $activeCases,
        'pending_invoices'      => (int) $pendingInvoices,
        'paid_invoices'         => (int) $paidInvoices,
        'upcoming_appointments' => (int) $upcomingAppointments,
        'total_revenue'         => (float) $totalRevenue,
        'monthly_revenue'       => (float) $monthlyRevenue,
    ];
}

function getRecentActivity(int $limit = 8): array
{
    return Database::fetchAll(
        'SELECT al.*, u.first_name, u.last_name
         FROM audit_logs al
         LEFT JOIN users u ON u.id = al.user_id
         ORDER BY al.created_at DESC
         LIMIT ?',
        [$limit]
    );
}

function getRecentNotifications(int $userId, int $limit = 5): array
{
    return Database::fetchAll(
        'SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?',
        [$userId, $limit]
    );
}

function getUnreadNotificationCount(int $userId): int
{
    return (int) (Database::fetch(
        'SELECT COUNT(*) AS count FROM notifications WHERE user_id = ? AND is_read = 0',
        [$userId]
    )['count'] ?? 0);
}

function getUpcomingAppointments(int $limit = 5): array
{
    return Database::fetchAll(
        "SELECT a.*, c.company_name, u.first_name, u.last_name
         FROM appointments a
         JOIN clients c ON c.id = a.client_id
         JOIN users u ON u.id = c.user_id
         WHERE a.start_time >= NOW() AND a.status IN ('scheduled', 'confirmed')
         ORDER BY a.start_time ASC
         LIMIT ?",
        [$limit]
    );
}

function getRecentCases(int $limit = 5): array
{
    return Database::fetchAll(
        "SELECT cs.*, u.first_name, u.last_name, cl.company_name
         FROM cases cs
         JOIN clients cl ON cl.id = cs.client_id
         JOIN users u ON u.id = cl.user_id
         ORDER BY cs.updated_at DESC
         LIMIT ?",
        [$limit]
    );
}

function getRevenueChartData(): array
{
    $rows = Database::fetchAll(
        "SELECT DATE_FORMAT(paid_at, '%b') AS month_label,
                MONTH(paid_at) AS month_num,
                COALESCE(SUM(amount), 0) AS total
         FROM payments
         WHERE status = 'completed' AND paid_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
         GROUP BY MONTH(paid_at), DATE_FORMAT(paid_at, '%b')
         ORDER BY month_num ASC"
    );

    $months  = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    $data    = array_fill(0, 6, 0);
    $labels  = [];

    for ($i = 5; $i >= 0; $i--) {
        $monthIndex = (int) date('n', strtotime("-{$i} months")) - 1;
        $labels[]   = $months[$monthIndex];
    }

    foreach ($rows as $row) {
        $idx = array_search($row['month_label'], $labels);
        if ($idx !== false) {
            $data[$idx] = (float) $row['total'];
        }
    }

    return ['labels' => $labels, 'data' => $data];
}

function notificationIcon(string $type): string
{
    $icons = [
        'invoice'     => 'bi-receipt',
        'payment'     => 'bi-credit-card',
        'appointment' => 'bi-calendar-event',
        'document'    => 'bi-file-earmark',
        'case'        => 'bi-briefcase',
        'account'     => 'bi-person-plus',
        'system'      => 'bi-bell',
    ];

    return $icons[$type] ?? 'bi-bell';
}
