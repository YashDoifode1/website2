<?php
/**
 * Contact Page
 * 
 * Contact form for inquiries and messages
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/security.php';

$page_title = 'Contact Us';
$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid security token. Please try again.';
        logSecurityEvent('CSRF token validation failed on contact form', 'WARNING');
    } 
    // Check rate limiting
    elseif (!checkRateLimit('contact_form', 5, 300)) {
        $remaining = getRateLimitRemaining('contact_form', 300);
        $error_message = 'Too many submissions. Please try again in ' . ceil($remaining / 60) . ' minutes.';
        logSecurityEvent('Rate limit exceeded on contact form', 'WARNING');
    } else {
        // Sanitize inputs
        $name = sanitizeInput(trim($_POST['name'] ?? ''));
        $email = sanitizeInput(trim($_POST['email'] ?? ''));
        $phone = sanitizeInput(trim($_POST['phone'] ?? ''));
        $message = sanitizeInput(trim($_POST['message'] ?? ''));
        
        // Validation
        $errors = [];
    
    if (empty($name) || strlen($name) < 2) {
        $errors[] = 'Please enter a valid name.';
    }
    
    if (empty($email) || !isValidEmail($email)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    if (empty($phone) || strlen($phone) < 10) {
        $errors[] = 'Please enter a valid phone number.';
    }
    
    if (empty($message) || strlen($message) < 10) {
        $errors[] = 'Please enter a message (at least 10 characters).';
    }
    
        if (empty($errors)) {
            try {
                $sql = "INSERT INTO contact_messages (name, email, phone, message) VALUES (?, ?, ?, ?)";
                executeQuery($sql, [$name, $email, $phone, $message]);
                
                $success_message = 'Thank you for contacting us! We will get back to you shortly.';
                logSecurityEvent('Contact form submitted successfully', 'INFO');
                
                // Clear form data
                $name = $email = $phone = $message = '';
            } catch (PDOException $e) {
                error_log('Contact Form Error: ' . $e->getMessage());
                logSecurityEvent('Contact form database error: ' . $e->getMessage(), 'ERROR');
                $error_message = 'An error occurred while submitting your message. Please try again.';
            }
        } else {
            $error_message = implode('<br>', $errors);
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<header class="hero">
    <div class="hero-content">
        <h1>Contact Us</h1>
        <p>Get in touch with us for your construction needs</p>
    </div>
</header>

<main class="container section">
    <section>
        <div class="grid grid-2">
            <!-- Contact Form -->
            <div class="card">
                <h2 style="margin-bottom: 1.5rem;">Send Us a Message</h2>
                
                <?php if ($success_message): ?>
                    <div class="alert alert-success">
                        <i data-feather="check-circle"></i>
                        <?= sanitizeOutput($success_message) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-error">
                        <i data-feather="alert-circle"></i>
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <?= getCsrfTokenField() ?>
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name *</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               class="form-input"
                               placeholder="Enter your full name" 
                               value="<?= isset($name) ? sanitizeOutput($name) : '' ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-input"
                               placeholder="your.email@example.com" 
                               value="<?= isset($email) ? sanitizeOutput($email) : '' ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Phone Number *</label>
                        <input type="tel" 
                               id="phone" 
                               name="phone" 
                               class="form-input"
                               placeholder="+91 98765 43210" 
                               value="<?= isset($phone) ? sanitizeOutput($phone) : '' ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Your Message *</label>
                        <textarea id="message" 
                                  name="message" 
                                  class="form-textarea"
                                  rows="6" 
                                  placeholder="Tell us about your project requirements..." 
                                  required><?= isset($message) ? sanitizeOutput($message) : '' ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i data-feather="send"></i> Send Message
                    </button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div>
                <h2>Contact Information</h2>
                
                <article class="card">
                    <h4><i data-feather="map-pin"></i> Office Address</h4>
                    <p>
                        Grand Jyothi Construction<br>
                        123 Construction Plaza<br>
                        Dharampeth, Nagpur - 440010<br>
                        Maharashtra, India
                    </p>
                </article>
                
                <article class="card">
                    <h4><i data-feather="phone"></i> Phone</h4>
                    <p>
                        <strong>Office:</strong> +91 712 2345678<br>
                        <strong>Mobile:</strong> +91 98765 43210<br>
                        <strong>WhatsApp:</strong> +91 98765 43210
                    </p>
                </article>
                
                <article class="card">
                    <h4><i data-feather="mail"></i> Email</h4>
                    <p>
                        <strong>General Inquiries:</strong><br>
                        info@grandjyothi.com<br><br>
                        <strong>Project Inquiries:</strong><br>
                        projects@grandjyothi.com
                    </p>
                </article>
                
                <article class="card">
                    <h4><i data-feather="clock"></i> Business Hours</h4>
                    <p>
                        <strong>Monday - Friday:</strong> 9:00 AM - 6:00 PM<br>
                        <strong>Saturday:</strong> 9:00 AM - 2:00 PM<br>
                        <strong>Sunday:</strong> Closed
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section>
        <h2>Find Us on Map</h2>
        <div style="border: 1px solid var(--muted-border-color); border-radius: 8px; overflow: hidden; height: 400px; background-color: var(--card-background-color); display: flex; align-items: center; justify-content: center;">
            <p style="color: var(--muted-color);">
                <i data-feather="map"></i><br>
                Map integration would be placed here<br>
                (Google Maps or similar service)
            </p>
        </div>
    </section>

    <!-- FAQ Section -->
    <section>
        <h2>Frequently Asked Questions</h2>
        <details>
            <summary>What types of projects do you handle?</summary>
            <p>
                We handle a wide range of construction projects including residential homes, 
                commercial buildings, industrial facilities, interior design, and renovation projects.
            </p>
        </details>
        
        <details>
            <summary>How long does a typical project take?</summary>
            <p>
                Project timelines vary based on scope and complexity. A typical residential project 
                takes 6-12 months, while commercial projects may take 12-24 months. We provide 
                detailed timelines during the consultation phase.
            </p>
        </details>
        
        <details>
            <summary>Do you provide free consultations?</summary>
            <p>
                Yes! We offer free initial consultations to discuss your project requirements, 
                budget, and timeline. Contact us to schedule a meeting.
            </p>
        </details>
        
        <details>
            <summary>Are you licensed and insured?</summary>
            <p>
                Yes, Grand Jyothi Construction is fully licensed and insured. We maintain all 
                necessary certifications and comply with local building codes and regulations.
            </p>
        </details>
        
        <details>
            <summary>What is your payment structure?</summary>
            <p>
                We follow a milestone-based payment structure with transparent pricing. Typically, 
                payments are divided into stages: advance, foundation, structure, finishing, and 
                final handover. Detailed payment terms are provided in the contract.
            </p>
        </details>
    </section>

    <!-- Call to Action -->
    <section style="text-align: center; padding: 3rem 0;">
        <h2>Ready to Start Your Project?</h2>
        <p>Let's discuss how we can help bring your construction vision to life.</p>
        <a href="/constructioninnagpur/projects.php" role="button" class="secondary">View Our Projects</a>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
