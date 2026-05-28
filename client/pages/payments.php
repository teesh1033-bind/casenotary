<?php
require_once __DIR__ . '/../core/bootstrap.php';

Auth::requireClient();

$clientId = Auth::clientId();
if (!$clientId) {
    flash('error', 'Client profile not found.');
    header('Location: ' . adminUrl('auth/login.php?portal=client'));
    exit;
}

$pageTitle = 'Payments';
$pageSubtitle = 'Your invoices and payment history';
$invoices = getClientInvoices($clientId);
$payments = getClientPayments($clientId);
$stats = getClientDashboardStats($clientId);

require __DIR__ . '/../includes/header.php';
?>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon metric-icon-warning"><i class="bi bi-hourglass-split"></i></div>
            <div class="metric-body">
                <span class="metric-label">Pending Invoices</span>
                <span class="metric-value"><?= number_format($stats['pending_invoices']) ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon metric-icon-primary"><i class="bi bi-receipt"></i></div>
            <div class="metric-body">
                <span class="metric-label">Total Invoices</span>
                <span class="metric-value"><?= number_format(count($invoices)) ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric-card">
            <div class="metric-icon metric-icon-success"><i class="bi bi-cash-stack"></i></div>
            <div class="metric-body">
                <span class="metric-label">Payments Made</span>
                <span class="metric-value"><?= number_format(count($payments)) ?></span>
            </div>
        </div>
    </div>
</div>

<div class="saas-card mb-4">
    <div class="saas-card-header appointment-list-header">
        <div>
            <h2 class="saas-card-title">Invoices</h2>
            <p class="saas-card-subtitle mb-0"><?= count($invoices) ?> invoice(s)</p>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($invoices)): ?>
            <div class="empty-state py-5">
                <i class="bi bi-receipt"></i>
                <p class="mb-0">No invoices yet.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table saas-table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Case</th>
                            <th>Amount</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $invoice): ?>
                            <?php $status = $invoice['payment_status'] ?? $invoice[invoiceStatusColumn()] ?? 'pending'; ?>
                            <tr>
                                <td><strong><?= e($invoice['invoice_number']) ?></strong></td>
                                <td>
                                    <?php if (!empty($invoice['case_number'])): ?>
                                        <span class="table-primary"><?= e($invoice['case_number']) ?></span>
                                        <?php if (!empty($invoice['case_title'])): ?>
                                            <span class="table-secondary d-block"><?= e($invoice['case_title']) ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><span class="table-primary"><?= formatCurrency((float) ($invoice['total'] ?? 0)) ?></span></td>
                                <td class="text-muted"><?= !empty($invoice['due_date']) ? formatDate($invoice['due_date']) : '—' ?></td>
                                <td><?= statusBadge($status) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="saas-card">
    <div class="saas-card-header appointment-list-header">
        <div>
            <h2 class="saas-card-title">Payment History</h2>
            <p class="saas-card-subtitle mb-0"><?= count($payments) ?> payment(s)</p>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($payments)): ?>
            <div class="empty-state py-5">
                <i class="bi bi-credit-card"></i>
                <p class="mb-0">No payments recorded yet.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table saas-table mb-0">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Paid At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td>
                                    <span class="table-primary"><?= e($payment['invoice_number']) ?></span>
                                    <span class="table-secondary d-block"><?= formatCurrency((float) ($payment['invoice_total'] ?? 0)) ?></span>
                                </td>
                                <td><span class="table-primary"><?= formatCurrency((float) $payment['amount']) ?></span></td>
                                <td><?= e(ucwords(str_replace('_', ' ', $payment['payment_method'] ?? 'other'))) ?></td>
                                <td><?= paymentStatusBadge(paymentStatusValue($payment)) ?></td>
                                <td class="text-muted"><?= formatDateTime($payment['paid_at'] ?? $payment['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
