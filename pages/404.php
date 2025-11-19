<?php
$error_code = '404';
$error_title = 'Page Not Found';
$error_message = 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.';

require_once __DIR__ . '/_error_header.php';
?>

<div class="error-code"><?= $error_code ?></div>
<h1 class="error-title"><?= $error_title ?></h1>
<p class="error-message"><?= $error_message ?></p>
<a href="<?= SITE_URL ?>" class="btn btn-home">
    <i class="fas fa-home me-2"></i>Back to Homepage
</a>

<?php require_once __DIR__ . '/_error_footer.php'; ?>
