<?php
/**
 * Service Info Page - Grand Jyothi Construction
 * BuildDream Theme: Modern, Professional, Yellow + Charcoal
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

// Get slug from query string
$slug = $_GET['slug'] ?? '';

if (empty($slug)) {
    header('Location: services.php');
    exit;
}

// Fetch service details
$sql = "SELECT title, description, icon, category, author, cover_image, created_at 
        FROM services WHERE slug = :slug LIMIT 1";
$stmt = executeQuery($sql, [':slug' => $slug]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$service) {
    header('Location: services.php');
    exit;
}

$page_title = sanitizeOutput($service['title']) . ' | Grand Jyothi Construction';
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section service-hero" style="
    background: linear-gradient(rgba(26,26,26,0.75), rgba(26,26,26,0.75)),
                url('<?= sanitizeOutput($service['cover_image'] ?? '/images/default.jpg') ?>') 
                no-repeat center center;
    background-size: cover;
">
    <div class="container text-center text-white">
        <h1><?= sanitizeOutput($service['title']) ?></h1>
        <p class="lead mb-0">
            <?= sanitizeOutput($service['category'] ?? 'Construction Services') ?>
        </p>
    </div>
</section>

<main>
    <section class="section-padding">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <article class="service-article bg-white p-4 rounded shadow-sm">
                        <div class="service-meta mb-4 text-muted small">
                            <i class="fas fa-user me-2"></i> <?= sanitizeOutput($service['author'] ?? 'Admin') ?> 
                            &nbsp;|&nbsp;
                            <i class="fas fa-calendar me-2"></i> <?= date('F j, Y', strtotime($service['created_at'])) ?>
                        </div>

                        <div class="service-content mb-4">
                            <p class="lead"><?= nl2br(sanitizeOutput($service['description'])) ?></p>
                        </div>

                        <div class="text-center mt-5">
                            <a href="services.php" class="btn btn-primary">
                                <i class="fas fa-arrow-left me-2"></i> Back to Services
                            </a>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- BuildDream Theme Styles -->
<style>
    :root {
        --primary-yellow: #F9A826;
        --charcoal: #1A1A1A;
        --white: #FFFFFF;
        --light-gray: #f8f9fa;
    }

    body {
        font-family: 'Roboto', sans-serif;
        color: var(--charcoal);
        background-color: var(--white);
        line-height: 1.7;
    }

    .service-hero {
        padding: 140px 0 100px;
        text-align: center;
        color: var(--white);
        text-shadow: 0 2px 10px rgba(0,0,0,0.6);
    }

    .service-hero h1 {
        font-size: 3rem;
        margin-bottom: 15px;
    }

    .section-padding {
        padding: 80px 0;
    }

    .service-article {
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    }

    .service-meta {
        font-size: 0.9rem;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }

    .service-content p {
        font-size: 1.05rem;
        color: #555;
    }

    .btn-primary {
        background-color: var(--primary-yellow);
        border-color: var(--primary-yellow);
        color: var(--charcoal);
        font-weight: 600;
        padding: 10px 25px;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #e89a1f;
        border-color: #e89a1f;
        color: var(--charcoal);
    }

    @media (max-width: 768px) {
        .service-hero {
            padding: 100px 0 70px;
        }
        .service-hero h1 {
            font-size: 2.2rem;
        }
    }
</style>
