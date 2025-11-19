<?php
/**
 * Admin Dashboard
 * Displays statistics and quick links
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config.php'; // SITE_URL defined here

requireAdmin();

$page_title = 'Dashboard';

// Fetch statistics
$stats = [
    'services'     => executeQuery("SELECT COUNT(*) as count FROM services")->fetch()['count'],
    'projects'     => executeQuery("SELECT COUNT(*) as count FROM projects")->fetch()['count'],
    'packages'     => executeQuery("SELECT COUNT(*) as count FROM packages WHERE is_active = 1")->fetch()['count'],
    'team'         => executeQuery("SELECT COUNT(*) as count FROM team")->fetch()['count'],
    'testimonials' => executeQuery("SELECT COUNT(*) as count FROM testimonials")->fetch()['count'],
    'messages'     => executeQuery("SELECT COUNT(*) as count FROM contact_messages")->fetch()['count'],
];

// Fetch recent messages
$recent_messages = executeQuery("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

require_once __DIR__ . '/includes/admin_header.php';
?>

<div class="content-header">
    <h1>Dashboard</h1>
    <p>Welcome back, <?= sanitizeOutput($_SESSION['admin_username']) ?>!</p>
</div>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i data-feather="briefcase"></i>
        </div>
        <div class="stat-info">
            <h3><?= $stats['services'] ?></h3>
            <p>Services</p>
            <a href="<?= SITE_URL ?>/admin/services.php" class="stat-link">Manage</a>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i data-feather="folder"></i>
        </div>
        <div class="stat-info">
            <h3><?= $stats['projects'] ?></h3>
            <p>Projects</p>
            <a href="<?= SITE_URL ?>/admin/projects.php" class="stat-link">Manage</a>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i data-feather="package"></i>
        </div>
        <div class="stat-info">
            <h3><?= $stats['packages'] ?></h3>
            <p>Active Packages</p>
            <a href="<?= SITE_URL ?>/admin/packages.php" class="stat-link">Manage</a>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon blue">
            <i data-feather="users"></i>
        </div>
        <div class="stat-info">
            <h3><?= $stats['team'] ?></h3>
            <p>Team Members</p>
            <a href="<?= SITE_URL ?>/admin/team.php" class="stat-link">Manage</a>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i data-feather="message-square"></i>
        </div>
        <div class="stat-info">
            <h3><?= $stats['testimonials'] ?></h3>
            <p>Testimonials</p>
            <a href="<?= SITE_URL ?>/admin/testimonials.php" class="stat-link">Manage</a>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon red">
            <i data-feather="mail"></i>
        </div>
        <div class="stat-info">
            <h3><?= $stats['messages'] ?></h3>
            <p>Messages</p>
            <a href="<?= SITE_URL ?>/admin/messages.php" class="stat-link">View</a>
        </div>
    </div>
</div>

<!-- Recent Messages -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Recent Contact Messages</h2>
    </div>
    
    <?php if (empty($recent_messages)): ?>
        <p class="text-center text-muted py-4">No messages yet.</p>
    <?php else: ?>
        <div class="table-container">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_messages as $msg): ?>
                        <tr>
                            <td>
    <?= sanitizeOutput(trim(($msg['first_name'] ?? '') . ' ' . ($msg['last_name'] ?? ''))) ?>
</td>

                            <td><?= sanitizeOutput($msg['email']) ?></td>
                            <td><?= sanitizeOutput($msg['phone'] ?? '-') ?></td>
                            <td><?= sanitizeOutput(substr($msg['message'], 0, 60)) ?>...</td>
                            <td><?= date('M d, Y', strtotime($msg['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-4">
            <a href="<?= SITE_URL ?>/admin/messages.php" class="btn btn-primary">
                View All Messages
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header">
        <h2 class="card-title">Quick Actions</h2>
    </div>
    <div class="btn-group">
        <a href="<?= SITE_URL ?>/admin/services.php?action=add" class="btn btn-primary">
            Add Service
        </a>
        <a href="<?= SITE_URL ?>/admin/projects.php?action=add" class="btn btn-primary">
            Add Project
        </a>
        <a href="<?= SITE_URL ?>/admin/packages.php?action=add" class="btn btn-primary">
            Add Package
        </a>
        <a href="<?= SITE_URL ?>/admin/team.php?action=add" class="btn btn-primary">
            Add Team Member
        </a>
        <a href="<?= SITE_URL ?>/admin/testimonials.php?action=add" class="btn btn-primary">
            Add Testimonial
        </a>
    </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>