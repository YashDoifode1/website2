<?php
/**
 * Select Plan Page - Modern Design
 * 
 * Enquiry form for selected construction package with modern UI
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Select Your Plan | BuildDream Construction';
$success_message = '';
$error_message = '';

// Get selected plan from URL
$selected_plan = $_GET['plan'] ?? '';

// Fetch package details
$package = null;
if ($selected_plan) {
    try {
        $stmt = executeQuery("SELECT * FROM packages WHERE title = ? AND is_active = 1", [$selected_plan]);
        $package = $stmt->fetch();
        
        if (!$package) {
            $error_message = 'Invalid or unavailable package selected.';
        } else {
            $page_title = $package['title'] . ' - Select Plan | BuildDream Construction';
        }
    } catch (PDOException $e) {
        error_log('Fetch Package Error: ' . $e->getMessage());
        $error_message = 'Error loading package details.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid security token. Please try again.';
        logSecurityEvent('CSRF token validation failed on select-plan form', 'WARNING');
    } 
    // Rate limiting
    elseif (!checkRateLimit('select_plan_form', 5, 300)) {
        $remaining = getRateLimitRemaining('select_plan_form', 300);
        $error_message = 'Too many submissions. Please try again in ' . ceil($remaining / 60) . ' minutes.';
        logSecurityEvent('Rate limit exceeded on select-plan form', 'WARNING');
    } else {
        $name = sanitizeInput(trim($_POST['name'] ?? ''));
        $email = sanitizeInput(trim($_POST['email'] ?? ''));
        $phone = sanitizeInput(trim($_POST['phone'] ?? ''));
        $message = sanitizeInput(trim($_POST['message'] ?? ''));
        $plan = sanitizeInput(trim($_POST['selected_plan'] ?? ''));
        
        $errors = [];
        
        if (empty($name) || strlen($name) < 2) $errors[] = 'Please enter a valid name.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
        if (empty($phone) || !preg_match('/^\+?\d{10,15}$/', $phone)) $errors[] = 'Please enter a valid phone number.';
        if (empty($plan)) $errors[] = 'Invalid plan selected.';
        
        if (empty($errors)) {
            try {
                $sql = "INSERT INTO contact_messages 
                        (name, email, phone, message, selected_plan, submitted_at) 
                        VALUES (?, ?, ?, ?, ?, NOW())";
                executeQuery($sql, [$name, $email, $phone, $message, $plan]);
                
                $success_message = "Thank you for your interest in the <strong>" . htmlspecialchars($plan) . "</strong> plan! We'll contact you within 24 hours.";
                logSecurityEvent("Plan enquiry submitted: $plan", 'INFO');
                
                // Clear form
                $name = $email = $phone = $message = '';
            } catch (PDOException $e) {
                error_log('Plan Enquiry Error: ' . $e->getMessage());
                $error_message = 'Error submitting your enquiry. Please try again.';
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&family=Open+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-yellow: #F9A826;
            --charcoal: #1A1A1A;
            --white: #FFFFFF;
            --light-gray: #f8f9fa;
        }
       
        body {
            font-family: 'Open Sans', sans-serif;
            color: var(--charcoal);
            background-color: var(--light-gray);
            line-height: 1.6;
        }
       
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
        }
       
        .btn-primary {
            background-color: var(--primary-yellow);
            border-color: var(--primary-yellow);
            color: var(--charcoal);
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 8px;
        }
       
        .btn-primary:hover {
            background-color: #e89a1f;
            border-color: #e89a1f;
            color: var(--charcoal);
        }
       
        .btn-outline-primary {
            border-color: var(--primary-yellow);
            color: var(--primary-yellow);
            font-weight: 600;
            padding: 10px 25px;
            border-radius: 8px;
        }
       
        .btn-outline-primary:hover {
            background-color: var(--primary-yellow);
            border-color: var(--primary-yellow);
            color: var(--charcoal);
        }
       
        .navbar {
            background-color: var(--charcoal);
            padding: 15px 0;
        }
       
        .navbar-brand {
            font-family: 'Montserrat', sans-serif;
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--primary-yellow) !important;
        }
       
        .nav-link {
            color: var(--white) !important;
            font-weight: 500;
            margin: 0 10px;
        }
       
        .nav-link:hover, .nav-link.active {
            color: var(--primary-yellow) !important;
        }
       
        .page-header {
            background: linear-gradient(rgba(26, 26, 26, 0.8), rgba(26, 26, 26, 0.8)),
                        url('https://images.unsplash.com/photo-1541888946425-d81bb19240f5?ixlib=rb-4.0.3&auto=format&fit=crop&w=1770&q=80') no-repeat center center;
            background-size: cover;
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }
       
        .page-header h1 {
            font-size: 2.8rem;
            margin-bottom: 15px;
        }
       
        .breadcrumb {
            background-color: transparent;
            padding: 1rem 0;
            margin-bottom: 0;
        }
       
        .breadcrumb-item a {
            color: var(--primary-yellow);
            text-decoration: none;
        }
       
        .breadcrumb-item a:hover {
            text-decoration: underline;
        }
       
        .breadcrumb-item.active {
            color: var(--white);
            font-weight: 500;
        }
       
        .plan-section {
            padding: 80px 0;
        }
       
        .plan-card {
            background-color: var(--white);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
            border: 1px solid #eee;
        }
       
        .plan-header {
            text-align: center;
            margin-bottom: 25px;
        }
       
        .plan-title {
            font-size: 1.8rem;
            margin-bottom: 10px;
            color: var(--charcoal);
        }
       
        .plan-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-yellow);
            margin: 20px 0;
        }
       
        .plan-price .unit {
            font-size: 1rem;
            color: #666;
            font-weight: 400;
        }
       
        .plan-description {
            color: #666;
            margin-bottom: 25px;
            line-height: 1.7;
        }
       
        .plan-features {
            margin: 25px 0;
        }
       
        .plan-features h4 {
            color: var(--charcoal);
            margin-bottom: 15px;
            font-size: 1.1rem;
        }
       
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
       
        .feature-list li {
            padding: 8px 0;
            display: flex;
            align-items: flex-start;
            color: #555;
            font-size: 0.95rem;
        }
       
        .feature-list li i {
            color: var(--primary-yellow);
            margin-right: 10px;
            width: 20px;
            flex-shrink: 0;
            margin-top: 2px;
        }
       
        .plan-notes {
            background-color: #f8f9fa;
            border-left: 4px solid var(--primary-yellow);
            padding: 15px;
            margin-top: 25px;
            border-radius: 0 8px 8px 0;
            font-size: 0.9rem;
            color: #555;
        }
       
        .plan-notes i {
            color: var(--primary-yellow);
            margin-right: 8px;
        }
       
        .enquiry-form {
            background-color: var(--white);
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
        }
       
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--charcoal);
        }
       
        .form-control, .form-select {
            padding: 12px 15px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
       
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-yellow);
            box-shadow: 0 0 0 0.25rem rgba(249, 168, 38, 0.25);
        }
       
        .form-control.is-invalid {
            border-color: #dc3545;
        }
       
        .invalid-feedback {
            display: none;
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: -15px;
            margin-bottom: 15px;
        }
       
        .success-message, .error-message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
       
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
       
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
       
        .back-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary-yellow);
            text-decoration: none;
            font-weight: 500;
            margin-top: 15px;
        }
       
        .back-link i {
            margin-right: 8px;
        }
       
        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 60px 0;
            }
            .page-header h1 {
                font-size: 2.2rem;
            }
            .plan-section {
                padding: 50px 0;
            }
            .plan-card, .enquiry-form {
                padding: 20px;
            }
            .plan-price {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>

    <!-- Navigation -->


    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1><?= $package ? sanitizeOutput($package['title']) : 'Select Your Plan' ?></h1>
            <p class="lead">Complete the form below to get started with your construction project</p>
        </div>
    </section>

    <!-- Breadcrumb -->
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/constructioninnagpur/index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="/constructioninnagpur/packages.php">Packages</a></li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?= $package ? sanitizeOutput($package['title']) : 'Select Plan' ?>
                </li>
            </ol>
        </nav>
    </div>

    <!-- Plan Section -->
    <section class="plan-section">
        <div class="container">
            <?php if (!$package): ?>
                <div class="text-center py-5">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        No package selected. Please <a href="/constructioninnagpur/packages.php" class="alert-link">choose a package</a> first.
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <!-- Package Details -->
                    <div class="col-lg-6 mb-4 mb-lg-0">
                        <div class="plan-card">
                            <div class="plan-header">
                                <h2 class="plan-title"><?= sanitizeOutput($package['title']) ?></h2>
                                <div class="plan-price">
                                    <?php if ($package['price_per_sqft'] > 0): ?>
                                        ₹<?= number_format((float)$package['price_per_sqft']) ?>
                                        <span class="unit">/sq.ft</span>
                                    <?php else: ?>
                                        Custom Quote
                                    <?php endif; ?>
                                </div>
                            </div>

                            <?php if ($package['description']): ?>
                                <p class="plan-description"><?= sanitizeOutput($package['description']) ?></p>
                            <?php endif; ?>

                            <?php if ($package['features']): ?>
                                <div class="plan-features">
                                    <h4>Included Features</h4>
                                    <ul class="feature-list">
                                        <?php 
                                        $features = explode('|', $package['features']);
                                        foreach ($features as $feature): 
                                            $feature = trim($feature);
                                            if (empty($feature)) continue;
                                        ?>
                                            <li><i class="fas fa-check"></i> <?= sanitizeOutput($feature) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($package['notes'])): ?>
                                <div class="plan-notes">
                                    <i class="fas fa-info-circle"></i>
                                    <?= sanitizeOutput($package['notes']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Enquiry Form -->
                    <div class="col-lg-6">
                        <div class="enquiry-form">
                            <h3 class="mb-4">Request a Quote</h3>

                            <?php if ($success_message): ?>
                                <div class="success-message">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <?= $success_message ?>
                                </div>
                                <div class="text-center">
                                    <a href="/constructioninnagpur/packages.php" class="btn btn-outline-primary">
                                        View Other Packages
                                    </a>
                                </div>
                            <?php else: ?>

                                <?php if ($error_message): ?>
                                    <div class="error-message">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        <?= $error_message ?>
                                    </div>
                                <?php endif; ?>

                                <form method="POST" action="" id="enquiryForm">
                                    <?= getCsrfTokenField() ?>
                                    <input type="hidden" name="selected_plan" value="<?= sanitizeOutput($package['title']) ?>">

                                    <div class="mb-3">
                                        <label for="name" class="form-label">Full Name *</label>
                                        <input type="text" name="name" class="form-control" id="name" 
                                               value="<?= sanitizeOutput($name ?? '') ?>" required>
                                        <div class="invalid-feedback">Please enter your full name.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" name="email" class="form-control" id="email" 
                                               value="<?= sanitizeOutput($email ?? '') ?>" required>
                                        <div class="invalid-feedback">Please enter a valid email.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number *</label>
                                        <input type="tel" name="phone" class="form-control" id="phone" 
                                               value="<?= sanitizeOutput($phone ?? '') ?>" required>
                                        <div class="invalid-feedback">Please enter a valid phone number.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="message" class="form-label">Additional Requirements (Optional)</label>
                                        <textarea name="message" class="form-control" id="message" rows="4" 
                                                  placeholder="Tell us about your project, plot size, timeline, or any specific needs..."><?= sanitizeOutput($message ?? '') ?></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100">Submit Enquiry</button>

                                    <a href="/constructioninnagpur/packages.php" class="back-link d-block text-center mt-3">
                                        Back to Packages
                                    </a>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Client-side validation
        document.getElementById('enquiryForm')?.addEventListener('submit', function(e) {
            let isValid = true;
            const required = ['name', 'email', 'phone'];
            
            required.forEach(id => {
                const field = document.getElementById(id);
                const feedback = field.nextElementSibling;
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    feedback.style.display = 'block';
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                    feedback.style.display = 'none';
                }
            });

            // Email format
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email.value && !emailRegex.test(email.value)) {
                email.classList.add('is-invalid');
                email.nextElementSibling.textContent = 'Please enter a valid email address.';
                email.nextElementSibling.style.display = 'block';
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });

        // Clear validation on input
        document.querySelectorAll('#enquiryForm input, #enquiryForm textarea').forEach(input => {
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                    this.nextElementSibling.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>