<?php
$error_code = '500';
$error_title = 'Internal Server Error';
$error_message = 'The server encountered an internal error and was unable to complete your request. Please try again later or contact support if the problem persists.';

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
        <i class="fas fa-headset me-2"></i>Contact Support
    </a>
</div>

<?php require_once __DIR__ . '/_error_footer.php'; ?>
