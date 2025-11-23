<?php
/**
 * SEO Optimization (Custom PHP Version)
 */

// Ensure proper heading structure
function ensure_heading_structure($content) {
    preg_match_all('/<h([1-6])(.*?)>(.*?)<\/h[1-6]>/i', $content, $headings);

    if (empty($headings[0])) {
        return $content;
    }

    $previous_level = 1;

    foreach ($headings[0] as $index => $heading) {
        $level = (int)$headings[1][$index];
        $text = $headings[3][$index];

        if ($level > $previous_level + 1) {
            $new_level = $previous_level + 1;
            $content = str_replace(
                $heading,
                "<h{$new_level}{$headings[2][$index]}>{$text}</h{$new_level}>",
                $content
            );
        }

        $previous_level = $level;
    }

    return $content;
}

// Add structured data (replace WordPress `is_front_page`)
function add_structured_data_custom($is_homepage = false) {

    if ($is_homepage) {

        $structured_data = [
            "@context" => "https://schema.org",
            "@type" => "WebSite",
            "name" => SITE_NAME,
            "url" => SITE_URL,
            "potentialAction" => [
                "@type" => "SearchAction",
                "target" => SITE_URL . "/search.php?query={search_term}",
                "query-input" => "required name=search_term"
            ]
        ];

        echo '<script type="application/ld+json">' .
            json_encode($structured_data, JSON_UNESCAPED_SLASHES) .
        '</script>' . "\n";
    }
}

// Optimize images
function optimize_images_custom($content) {

    // Add loading="lazy"
    $content = preg_replace('/<img\s(.*?)>/i', '<img $1 loading="lazy">', $content);

    // Auto add width/height if missing
    $content = preg_replace_callback(
        '/<img([^>]*)>/i',
        function($matches) {

            $tag = $matches[1];

            preg_match('/src=["\'](.*?)["\']/i', $tag, $src_matches);

            if (empty($src_matches[1])) {
                return $matches[0];
            }

            $image_url = $src_matches[1];
            $image_path = str_replace(SITE_URL, $_SERVER['DOCUMENT_ROOT'], $image_url);

            if (file_exists($image_path)) {
                list($width, $height) = @getimagesize($image_path);

                if ($width && $height) {
                    if (!strpos($tag, 'width=')) {
                        $tag .= " width=\"{$width}\"";
                    }
                    if (!strpos($tag, 'height=')) {
                        $tag .= " height=\"{$height}\"";
                    }
                }
            }

            return "<img{$tag}>";
        },
        $content
    );

    return $content;
}

// Add resource hints (custom)
function add_resource_hints_custom() {
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}
?>
