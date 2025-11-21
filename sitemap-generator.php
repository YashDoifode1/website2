<?php
/**
 * Sitemap Generator
 * Run this script to generate/update sitemap.xml
 * Access via: https://yourdomain.com/sitemap-generator.php
 */

// Disable error display in production
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Set timezone
date_default_timezone_set('Asia/Kolkata');

// Load required files
require_once __DIR__ . '/config.php';

// Function to generate sitemap XML
function generateSitemap() {
    // Initialize XML writer
    $xml = new XMLWriter();
    $xml->openMemory();
    $xml->setIndent(true);
    
    // Start document
    $xml->startDocument('1.0', 'UTF-8');
    
    // Start urlset
    $xml->startElement('urlset');
    $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $xml->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $xml->writeAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');

    // Static pages
    $pages = [
        '' => ['changefreq' => 'daily', 'priority' => '1.0'],
        'about.php' => ['changefreq' => 'weekly', 'priority' => '0.9'],
        'services.php' => ['changefreq' => 'weekly', 'priority' => '0.9'],
        'projects.php' => ['changefreq' => 'daily', 'priority' => '0.9'],
        'packages.php' => ['changefreq' => 'weekly', 'priority' => '0.8'],
        'blog.php' => ['changefreq' => 'daily', 'priority' => '0.8'],
        'team.php' => ['changefreq' => 'weekly', 'priority' => '0.7'],
        'testimonials.php' => ['changefreq' => 'weekly', 'priority' => '0.7'],
        'contact.php' => ['changefreq' => 'monthly', 'priority' => '0.8'],
        'faq.php' => ['changefreq' => 'monthly', 'priority' => '0.6'],
        'disclaimer.php' => ['changefreq' => 'yearly', 'priority' => '0.3'],
        'privacy-policy.php' => ['changefreq' => 'yearly', 'priority' => '0.3'],
        'terms-conditions.php' => ['changefreq' => 'yearly', 'priority' => '0.3']
    ];

    // Add static pages to sitemap
    foreach ($pages as $page => $settings) {
        $url = rtrim(SITE_URL, '/') . '/' . ltrim($page, '/');
        $lastmod = date('Y-m-d');
        
        $xml->startElement('url');
        $xml->writeElement('loc', htmlspecialchars($url));
        $xml->writeElement('lastmod', $lastmod);
        $xml->writeElement('changefreq', $settings['changefreq']);
        $xml->writeElement('priority', $settings['priority']);
        $xml->endElement(); // url
    }

        // Add dynamic content if database is available
    try {
        $db = null;
        $dbFile = __DIR__ . '/includes/db.php';
        
        if (file_exists($dbFile)) {
            require_once $dbFile;
            
            // Check if Database class exists and can be instantiated
            if (class_exists('Database')) {
                $db = Database::getInstance()->getConnection();
                
                // Only proceed if we have a valid database connection
                if ($db instanceof PDO) {
                    // Blog posts
                    $blogStmt = $db->query("SHOW TABLES LIKE 'blog_posts'");
                    if ($blogStmt && $blogStmt->rowCount() > 0) {
                        $blogPosts = $db->query("SELECT slug, updated_at FROM blog_posts WHERE status = 'published'");
                        if ($blogPosts) {
                            while ($post = $blogPosts->fetch(PDO::FETCH_ASSOC)) {
                                $url = rtrim(SITE_URL, '/') . '/blog/' . htmlspecialchars($post['slug']);
                                $lastmod = !empty($post['updated_at']) ? date('Y-m-d', strtotime($post['updated_at'])) : date('Y-m-d');
                                
                                $xml->startElement('url');
                                $xml->writeElement('loc', $url);
                                $xml->writeElement('lastmod', $lastmod);
                                $xml->writeElement('changefreq', 'weekly');
                                $xml->writeElement('priority', '0.7');
                                $xml->endElement(); // url
                            }
                        }
                    }
                    
                    // Projects
                    $projectTable = $db->query("SHOW TABLES LIKE 'projects'");
                    if ($projectTable && $projectTable->rowCount() > 0) {
                        $projects = $db->query("SELECT slug, updated_at FROM projects WHERE status = 'completed'");
                        if ($projects) {
                            while ($project = $projects->fetch(PDO::FETCH_ASSOC)) {
                                $url = rtrim(SITE_URL, '/') . '/projects/' . htmlspecialchars($project['slug']);
                                $lastmod = !empty($project['updated_at']) ? date('Y-m-d', strtotime($project['updated_at'])) : date('Y-m-d');
                                
                                $xml->startElement('url');
                                $xml->writeElement('loc', $url);
                                $xml->writeElement('lastmod', $lastmod);
                                $xml->writeElement('changefreq', 'monthly');
                                $xml->writeElement('priority', '0.8');
                                $xml->endElement(); // url
                            }
                        }
                    }
                }
            }
        }
    } catch (Exception $e) {
        // Log error but continue
        error_log('Sitemap Generator DB Error: ' . $e->getMessage());
        // Ensure we don't have a broken database connection
        $db = null;
    }

    // End urlset
    $xml->endElement();
    
    // End document
    $xml->endDocument();
    
    return $xml->outputMemory();
}

// Generate the sitemap
$sitemap = generateSitemap();

// Save to file
$saved = @file_put_contents(__DIR__ . '/sitemap.xml', $sitemap);

// If accessed via web browser, output with proper headers
if (php_sapi_name() !== 'cli') {
    header('Content-Type: application/xml; charset=utf-8');
    header('Content-Length: ' . strlen($sitemap));
    echo $sitemap;
    exit;
}

// Return success/failure for CLI usage
exit($saved !== false ? 0 : 1);
