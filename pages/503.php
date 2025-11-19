<?php
// You can set a Retry-After header (in seconds) for maintenance pages
// header('Retry-After: 3600'); // 1 hour

$error_code = '503';
$error_title = 'Service Unavailable';
$error_message = 'We are currently performing scheduled maintenance. We\'ll be back online shortly. Thank you for your patience.';

require_once __DIR__ . '/_error_header.php';
?>

<div class="error-code"><?= $error_code ?></div>
<h1 class="error-title"><?= $error_title ?></h1>
<p class="error-message"><?= $error_message ?></p>

<div class="maintenance-info bg-light p-3 rounded mb-4">
    <h5 class="mb-2">
        <i class="fas fa-tools text-warning me-2"></i>Maintenance in Progress
    </h5>
    <p class="mb-0 small">
        We're working hard to improve your experience. Expected completion: <strong>2 hours</strong>
    </p>
</div>

<a href="<?= SITE_URL ?>" class="btn btn-home">
    <i class="fas fa-sync-alt me-2"></i>Try Again
</a>

<?php require_once __DIR__ . '/_error_footer.php'; ?>
