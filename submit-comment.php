<?php
/**
 * submit-comment.php
 * Secure comment submission for Grand Jyothi Blog
 * Validates, sanitizes, saves, redirects with feedback
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/config.php'; // Must define: define('SITE_URL', 'https://www.grandjyothi.com');

// === 1. SECURITY: Block direct access without POST ===
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . SITE_URL . "/blog.php");
    exit;
}

// === 2. HONEYPOT SPAM PROTECTION ===
if (!empty($_POST['website'])) {
    // Bot filled the hidden field
    header("Location: " . SITE_URL . "/blog.php");
    exit;
}

// === 3. GET & VALIDATE DATA ===
$article_id = (int)($_POST['article_id'] ?? 0);
$name       = trim($_POST['name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$comment    = trim($_POST['comment'] ?? '');

// Validate article exists
$article = executeQuery(
    "SELECT id, slug FROM blog_articles WHERE id = ? AND is_published = 1",
    [$article_id]
)->fetch();

if (!$article) {
    $_SESSION['comment_error'] = "Invalid article.";
    header("Location: " . SITE_URL . "/blog-detail.php?slug=" . urlencode($article['slug'] ?? ''));
    exit;
}

// === 4. VALIDATE INPUTS ===
$errors = [];

if (empty($name) || strlen($name) > 100) {
    $errors[] = "Name is required and must be under 100 characters.";
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 255) {
    $errors[] = "Valid email is required.";
}

if (empty($comment) || strlen($comment) < 10 || strlen($comment) > 2000) {
    $errors[] = "Comment must be between 10 and 2000 characters.";
}

// === 5. RATE LIMIT: Max 3 comments per hour per email ===
$recent = executeQuery(
    "SELECT COUNT(*) FROM blog_comments 
     WHERE email = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
    [$email]
)->fetchColumn();

if ($recent >= 3) {
    $errors[] = "Too many comments. Please try again in an hour.";
}

// === 6. IF ERRORS: Store & Redirect ===
if ($errors) {
    $_SESSION['comment_errors'] = $errors;
    $_SESSION['comment_data'] = $_POST;
    header("Location: " . SITE_URL . "/blog-detail.php?slug=" . urlencode($article['slug']));
    exit;
}

// === 7. SANITIZE & SAVE COMMENT ===
try {
    executeQuery(
        "INSERT INTO blog_comments (article_id, name, email, comment, is_approved) 
         VALUES (?, ?, ?, ?, 0)", // Auto-moderate or approve manually
        [
            $article_id,
            htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
            filter_var($email, FILTER_SANITIZE_EMAIL),
            htmlspecialchars($comment, ENT_QUOTES, 'UTF-8')
        ]
    );

    $_SESSION['comment_success'] = "Thank you! Your comment is awaiting moderation.";
} catch (Exception $e) {
    error_log("Comment save failed: " . $e->getMessage());
    $_SESSION['comment_error'] = "Failed to submit comment. Please try again.";
}

header("Location: " . SITE_URL . "/blog-detail.php?slug=" . urlencode($article['slug']));
exit;