<?php
/**
 * Social Media Links (Custom PHP Version)
 */

// Social media profiles
$social_links = [
    'facebook' => [
        'url' => 'https://facebook.com/yourpage',
        'icon' => 'fab fa-facebook-f',
        'name' => 'Facebook',
        'enabled' => true
    ],
    'twitter' => [
        'url' => 'https://twitter.com/yourhandle',
        'icon' => 'fab fa-twitter',
        'name' => 'Twitter',
        'enabled' => true
    ],
    'linkedin' => [
        'url' => 'https://linkedin.com/company/yourcompany',
        'icon' => 'fab fa-linkedin-in',
        'name' => 'LinkedIn',
        'enabled' => true
    ],
    'youtube' => [
        'url' => 'https://youtube.com/yourchannel',
        'icon' => 'fab fa-youtube',
        'name' => 'YouTube',
        'enabled' => true
    ],
    'instagram' => [
        'url' => 'https://instagram.com/yourprofile',
        'icon' => 'fab fa-instagram',
        'name' => 'Instagram',
        'enabled' => true
    ]
];

// Function to display social media links
function display_social_links($size = 'md') {
    global $social_links;
    
    $size_class = '';
    switch ($size) {
        case 'sm': $size_class = 'text-2xl'; break;
        case 'lg': $size_class = 'text-3xl'; break;
        default: $size_class = 'text-xl';
    }
    
    echo '<div class="social-links flex flex-wrap gap-4">';
    foreach ($social_links as $platform => $data) {
        if ($data['enabled'] && !empty($data['url'])) {
            echo sprintf(
                '<a href="%s" target="_blank" rel="noopener noreferrer"
                  class="%s %s text-gray-700 hover:text-primary transition-colors duration-200"
                  aria-label="%s on %s"
                  title="Follow us on %s">
                    <i class="%s"></i>
                </a>',
                htmlspecialchars($data['url']),
                $size_class,
                $platform,
                SITE_NAME,
                $data['name'],
                $data['name'],
                $data['icon']
            );
        }
    }
    echo '</div>';
}

// Function to add social meta tags (custom PHP version)
function add_social_meta_tags_custom() {
    global $social_links;

    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

    // Facebook App ID support
    if (defined('FACEBOOK_APP_ID')) {
        echo '<meta property="fb:app_id" content="' . FACEBOOK_APP_ID . '">' . "\n";
    }

    // Social "rel=me" links
    foreach ($social_links as $data) {
        if ($data['enabled'] && !empty($data['url'])) {
            echo '<link rel="me" href="' . htmlspecialchars($data['url']) . '">' . "\n";
        }
    }
}
?>
