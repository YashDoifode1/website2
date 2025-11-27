<?php
function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

// function truncateText($text, $length = 100) {
//     return (strlen($text) > $length) ? substr($text, 0, $length) . '...' : $text;
// }

?>

