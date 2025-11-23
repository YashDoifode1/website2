<?php
/**
 * Performance Optimization (Custom PHP Version)
 */

// Disable emojis (browser-level)
function disable_emojis() {
    header("Content-Security-Policy: script-src 'self'");
}

// Defer JS (only works on manually printed scripts)
function defer_js($html) {
    return str_replace('<script', '<script defer', $html);
}

// Remove query strings from static files
function remove_query_strings($url) {
    $parts = explode('?', $url);
    return $parts[0];
}

// Async CSS Loader
function load_async_css() {
    ?>
    <style id="critical-css">
        body { visibility: hidden; opacity: 0; transition: 0.3s ease; }
    </style>

    <script>
        function loadCSS(href) {
            var link = document.createElement("link");
            link.rel = "stylesheet";
            link.href = href;
            link.media = "print";
            link.onload = function() { link.media = "all"; };
            document.head.appendChild(link);
        }

        loadCSS("<?php echo SITE_URL; ?>/assets/css/global-styles.css");

        document.addEventListener("DOMContentLoaded", function() {
            document.body.style.visibility = "visible";
            document.body.style.opacity = 1;
        });
    </script>
    <?php
}
?>
