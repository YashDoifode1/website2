<?php
/**
 * Mobile Optimization (Custom PHP Version)
 */

// Add viewport meta tag
function add_viewport_meta() {
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">' . "\n";
}

// Add touch icons
function add_touch_icons() {
    ?>
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo SITE_URL; ?>/assets/images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php echo SITE_URL; ?>/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php echo SITE_URL; ?>/assets/images/favicon-16x16.png">
    <link rel="manifest" href="<?php echo SITE_URL; ?>/site.webmanifest">
    <meta name="theme-color" content="#ffffff">
    <?php
}

// Add mobile-specific CSS
function mobile_optimization_styles() {
    ?>
    <style>
        html { font-size: 16px; }
        a, button, .button, [role="button"], input[type="submit"] {
            min-height: 44px;
            min-width: 44px;
            padding: 10px 16px;
        }
        body {
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
            line-height: 1.6;
        }
        @media (max-width: 767px) {
            .nav-menu a, .footer-menu a {
                padding: 12px 16px;
                display: block;
            }
            .container, .content-area {
                width: 100%;
                max-width: 100%;
                padding-left: 15px;
                padding-right: 15px;
            }
            img {
                max-width: 100%;
                height: auto;
            }
        }
    </style>
    <?php
}
?>
