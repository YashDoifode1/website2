<?php
$error_code = '403';
$error_title = 'Access Forbidden';
$error_message = 'You do not have permission to access this page. Please contact the administrator if you believe this is an error.';

require_once __DIR__ . '/_error_header.php';
?>

<div class="error-code"><?= $error_code ?></div>
<h1 class="error-title"><?= $error_title ?></h1>
<p class="error-message"><?= $error_message ?></p>
<div class="d-flex justify-content-center gap-3">
    <a href="<?= SITE_URL ?>" class="btn btn-home">
        <i class="fas fa-home me-2"></i>Back to Home
    </a>
    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-outline-secondary">
        <i class="fas fa-envelope me-2"></i>Contact Admin
    </a>
</div>

<?php require_once __DIR__ . '/_error_footer.php'; ?>
