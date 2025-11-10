<?php
/**
 * Contact Page - Grand Jyothi Construction
 * HEADER: Uniform with index.php (BuildDream Theme)
 * PAGE DESIGN: 100% as per your original code
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Contact Us | BuildDream Construction';
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid security token. Please try again.';
        logSecurityEvent('CSRF token validation failed on contact form', 'WARNING');
    } 
    elseif (!checkRateLimit('contact_form', 5, 300)) {
        $remaining = getRateLimitRemaining('contact_form', 300);
        $error_message = 'Too many submissions. Please try again in ' . ceil($remaining / 60) . ' minutes.';
        logSecurityEvent('Rate limit exceeded on contact form', 'WARNING');
    } else {
        $first_name = sanitizeInput(trim($_POST['first_name'] ?? ''));
        $last_name = sanitizeInput(trim($_POST['last_name'] ?? ''));
        $email = sanitizeInput(trim($_POST['email'] ?? ''));
        $phone = sanitizeInput(trim($_POST['phone'] ?? ''));
        $project_type = sanitizeInput(trim($_POST['project_type'] ?? ''));
        $budget = sanitizeInput(trim($_POST['budget'] ?? ''));
        $message = sanitizeInput(trim($_POST['message'] ?? ''));
        
        $errors = [];
    
        if (empty($first_name) || strlen($first_name) < 2) $errors[] = 'Please enter a valid first name.';
        if (empty($last_name) || strlen($last_name) < 2) $errors[] = 'Please enter a valid last name.';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
        if (empty($phone) || !preg_match('/^\+?\d{10,15}$/', $phone)) $errors[] = 'Please enter a valid phone number.';
        if (empty($message) || strlen($message) < 20) $errors[] = 'Please provide a detailed message (at least 20 characters).';
    
        if (empty($errors)) {
            try {
                $sql = "INSERT INTO contact_messages 
                        (first_name, last_name, email, phone, project_type, budget, message, submitted_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
                executeQuery($sql, [$first_name, $last_name, $email, $phone, $project_type, $budget, $message]);
                
                $success_message = 'Thank you! Your consultation request has been submitted. We\'ll contact you within 24 hours.';
                logSecurityEvent('Contact form submitted successfully', 'INFO');
                
                $first_name = $last_name = $email = $phone = $project_type = $budget = $message = '';
            } catch (PDOException $e) {
                error_log('Contact Form Error: ' . $e->getMessage());
                logSecurityEvent('Contact form database error: ' . $e->getMessage(), 'ERROR');
                $error_message = 'An error occurred. Please try again later.';
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

require_once __DIR__ . '/includes/header.php'; // Uniform BuildDream Header
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
    
    <!-- YOUR ORIGINAL STYLES – UNTOUCHED -->
    <style>
        :root {
            --primary-yellow: #F9A826;
            --charcoal: #1A1A1A;
            --white: #FFFFFF;
            --light-gray: #f8f9fa;
        }
       
        body {
            font-family: 'ROBOTO', sans-serif;
            color: var(--charcoal);
            background-color: var(--white);
            line-height: 1.6;
            padding-top: 80px; /* For fixed header */
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
        }
       
        .btn-primary:hover {
            background-color: #e89a1f;
            border-color: #e89a1f;
            color: var(--charcoal);
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
       
        .contact-section {
            padding: 80px 0;
        }
       
        .contact-form {
            background-color: var(--white);
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            height: 100%;
        }
       
        .form-label {
            font-weight: 600;
            margin-bottom: 8px;
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
       
        .contact-info {
            background-color: var(--charcoal);
            color: var(--white);
            border-radius: 10px;
            padding: 40px;
            height: 100%;
        }
       
        .contact-info h3 {
            color: var(--primary-yellow);
            margin-bottom: 25px;
            position: relative;
            padding-bottom: 15px;
        }
       
        .contact-info h3:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: var(--primary-yellow);
        }
       
        .contact-details {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 30px;
        }
       
        .contact-details li {
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: flex-start;
        }
       
        .contact-details li:last-child {
            border-bottom: none;
        }
       
        .contact-details li i {
            color: var(--primary-yellow);
            margin-right: 15px;
            width: 20px;
            text-align: center;
            margin-top: 3px;
        }
       
        .map-container {
            border-radius: 10px;
            overflow: hidden;
            height: 300px;
            margin-top: 30px;
        }
       
        .map-placeholder {
            background-color: #e9ecef;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
        }
       
        .floating-buttons {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }
       
        .floating-btn {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.5rem;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: all 0.3s ease;
        }
       
        .floating-btn:hover {
            transform: translateY(-5px);
            color: var(--white);
        }
       
        .whatsapp-btn {
            background-color: #25D366;
        }
       
        .call-btn {
            background-color: var(--primary-yellow);
            color: var(--charcoal);
        }
       
        .success-message {
            display: none;
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
            text-align: center;
            font-weight: 500;
        }
       
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 60px 0;
            }
            .page-header h1 {
                font-size: 2.2rem;
            }
            .contact-section {
                padding: 50px 0;
            }
            .floating-buttons {
                bottom: 20px;
                right: 20px;
            }
            .floating-btn {
                width: 50px;
                height: 50px;
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Contact Us</h1>
            <p class="lead">Get in touch with our team for a free consultation and quote for your construction project</p>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section">
        <div class="container">
            <div class="row">
                <!-- Contact Form -->
                <div class="col-lg-7 mb-5 mb-lg-0">
                    <div class="contact-form">
                        <h3 class="mb-4">Request a Free Consultation</h3>

                        <?php if ($error_message): ?>
                            <div class="error-message">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?= $error_message ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="" id="consultationForm">
                            <?= getCsrfTokenField() ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First Name *</label>
                                        <input type="text" name="first_name" class="form-control" id="first_name" 
                                               value="<?= sanitizeOutput($first_name ?? '') ?>" required>
                                        <div class="invalid-feedback">Please provide your first name.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">Last Name *</label>
                                        <input type="text" name="last_name" class="form-control" id="last_name" 
                                               value="<?= sanitizeOutput($last_name ?? '') ?>" required>
                                        <div class="invalid-feedback">Please provide your last name.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email Address *</label>
                                        <input type="email" name="email" class="form-control" id="email" 
                                               value="<?= sanitizeOutput($email ?? '') ?>" required>
                                        <div class="invalid-feedback">Please provide a valid email address.</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number *</label>
                                        <input type="tel" name="phone" class="form-control" id="phone" 
                                               value="<?= sanitizeOutput($phone ?? '') ?>" required>
                                        <div class="invalid-feedback">Please provide a valid phone number.</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="project_type" class="form-label">Project Type</label>
                                <select name="project_type" class="form-select" id="project_type">
                                    <option value="">Select Project Type</option>
                                    <option value="New Home Construction" <?= ($project_type ?? '') === 'New Home Construction' ? 'selected' : '' ?>>New Home Construction</option>
                                    <option value="Home Renovation" <?= ($project_type ?? '') === 'Home Renovation' ? 'selected' : '' ?>>Home Renovation</option>
                                    <option value="Commercial Construction" <?= ($project_type ?? '') === 'Commercial Construction' ? 'selected' : '' ?>>Commercial Construction</option>
                                    <option value="Interior Design" <?= ($project_type ?? '') === 'Interior Design' ? 'selected' : '' ?>>Interior Design</option>
                                    <option value="Other" <?= ($project_type ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="budget" class="form-label">Estimated Budget</label>
                                <select name="budget" class="form-select" id="budget">
                                    <option value="">Select Budget Range</option>
                                    <option value="Under ₹20 Lakhs" <?= ($budget ?? '') === 'Under ₹20 Lakhs' ? 'selected' : '' ?>>Under ₹20 Lakhs</option>
                                    <option value="₹20-40 Lakhs" <?= ($budget ?? '') === '₹20-40 Lakhs' ? 'selected' : '' ?>>₹20-40 Lakhs</option>
                                    <option value="₹40-80 Lakhs" <?= ($budget ?? '') === '₹40-80 Lakhs' ? 'selected' : '' ?>>₹40-80 Lakhs</option>
                                    <option value="₹80 Lakhs - ₹1.5 Crore" <?= ($budget ?? '') === '₹80 Lakhs - ₹1.5 Crore' ? 'selected' : '' ?>>₹80 Lakhs - ₹1.5 Crore</option>
                                    <option value="Above ₹1.5 Crore" <?= ($budget ?? '') === 'Above ₹1.5 Crore' ? 'selected' : '' ?>>Above ₹1.5 Crore</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Project Details *</label>
                                <textarea name="message" class="form-control" id="message" rows="5" 
                                          placeholder="Please describe your project requirements, timeline, and any specific needs..." required><?= sanitizeOutput($message ?? '') ?></textarea>
                                <div class="invalid-feedback">Please provide details about your project.</div>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100">Submit Request</button>

                            <div class="success-message" id="successMessage" style="display: <?= $success_message ? 'block' : 'none' ?>;">
                                <i class="fas fa-check-circle me-2"></i> <?= $success_message ?>
                            </div>
                        </form>
                    </div>
                </div>
               
                <!-- Contact Info & Map -->
                <div class="col-lg-5">
                    <div class="contact-info">
                        <h3>Get In Touch</h3>
                        <ul class="contact-details">
                            <li>
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <strong>Office Address</strong><br>
                                    Grand Jyothi Construction,<br>
                                    123 Construction Plaza, Dharampeth,<br>
                                    Nagpur - 440010, Maharashtra, India
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-phone"></i>
                                <div>
                                    <strong>Phone Number</strong><br>
                                    +91 712 2345678<br>
                                    +91 98765 43210
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <strong>Email Address</strong><br>
                                    info@grandjyothi.com<br>
                                    projects@grandjyothi.com
                                </div>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Working Hours</strong><br>
                                    Monday - Friday: 9:00 AM - 6:00 PM<br>
                                    Saturday: 9:00 AM - 2:00 PM<br>
                                    Sunday: Closed
                                </div>
                            </li>
                        </ul>
                       
                        <div class="map-container">
                            <div class="map-placeholder">
                                <div class="text-center">
                                    <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                                    <p>Google Maps Integration</p>
                                    <small>Location: Nagpur, Maharashtra</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Floating Action Buttons -->
    <div class="floating-buttons">
        <a href="https://wa.me/919876543210" class="floating-btn whatsapp-btn" target="_blank" title="Chat on WhatsApp">
            <i class="fab fa-whatsapp"></i>
        </a>
        <a href="tel:+919876543210" class="floating-btn call-btn" title="Call Us">
            <i class="fas fa-phone"></i>
        </a>
    </div>

    <?php require_once __DIR__ . '/includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        <?php if ($success_message): ?>
        setTimeout(() => {
            document.getElementById('successMessage').style.display = 'none';
        }, 10000);
        <?php endif; ?>
    </script>
</body>
</html>