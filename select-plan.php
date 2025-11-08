<?php
/**
 * Select Plan Page
 * 
 * Enquiry form for selected construction package
 */

declare(strict_types=1);

require_once __DIR__ . '/includes/db.php';

$page_title = 'Select Your Plan';
$success_message = '';
$error_message = '';

// Get selected plan from URL
$selected_plan = $_GET['plan'] ?? '';

// Fetch package details if plan is specified
$package = null;
if ($selected_plan) {
    try {
        $stmt = executeQuery("SELECT * FROM packages WHERE title = ?", [$selected_plan]);
        $package = $stmt->fetch();
        
        if (!$package) {
            $error_message = 'Invalid package selected.';
        } else {
            $page_title = $package['title'] . ' - Select Plan';
        }
    } catch (PDOException $e) {
        error_log('Fetch Package Error: ' . $e->getMessage());
        $error_message = 'Error loading package details.';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $plan = trim($_POST['selected_plan'] ?? '');
    
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
    
    if (empty($plan)) {
        $errors[] = 'Please select a valid plan.';
    }
    
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO contact_messages (name, email, phone, message, selected_plan) VALUES (?, ?, ?, ?, ?)";
            executeQuery($sql, [$name, $email, $phone, $message, $plan]);
            
            $success_message = "Thank you for your interest in the <strong>" . htmlspecialchars($plan) . "</strong> plan! We'll contact you soon.";
            
            // Clear form data
            $name = $email = $phone = $message = '';
        } catch (PDOException $e) {
            error_log('Contact Form Error: ' . $e->getMessage());
            $error_message = 'Error submitting your enquiry. Please try again.';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="/constructioninnagpur/index.php">Home</a>
        <span class="separator">→</span>
        <a href="/constructioninnagpur/packages.php">Packages</a>
        <span class="separator">→</span>
        <span><?= $package ? sanitizeOutput($package['title']) : 'Select Plan' ?></span>
    </div>
</div>

<!-- Hero Section -->
<header class="hero">
    <div class="hero-content">
        <h1><?= $package ? sanitizeOutput($package['title']) : 'Select Your Plan' ?></h1>
        <p>Complete the form below to get started with your construction project</p>
    </div>
</header>

<main class="container section">
    <?php if (!$package): ?>
        <div class="alert alert-error">
            <i data-feather="alert-circle"></i>
            No package selected. Please <a href="/constructioninnagpur/packages.php">choose a package</a> first.
        </div>
    <?php else: ?>
        
        <div class="grid grid-2">
            <!-- Package Details -->
            <div>
                <div class="plan-card">
                    <h2 style="color: var(--primary-blue); margin-bottom: 1rem;">
                        <?= sanitizeOutput($package['title']) ?>
                    </h2>
                    
                    <div style="font-size: 2.5rem; font-weight: 700; color: var(--text-dark); margin: 1.5rem 0;">
                        ₹<?= number_format((float)$package['price_per_sqft'], 0) ?>
                        <span style="font-size: 1rem; font-weight: 400; color: var(--text-gray);">/sqft</span>
                    </div>
                    
                    <?php if ($package['description']): ?>
                        <p style="color: var(--text-gray); margin-bottom: 1.5rem; line-height: 1.6;">
                            <?= sanitizeOutput($package['description']) ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if ($package['features']): ?>
                        <div style="margin-top: 1.5rem;">
                            <h3 style="font-size: 1.125rem; margin-bottom: 1rem; color: var(--text-dark);">
                                <i data-feather="check-circle" style="color: var(--primary-blue);"></i>
                                Included Features
                            </h3>
                            <ul class="feature-list">
                                <?php 
                                $features = explode('|', $package['features']);
                                foreach ($features as $feature): 
                                ?>
                                    <li>
                                        <i data-feather="check" style="color: var(--primary-blue);"></i>
                                        <?= sanitizeOutput(trim($feature)) ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($package['notes']): ?>
                        <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-light); border-radius: var(--radius-md); border-left: 4px solid var(--primary-orange);">
                            <p style="color: var(--text-gray); font-size: 0.95rem; margin: 0;">
                                <i data-feather="info"></i>
                                <?= sanitizeOutput($package['notes']) ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Enquiry Form -->
            <div>
                <div class="card">
                    <h2 style="margin-bottom: 1.5rem;">Request a Quote</h2>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success">
                            <i data-feather="check-circle"></i>
                            <?= $success_message ?>
                        </div>
                        <p style="margin-top: 1rem;">
                            <a href="/constructioninnagpur/packages.php" class="btn btn-secondary">
                                <i data-feather="arrow-left"></i> View Other Packages
                            </a>
                        </p>
                    <?php else: ?>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-error">
                                <i data-feather="alert-circle"></i>
                                <?= $error_message ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <input type="hidden" name="selected_plan" value="<?= sanitizeOutput($package['title']) ?>">
                            
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
                                <label for="message" class="form-label">Additional Requirements (Optional)</label>
                                <textarea id="message" 
                                          name="message" 
                                          class="form-textarea"
                                          rows="4" 
                                          placeholder="Tell us about your project, plot size, timeline, or any specific requirements..."><?= isset($message) ? sanitizeOutput($message) : '' ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="width: 100%;">
                                <i data-feather="send"></i> Submit Enquiry
                            </button>
                            
                            <p style="text-align: center; margin-top: 1rem; font-size: 0.9rem; color: var(--text-gray);">
                                <a href="/constructioninnagpur/packages.php" style="color: var(--primary-blue);">
                                    <i data-feather="arrow-left"></i> Back to Packages
                                </a>
                            </p>
                        </form>
                        
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
