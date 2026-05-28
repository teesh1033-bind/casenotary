<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$company = getCompanySettings();

$navItems = [
    ['icon' => 'bi-grid-1x2-fill', 'label' => 'Dashboard', 'href' => 'pages/dashboard.php', 'page' => 'dashboard'],
    ['icon' => 'bi-people-fill', 'label' => 'Clients', 'href' => '#', 'page' => 'clients'],
    ['icon' => 'bi-briefcase-fill', 'label' => 'Cases', 'href' => '#', 'page' => 'cases'],
    ['icon' => 'bi-file-earmark-text', 'label' => 'Documents', 'href' => '#', 'page' => 'documents'],
    ['icon' => 'bi-receipt', 'label' => 'Invoices', 'href' => '#', 'page' => 'invoices'],
    ['icon' => 'bi-credit-card', 'label' => 'Payments', 'href' => '#', 'page' => 'payments'],
    ['icon' => 'bi-calendar-event', 'label' => 'Appointments', 'href' => '#', 'page' => 'appointments'],
    ['icon' => 'bi-bell', 'label' => 'Notifications', 'href' => '#', 'page' => 'notifications'],
    ['icon' => 'bi-bar-chart-line', 'label' => 'Reports', 'href' => '#', 'page' => 'reports'],
    ['icon' => 'bi-gear', 'label' => 'Settings', 'href' => '#', 'page' => 'settings'],
];
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="brand-text">
                <span class="brand-name"><?= e($company['company_name']) ?></span>
                <span class="brand-tag">Admin Portal</span>
            </div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <?php foreach ($navItems as $item): ?>
                <li class="nav-item">
                    <a href="<?= url($item['href']) ?>"
                       class="nav-link <?= $currentPage === $item['page'] ? 'active' : '' ?>">
                        <i class="bi <?= e($item['icon']) ?>"></i>
                        <span><?= e($item['label']) ?></span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-help">
            <i class="bi bi-headset"></i>
            <div>
                <span>Need help?</span>
                <a href="mailto:<?= e($company['office_email'] ?? '') ?>">Contact Support</a>
            </div>
        </div>
    </div>
</aside>
