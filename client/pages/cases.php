<?php
require_once __DIR__ . '/../core/bootstrap.php';

Auth::requireClient();

$clientId = Auth::clientId();
if (!$clientId) {
    flash('error', 'Client profile not found.');
    header('Location: ' . adminUrl('auth/login.php?portal=client'));
    exit;
}

$pageTitle = 'My Cases';
$pageSubtitle = 'View your cases and upload documents';
$cases = getClientCases($clientId);

require __DIR__ . '/../includes/header.php';
?>

<div class="saas-card">
    <div class="saas-card-header">
        <div>
            <h2 class="saas-card-title">My Cases</h2>
            <p class="saas-card-subtitle"><?= count($cases) ?> assigned case(s)</p>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (empty($cases)): ?>
            <div class="empty-state py-5">
                <i class="bi bi-briefcase"></i>
                <p>No cases assigned yet.</p>
                <p class="text-muted small mb-0">When a case is assigned to you, you can view details and upload documents here.</p>
            </div>
        <?php else: ?>
            <div class="alert alert-light border-0 rounded-0 mb-0 py-3 px-4">
                <i class="bi bi-info-circle me-2 text-primary"></i>
                Open a case and use the <strong>Documents</strong> tab to upload PDF, Word, image, or ZIP files.
            </div>
            <div class="table-responsive">
                <table class="table saas-table mb-0">
                    <thead>
                        <tr>
                            <th>Case #</th>
                            <th>Title</th>
                            <th>Service</th>
                            <th>Status</th>
                            <th>Documents</th>
                            <th>Updated</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cases as $case): ?>
                            <tr>
                                <td><strong><?= e($case['case_number']) ?></strong></td>
                                <td><?= e($case['title']) ?></td>
                                <td><?= e($case['service_type']) ?></td>
                                <td><?= statusBadge($case['status']) ?></td>
                                <td><?= number_format((int) ($case['document_count'] ?? 0)) ?></td>
                                <td class="text-muted"><?= formatDate($case['updated_at']) ?></td>
                                <td class="text-end text-nowrap">
                                    <a href="<?= clientUrl('pages/case-view.php?id=' . $case['id']) ?>" class="btn btn-soft btn-sm">View</a>
                                    <a href="<?= clientUrl('pages/case-view.php?id=' . $case['id'] . '#documents') ?>" class="btn btn-primary btn-sm">
                                        <i class="bi bi-upload"></i> Upload
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/footer.php'; ?>
