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
$contactPhone = trim($company['office_phone'] ?? '') ?: '+1 (555) 123-4567';
$businessHours = trim($company['business_hours'] ?? '') ?: 'Monday – Friday: 9:00 AM – 5:00 PM, Saturday – Sunday: Closed';
$pageTitle = 'Contact';
$pageSubtitle = 'Get in touch with our team';

require __DIR__ . '/../includes/header.php';
?>

<style>
    .contact-page .saas-card-header.contact-panel-header,
    .contact-page .saas-card-header.contact-form-header {
        padding: 1.25rem 2.5rem !important;
    }

    .contact-page .contact-info-body,
    .contact-page .contact-form-body {
        padding: 2rem 2.5rem 2.25rem !important;
    }

    .contact-page .contact-message-form .form-control {
        width: 100%;
        box-sizing: border-box;
    }

    .contact-info-list {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .contact-info-row {
        display: flex !important;
        align-items: flex-start !important;
    }

    .contact-info-icon {
        font-size: 1.125rem !important;
        color: var(--primary) !important;
        margin-right: 0.875rem !important;
        width: 1.25rem !important;
        text-align: center !important;
        line-height: 1.45 !important;
        flex-shrink: 0;
    }

    .contact-info-content {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .contact-info-term {
        font-weight: 700 !important;
        color: var(--secondary) !important;
        display: block !important;
    }

    .contact-info-text {
        color: var(--gray-600) !important;
        font-weight: 400;
        line-height: 1.55;
    }

    .contact-info-link {
        text-decoration: none !important;
        color: var(--primary) !important;
    }

    .contact-info-link:hover {
        text-decoration: underline !important;
    }
</style>
<div class="contact-page">
    <div class="row g-4 contact-page-top">
        <div class="col-lg-6">
            <div class="saas-card h-100 contact-panel">
                <div class="saas-card-header contact-panel-header">
                    <h2 class="saas-card-title mb-0">Office Information</h2>
                </div>
                <div class="card-body contact-info-body">
                    <div class="contact-info-list">
                        <div class="contact-info-row">
                            <div class="contact-info-icon"><i class="bi bi-building"></i></div>
                            <div class="contact-info-content">
                                <span class="contact-info-term">Company:</span>
                                <span class="contact-info-text"><?= e($company['company_name'] ?? 'Notary Management Pro') ?></span>
                            </div>
                        </div>

                        <?php if (!empty($company['description'])): ?>
                            <div class="contact-info-row">
                                <div class="contact-info-icon"><i class="bi bi-briefcase"></i></div>
                                <div class="contact-info-content">
                                    <span class="contact-info-term">Services:</span>
                                    <span class="contact-info-text"><?= e($company['description']) ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($company['office_email'])): ?>
                            <div class="contact-info-row">
                                <div class="contact-info-icon"><i class="bi bi-envelope"></i></div>
                                <div class="contact-info-content">
                                    <span class="contact-info-term">Email Us:</span>
                                    <a href="mailto:<?= e($company['office_email']) ?>" class="contact-info-text contact-info-link">
                                        <?= e($company['office_email']) ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="contact-info-row">
                            <div class="contact-info-icon"><i class="bi bi-telephone"></i></div>
                            <div class="contact-info-content">
                                <span class="contact-info-term">Contact Us:</span>
                                <a href="tel:<?= e(preg_replace('/\s+/', '', $contactPhone)) ?>" class="contact-info-text contact-info-link">
                                    <?= e($contactPhone) ?>
                                </a>
                            </div>
                        </div>

                        <div class="contact-info-row">
                            <div class="contact-info-icon"><i class="bi bi-clock"></i></div>
                            <div class="contact-info-content">
                                <span class="contact-info-term">Business Hours:</span>
                                <span class="contact-info-text">
                                    Monday – Friday: 9:00 AM – 5:00 PM<br>
                                    <span style="color: #6c757d;">Saturday – Sunday: Closed</span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="saas-card h-100 contact-panel">
                <div class="saas-card-header contact-form-header">
                    <h2 class="saas-card-title mb-0">Send a Message</h2>
                    <p class="contact-response-note mb-0">We typically respond with one or two business day(s).</p>
                </div>
                <div class="card-body contact-form-body">
                    <form method="post" action="<?= clientUrl('actions/contact-action.php') ?>" class="contact-message-form">
                        <?= CSRF::field() ?>
                        <div class="mb-3">
                            <label class="form-label contact-form-label" for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control" required
                                   value="<?= e(old('subject')) ?>" placeholder="How can we help?">
                        </div>
                        <div class="mb-4">
                            <label class="form-label contact-form-label" for="message">Message</label>
                            <textarea id="message" name="message" class="form-control" rows="7" required
                                      placeholder="Write your message here..."><?= e(old('message')) ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send me-2"></i> Send Message
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="saas-card contact-quick-links-card contact-panel mt-4">
        <div class="saas-card-header contact-panel-header">
            <div>
                <h2 class="saas-card-title mb-0">Quick Links</h2>
                <p class="saas-card-subtitle mb-0">Common actions in your portal</p>
            </div>
        </div>
        <div class="card-body contact-quick-links-body">
            <div class="row g-3 g-lg-4">
                <div class="col-sm-6 col-xl-3">
                    <a href="<?= clientUrl('pages/cases.php') ?>" class="contact-quick-tile">
                        <div class="contact-quick-tile-head">
                            <i class="bi bi-briefcase"></i>
                            <span>Cases</span>
                        </div>
                        <div class="contact-quick-tile-body">
                            My Cases <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <a href="<?= clientUrl('pages/payments.php') ?>" class="contact-quick-tile">
                        <div class="contact-quick-tile-head">
                            <i class="bi bi-credit-card"></i>
                            <span>Invoice/Payment</span>
                        </div>
                        <div class="contact-quick-tile-body">
                            Check Invoices &amp; Payments <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <a href="<?= clientUrl('pages/appointments.php') ?>" class="contact-quick-tile">
                        <div class="contact-quick-tile-head">
                            <i class="bi bi-calendar3"></i>
                            <span>Appointments</span>
                        </div>
                        <div class="contact-quick-tile-body">
                            View Appointments <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-xl-3">
                    <a href="<?= clientUrl('pages/notifications.php') ?>" class="contact-quick-tile">
                        <div class="contact-quick-tile-head">
                            <i class="bi bi-bell"></i>
                            <span>Notification</span>
                        </div>
                        <div class="contact-quick-tile-body">
                            All Notifications <i class="bi bi-arrow-right"></i>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>