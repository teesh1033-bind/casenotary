<?php
$company   = getCompanySettings();
$user      = Auth::user();
$unreadCount = getUnreadNotificationCount(Auth::id());
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Dashboard') ?> — <?= e($company['company_name']) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="<?= asset('css/app.css') ?>" rel="stylesheet">
    <style>
        :root {
            --primary: <?= e($company['primary_color']) ?>;
            --secondary: <?= e($company['secondary_color']) ?>;
            --dark-accent: <?= e($company['dark_accent']) ?>;
        }
    </style>
</head>
<body>
    <div class="app-wrapper">
        <?php require __DIR__ . '/sidebar.php'; ?>

        <div class="main-content">
            <header class="topbar">
                <button class="sidebar-toggle d-lg-none" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>

                <div class="topbar-search d-none d-md-block">
                    <i class="bi bi-search"></i>
                    <input type="text" placeholder="Search cases, clients, invoices..." class="form-control">
                </div>

                <div class="topbar-actions">
                    <div class="dropdown">
                        <button class="topbar-btn" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell"></i>
                            <?php if ($unreadCount > 0): ?>
                                <span class="notification-dot"><?= $unreadCount ?></span>
                            <?php endif; ?>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                            <div class="dropdown-header d-flex justify-content-between align-items-center">
                                <span>Notifications</span>
                                <?php if ($unreadCount > 0): ?>
                                    <span class="badge bg-primary"><?= $unreadCount ?> new</span>
                                <?php endif; ?>
                            </div>
                            <?php
                            $notifications = getRecentNotifications(Auth::id(), 4);
                            if (empty($notifications)):
                            ?>
                                <div class="dropdown-item-text text-muted text-center py-3">No notifications</div>
                            <?php else: ?>
                                <?php foreach ($notifications as $notif): ?>
                                    <a href="#" class="dropdown-item notification-item <?= !$notif['is_read'] ? 'unread' : '' ?>">
                                        <div class="notification-icon">
                                            <i class="bi <?= notificationIcon($notif['type']) ?>"></i>
                                        </div>
                                        <div class="notification-content">
                                            <strong><?= e($notif['title']) ?></strong>
                                            <p><?= e($notif['message']) ?></p>
                                            <small><?= timeAgo($notif['created_at']) ?></small>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="dropdown">
                        <button class="topbar-profile" data-bs-toggle="dropdown">
                            <div class="profile-avatar">
                                <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                            </div>
                            <div class="profile-info d-none d-md-block">
                                <span class="profile-name"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></span>
                                <span class="profile-role">Administrator</span>
                            </div>
                            <i class="bi bi-chevron-down d-none d-md-block"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= url('auth/logout.php') ?>"><i class="bi bi-box-arrow-right me-2"></i>Sign Out</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <main class="page-content">
