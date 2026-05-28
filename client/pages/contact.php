<?php
require_once __DIR__ . '/../core/bootstrap.php';

Auth::requireClient();

$clientId = Auth::clientId();
if (!$clientId) {
    flash('error', 'Client profile not found.');
    header('Location: ' . adminUrl('auth/login.php?portal=client'));
    exit;
}

$company = getCompanySettings();
$pageTitle = 'Contact';
$pageSubtitle = 'Get in touch with us';

require __DIR__ . '/../includes/header.php';
?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="saas-card h-100">
            <div class="saas-card-header">
                <div>
                    <h2 class="saas-card-title"><?= e($company['company_name']) ?></h2>
                    <p class="saas-card-subtitle mb-0">We're here to help with your cases and appointments</p>
                </div>
            </div>
            <div class="card-body">
                <?php if (!empty($company['description'])): ?>
                    <p class="text-muted mb-4"><?= nl2br(e($company['description'])) ?></p>
                <?php endif; ?>

                <div class="row g-4">
                    <?php if (!empty($company['office_email'])): ?>
                        <div class="col-sm-6">
                            <div class="metric-card h-100">
                                <div class="metric-icon metric-icon-primary"><i class="bi bi-envelope"></i></div>
                                <div class="metric-body">
                                    <span class="metric-label">Email</span>
                                    <a href="mailto:<?= e($company['office_email']) ?>" class="metric-value metric-value-sm text-decoration-none">
                                        <?= e($company['office_email']) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($company['office_phone'])): ?>
                        <div class="col-sm-6">
                            <div class="metric-card h-100">
                                <div class="metric-icon metric-icon-success"><i class="bi bi-telephone"></i></div>
                                <div class="metric-body">
                                    <span class="metric-label">Phone</span>
                                    <a href="tel:<?= e(preg_replace('/\s+/', '', $company['office_phone'])) ?>" class="metric-value metric-value-sm text-decoration-none">
                                        <?= e($company['office_phone']) ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($company['address'])): ?>
                        <div class="col-12">
                            <div class="metric-card">
                                <div class="metric-icon metric-icon-warning"><i class="bi bi-geo-alt"></i></div>
                                <div class="metric-body">
                                    <span class="metric-label">Office Address</span>
                                    <span class="metric-value metric-value-sm"><?= nl2br(e($company['address'])) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if (empty($company['office_email']) && empty($company['office_phone']) && empty($company['address'])): ?>
                    <div class="empty-state py-4">
                        <i class="bi bi-envelope"></i>
                        <p class="mb-0">Contact details have not been configured yet. Please check back later.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="saas-card h-100">
            <div class="saas-card-header">
                <div>
                    <h2 class="saas-card-title">Quick Links</h2>
                    <p class="saas-card-subtitle mb-0">Common actions in your portal</p>
                </div>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="<?= clientUrl('pages/cases.php') ?>" class="btn btn-soft text-start">
                        <i class="bi bi-briefcase me-2"></i> View my cases
                    </a>
                    <a href="<?= clientUrl('pages/payments.php') ?>" class="btn btn-soft text-start">
                        <i class="bi bi-receipt me-2"></i> Check invoices & payments
                    </a>
                    <a href="<?= clientUrl('pages/appointments.php') ?>" class="btn btn-soft text-start">
                        <i class="bi bi-calendar3 me-2"></i> View appointments
                    </a>
                    <a href="<?= clientUrl('pages/notifications.php') ?>" class="btn btn-soft text-start">
                        <i class="bi bi-bell me-2"></i> Notifications
                    </a>
                </div>

                <?php if (!empty($company['office_email'])): ?>
                    <hr>
                    <p class="small text-muted mb-2">Need help with a specific case? Email us and include your case number.</p>
                    <a href="mailto:<?= e($company['office_email']) ?>?subject=Client%20Portal%20Support" class="btn btn-primary w-100">
                        <i class="bi bi-send me-2"></i> Send Email
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
