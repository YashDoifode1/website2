<?php
/**
 * Contact Page – Grand Jyothi Construction
 * FIXED: Proper mobile layout - Form appears FIRST on mobile
 */
declare(strict_types=1);
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/config.php';
$page_title = 'Contact Us | Grand Jyothi Construction';
$success_message = $error_message = '';

/* ---------- 1. Form submission ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid security token. Please try again.';
        logSecurityEvent('CSRF fail on contact form', 'WARNING');
    } elseif (!checkRateLimit('contact_form', 5, 300)) {
        $remaining = getRateLimitRemaining('contact_form', 300);
        $error_message = 'Too many submissions. Try again in ' . ceil($remaining / 60) . ' minutes.';
    } else {
        $first_name = sanitizeInput(trim($_POST['first_name'] ?? ''));
        $last_name = sanitizeInput(trim($_POST['last_name'] ?? ''));
        $email = sanitizeInput(trim($_POST['email'] ?? ''));
        $phone = sanitizeInput(trim($_POST['phone'] ?? ''));
        $project_type = sanitizeInput(trim($_POST['project_type'] ?? ''));
        $budget = sanitizeInput(trim($_POST['budget'] ?? ''));
        $message = sanitizeInput(trim($_POST['message'] ?? ''));
        $errors = [];
        if (empty($first_name) || strlen($first_name) < 2) $errors[] = 'Valid first name required.';
        if (empty($last_name) || strlen($last_name) < 2) $errors[] = 'Valid last name required.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email required.';
        if (empty($phone) || !preg_match('/^\+?\d{10,15}$/', $phone)) $errors[] = 'Valid phone required.';
        if (empty($message) || strlen($message) < 20) $errors[] = 'Message must be at least 20 characters.';
        if (empty($errors)) {
            try {
                executeQuery(
                    "INSERT INTO contact_messages
                     (first_name,last_name,email,phone,project_type,budget,message,submitted_at)
                     VALUES (?,?,?,?,?,?,?,NOW())",
                    [$first_name,$last_name,$email,$phone,$project_type,$budget,$message]
                );
                $success_message = "Thank you! We'll contact you within 24 hours.";
                logSecurityEvent('Contact form submitted', 'INFO');
            } catch (PDOException $e) {
                error_log('Contact DB error: '.$e->getMessage());
                $error_message = 'Submission failed. Please try again later.';
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

/* ---------- 2. Sidebar data ---------- */
$categories = executeQuery(
    "SELECT SUBSTRING_INDEX(title,' ',1) AS cat, COUNT(*) AS cnt
     FROM packages WHERE is_active=1 GROUP BY cat ORDER BY cat"
)->fetchAll();
$total_packages = executeQuery("SELECT COUNT(*) FROM packages WHERE is_active=1")->fetchColumn();
$popular_packages = executeQuery(
    "SELECT title, price_per_sqft FROM packages
     WHERE is_active=1 ORDER BY display_order ASC LIMIT 3"
)->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= sanitizeOutput($page_title) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root{
            --primary-yellow:#F9A826;--charcoal:#1A1A1A;--white:#fff;
            --light-gray:#f8f9fa;--medium-gray:#e9ecef;
        }
        body{font-family:'Roboto',sans-serif;color:var(--charcoal);background:var(--white);line-height:1.6;}
        h1,h2,h3,h4,h5,h6{font-family:'Poppins',sans-serif;font-weight:600;}
        .btn-primary{
            background:var(--primary-yellow);border-color:var(--primary-yellow);
            color:var(--charcoal);font-weight:600;padding:12px 30px;border-radius:8px;
            transition:.3s;
        }
        .btn-primary:hover{
            background:#e89a1f;border-color:#e89a1f;color:var(--charcoal);
        }
        .contact-banner{
            height:500px;background:linear-gradient(rgba(26,26,26,.6),rgba(26,26,26,.6)),
            url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80')
            center/cover no-repeat;display:flex;align-items:flex-end;padding:60px 0;color:var(--white);position:relative;
        }
        .contact-banner::before{
            content:'';position:absolute;inset:0;
            background:linear-gradient(135deg,rgba(249,168,38,.1) 0%,transparent 70%);
        }
        .banner-title{font-size:3rem;margin-bottom:20px;line-height:1.2;}
        .banner-subtitle{font-size:1.2rem;opacity:.9;}
        .breadcrumb{background:transparent;padding:0;margin-bottom:20px;}
        .breadcrumb-item a{color:rgba(255,255,255,.8);text-decoration:none;}
        .breadcrumb-item.active{color:var(--primary-yellow);}
        .contact-section{padding:80px 0;}
        .section-title{font-size:1.8rem;margin-bottom:30px;padding-bottom:15px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;}
        .contact-form{
            background:var(--white);border-radius:10px;padding:40px;
            box-shadow:0 5px 15px rgba(0,0,0,.05);
        }
        .form-label{font-weight:600;color:var(--charcoal);margin-bottom:8px;}
        .form-control,.form-select{
            padding:12px 15px;border-radius:5px;border:1px solid #ddd;
            margin-bottom:20px;transition:all .3s;
        }
        .form-control:focus,.form-select:focus{
            border-color:var(--primary-yellow);
            box-shadow:0 0 0 .25rem rgba(249,168,38,.25);
        }
        .contact-info{
            background:var(--charcoal);color:var(--white);border-radius:10px;
            padding:40px;position:relative;
        }
        .contact-info h3{
            color:var(--primary-yellow);margin-bottom:25px;position:relative;
            padding-bottom:15px;
        }
        .contact-info h3::after{
            content:'';position:absolute;bottom:0;left:0;
            width:60px;height:3px;background:var(--primary-yellow);
        }
        .contact-details{list-style:none;padding:0;margin:0;}
        .contact-details li{
            display:flex;align-items:flex-start;padding:15px 0;
            border-bottom:1px solid rgba(255,255,255,.1);
        }
        .contact-details li:last-child{border:none;}
        .contact-details i{
            color:var(--primary-yellow);margin-right:15px;width:20px;
            text-align:center;margin-top:3px;
        }
        .google-maps-section {
            margin-top: 30px;
            padding: 25px;
            background: rgba(255,255,255,0.05);
            border-radius: 12px;
        }
        .google-maps-section h4 {
            color: var(--primary-yellow);
            margin-bottom: 15px;
            font-size: 1.25rem;
        }
        .map-wrapper {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            height: 320px;
            border: 3px solid var(--primary-yellow);
        }
        .map-wrapper iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }
        .directions-btn {
            display: block;
            margin: 20px auto 0;
            background: var(--primary-yellow);
            color: var(--charcoal);
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 50px;
            text-decoration: none;
            width: fit-content;
            transition: .3s;
        }
        .directions-btn:hover {
            background: #e89a1f;
            transform: translateY(-2px);
        }
        .sidebar{
            background:var(--light-gray);border-radius:10px;padding:30px;margin-bottom:30px;
        }
        .sidebar-title{
            font-size:1.2rem;margin-bottom:20px;padding-bottom:10px;
            border-bottom:2px solid var(--primary-yellow);display:inline-block;
        }
        .search-box{position:relative;margin-bottom:30px;}
        .search-box input{
            width:100%;padding:12px 40px 12px 15px;border-radius:50px;border:1px solid #ddd;
        }
        .search-box button{
            position:absolute;right:8px;top:8px;background:var(--primary-yellow);
            border:none;color:var(--charcoal);width:36px;height:36px;border-radius:50%;
            display:flex;align-items:center;justify-content:center;
        }
        .category-list{list-style:none;padding:0;}
        .category-list a{
            display:flex;justify-content:space-between;align-items:center;
            padding:10px 0;color:var(--charcoal);text-decoration:none;border-bottom:1px solid #eee;
        }
        .category-list a:hover,.category-list a.active{
            color:var(--primary-yellow);font-weight:600;
        }
        .floating-buttons{
            position:fixed;bottom:20px;right:20px;z-index:1000;display:flex;flex-direction:column;gap:12px;
        }
        .floating-btn{
            width:56px;height:56px;border-radius:50%;display:flex;
            align-items:center;justify-content:center;color:var(--white);
            font-size:1.5rem;box-shadow:0 5px 15px rgba(0,0,0,.3);
            transition:all .3s;
        }
        .floating-btn:hover{transform:translateY(-5px);}
        .whatsapp-btn{background:#25D366;}
        .call-btn{background:var(--primary-yellow);color:var(--charcoal);}
        .success-message,.error-message{
            padding:15px;border-radius:8px;margin-bottom:20px;
            text-align:center;font-weight:500;
        }
        .success-message{background:#d4edda;color:#155724;border:1px solid #c3e6cb;}
        .error-message{background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;}
        .cta-section{
            background:linear-gradient(135deg,var(--charcoal) 0%,#2d2d2d 100%);
            color:var(--white);padding:80px 0;text-align:center;
        }

        /* FIXED RESPONSIVE BEHAVIOR */
        @media (max-width: 992px) {
            .contact-banner{height:400px;padding:40px 0;}
            .banner-title{font-size:2.5rem;}
            .contact-section .row {
                flex-direction: column; /* Normal order - FORM FIRST */
            }
            /* Form first, then sidebar - natural mobile flow */
            .col-lg-8 { order: 1; }
            .col-lg-4 { order: 2; }

            .contact-form, .contact-info {
                padding: 30px;
            }
            .sticky-top {
                position: static !important; /* Disable sticky on mobile */
            }
            .map-wrapper { height: 300px; }
        }

        @media (max-width: 576px) {
            .banner-title{font-size:2rem;}
            .contact-form, .contact-info {
                padding: 25px;
            }
            .floating-buttons{bottom:15px;right:15px;gap:10px;}
            .floating-btn{width:50px;height:50px;font-size:1.3rem;}
            .map-wrapper{height: 280px;}
            .directions-btn{padding:10px 24px;font-size:0.9rem;}
        }
    </style>
</head>
<body>

<!-- HERO -->
<section class="contact-banner">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>/">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Contact</li>
            </ol>
        </nav>
        <h1 class="banner-title">Contact Us</h1>
        <p class="banner-subtitle">Get a free consultation and quote for your construction project</p>
    </div>
</section>

<!-- MAIN CONTENT -->
<main class="contact-section">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Form - NOW APPEARS FIRST ON MOBILE -->
            <div class="col-lg-8">
                <?php if ($success_message): ?>
                    <div class="success-message"><?= $success_message ?></div>
                <?php endif; ?>
                <?php if ($error_message): ?>
                    <div class="error-message"><?= $error_message ?></div>
                <?php endif; ?>

                <div class="contact-form">
                    <h3 class="mb-4">Request a Free Consultation</h3>
                    <form method="POST" id="consultationForm">
                        <?= getCsrfTokenField() ?>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-control" required value="<?= sanitizeOutput($first_name ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-control" required value="<?= sanitizeOutput($last_name ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required value="<?= sanitizeOutput($email ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone *</label>
                                <input type="tel" name="phone" class="form-control" required value="<?= sanitizeOutput($phone ?? '') ?>">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Project Type</label>
                                <select name="project_type" class="form-select">
                                    <option value="">Select Project Type</option>
                                    <option value="New Home Construction" <?= ($project_type ?? '') === 'New Home Construction' ? 'selected' : '' ?>>New Home Construction</option>
                                    <option value="Home Renovation" <?= ($project_type ?? '') === 'Home Renovation' ? 'selected' : '' ?>>Home Renovation</option>
                                    <option value="Commercial Construction" <?= ($project_type ?? '') === 'Commercial Construction' ? 'selected' : '' ?>>Commercial Construction</option>
                                    <option value="Interior Design" <?= ($project_type ?? '') === 'Interior Design' ? 'selected' : '' ?>>Interior Design</option>
                                    <option value="Other" <?= ($project_type ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Estimated Budget</label>
                                <select name="budget" class="form-select">
                                    <option value="">Select Budget Range</option>
                                    <option value="Under ₹20 Lakhs" <?= ($budget ?? '') === 'Under ₹20 Lakhs' ? 'selected' : '' ?>>Under ₹20 Lakhs</option>
                                    <option value="₹20-40 Lakhs" <?= ($budget ?? '') === '₹20-40 Lakhs' ? 'selected' : '' ?>>₹20-40 Lakhs</option>
                                    <option value="₹40-80 Lakhs" <?= ($budget ?? '') === '₹40-80 Lakhs' ? 'selected' : '' ?>>₹40-80 Lakhs</option>
                                    <option value="₹80 Lakhs - ₹1.5 Crore" <?= ($budget ?? '') === '₹80 Lakhs - ₹1.5 Crore' ? 'selected' : '' ?>>₹80 Lakhs - ₹1.5 Crore</option>
                                    <option value="Above ₹1.5 Crore" <?= ($budget ?? '') === 'Above ₹1.5 Crore' ? 'selected' : '' ?>>Above ₹1.5 Crore</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Project Details *</label>
                                <textarea name="message" class="form-control" rows="5" required placeholder="Describe your project, timeline, special needs..."><?= sanitizeOutput($message ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100">Submit Request</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar + Contact Info -->
            <aside class="col-lg-4">
                <div class="sticky-top" style="top:2rem;">
                    <div class="contact-info mb-4">
                        <h3>Get In Touch</h3>
                        <ul class="contact-details">
                            <li><i class="fas fa-map-marker-alt"></i>
                                <div><strong>Office Address</strong><br>
                                    Grand Jyothi Construction,<br>
                                    123 Construction Plaza, Dharampeth,<br>
                                    Nagpur - 440010, Maharashtra, India</div>
                            </li>
                            <li><i class="fas fa-phone"></i>
                                <div><strong>Phone</strong><br>+91 712 2345678<br>+91 98765 43210</div>
                            </li>
                            <li><i class="fas fa-envelope"></i>
                                <div><strong>Email</strong><br>info@grandjyothi.com<br>projects@grandjyothi.com</div>
                            </li>
                            <li><i class="fas fa-clock"></i>
                                <div><strong>Hours</strong><br>Mon–Fri: 9AM–6PM<br>Sat: 9AM–2PM<br>Sun: Closed</div>
                            </li>
                        </ul>

                        <div class="google-maps-section">
                            <h4>Our Location</h4>
                            <div class="map-wrapper">
                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3722.041903101081!2d79.0813923153313!3d21.105844085948203!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3bd4bf2a6f9a8f0f%3A0x9e5e5e5e5e5e5e5e!2sRakhi%20Construction%20%26%20Consultancy%20Pvt%20Ltd!5e0!3m2!1sen!2sin!4v1630000000000!5m2!1sen!2sin"
                                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                            <a href="https://maps.app.goo.gl/5962M 

                            <a href="https://maps.app.goo.gl/5962MWFdiDSx4h1t6" target="_blank" class="directions-btn">
                                Get Directions
                            </a>
                        </div>
                    </div>

                    <!-- Search & Categories -->
                    <div class="sidebar">
                        <h3 class="sidebar-title">Search Packages</h3>
                        <form action="<?= SITE_URL ?>/packages.php" method="get" class="search-box">
                            <input type="text" name="search" placeholder="Search packages..." value="<?= sanitizeOutput($_GET['search'] ?? '') ?>">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>

                    <div class="sidebar">
                        <h3 class="sidebar-title">Categories</h3>
                        <ul class="category-list">
                            <li><a href="<?= SITE_URL ?>/packages.php" class="<?= empty($_GET['category']) ? 'active' : '' ?>">
                                <span>All Packages</span>
                                <span class="badge bg-dark text-white"><?= $total_packages ?></span>
                            </a></li>
                            <?php foreach ($categories as $c): ?>
                                <li><a href="<?= SITE_URL ?>/packages.php?category=<?= urlencode($c['cat']) ?>"
                                       class="<?= ($_GET['category'] ?? '') === $c['cat'] ? 'active' : '' ?>">
                                    <span><?= ucfirst(sanitizeOutput($c['cat'])) ?></span>
                                    <span class="badge bg-dark text-white"><?= $c['cnt'] ?></span>
                                </a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</main>

<!-- Floating Buttons -->
<div class="floating-buttons">
    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', WHATSAPP_NUMBER) ?>" target="_blank" class="floating-btn whatsapp-btn" title="Chat on WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <a href="tel:<?= preg_replace('/[^0-9+]/', '', PHONE_NUMBER) ?>" class="floating-btn call-btn" title="Call Us">
        <i class="fas fa-phone"></i>
    </a>
</div>

<!-- CTA -->
<section class="cta-section">
    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-lg-8">
                <h2 class="display-5 fw-bold mb-4">Ready to Build Your Dream?</h2>
                <p class="lead mb-4">Let’s discuss your vision and create something extraordinary together</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= SITE_URL ?>/contact.php" class="btn btn-primary btn-lg">Get Free Consultation</a>
                    <a href="<?= SITE_URL ?>/packages.php" class="btn btn-outline-light btn-lg">View All Packages</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<script>
document.getElementById('consultationForm')?.addEventListener('submit', function(e) {
    let ok = true;
    ['first_name','last_name','email','phone','message'].forEach(id => {
        const el = document.querySelector(`[name="${id}"]`);
        if (!el.value.trim()) {
            el.classList.add('is-invalid');
            ok = false;
        } else {
            el.classList.remove('is-invalid');
        }
    });
    if (!ok) e.preventDefault();
});
</script>
</body>
</html>