<?php
/**
 * File Upload Handler
 * 
 * Handles secure file uploads with validation
 */

declare(strict_types=1);

/**
 * Upload image file
 * 
 * @param array $file $_FILES array element
 * @param string $uploadDir Upload directory path
 * @param array $allowedTypes Allowed file extensions
 * @param int $maxSize Maximum file size in bytes
 * @return array ['success' => bool, 'filename' => string, 'error' => string]
 */
function uploadImage(array $file, string $uploadDir = 'assets/images/', array $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'], int $maxSize = 5242880): array
{
    $result = [
        'success' => false,
        'filename' => '',
        'error' => ''
    ];
    
    // Check if file was uploaded
    if (!isset($file['error']) || is_array($file['error'])) {
        $result['error'] = 'Invalid file upload.';
        return $result;
    }
    
    // Check for upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            $result['error'] = 'No file was uploaded.';
            return $result;
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $result['error'] = 'File size exceeds limit.';
            return $result;
        default:
            $result['error'] = 'Unknown upload error.';
            return $result;
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        $result['error'] = 'File size exceeds ' . formatBytes($maxSize) . ' limit.';
        return $result;
    }
    
    // Get file extension
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    // Validate file extension
    if (!in_array($fileExtension, $allowedTypes)) {
        $result['error'] = 'Invalid file type. Allowed: ' . implode(', ', $allowedTypes);
        return $result;
    }
    
    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp'
    ];
    
    if (!in_array($mimeType, $allowedMimes)) {
        $result['error'] = 'Invalid file type detected.';
        return $result;
    }
    
    // Generate unique filename
    $filename = generateUniqueFilename($fileExtension);
    
    // Create upload directory if it doesn't exist
    $fullUploadDir = __DIR__ . '/../' . $uploadDir;
    if (!is_dir($fullUploadDir)) {
        if (!mkdir($fullUploadDir, 0755, true)) {
            $result['error'] = 'Failed to create upload directory.';
            return $result;
        }
    }
    
    // Move uploaded file
    $destination = $fullUploadDir . $filename;
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $result['error'] = 'Failed to move uploaded file.';
        return $result;
    }
    
    // Set proper permissions
    chmod($destination, 0644);
    
    $result['success'] = true;
    $result['filename'] = $filename;
    
    return $result;
}

/**
 * Generate unique filename
 * 
 * @param string $extension File extension
 * @return string Unique filename
 */
function generateUniqueFilename(string $extension): string
{
    return uniqid('img_', true) . '_' . time() . '.' . $extension;
}

/**
 * Delete uploaded file
 * 
 * @param string $filename Filename to delete
 * @param string $uploadDir Upload directory path
 * @return bool Success status
 */
function deleteUploadedFile(string $filename, string $uploadDir = 'assets/images/'): bool
{
    if (empty($filename) || $filename === 'placeholder.jpg') {
        return true;
    }
    
    $filePath = __DIR__ . '/../' . $uploadDir . $filename;
    
    if (file_exists($filePath)) {
        return unlink($filePath);
    }
    
    return true;
}

/**
 * Format bytes to human readable format
 * 
 * @param int $bytes Bytes
 * @param int $precision Decimal precision
 * @return string Formatted string
 */
function formatBytes(int $bytes, int $precision = 2): string
{
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Validate image dimensions
 * 
 * @param string $filePath Path to image file
 * @param int $maxWidth Maximum width
 * @param int $maxHeight Maximum height
 * @return array ['valid' => bool, 'width' => int, 'height' => int, 'error' => string]
 */
function validateImageDimensions(string $filePath, int $maxWidth = 2000, int $maxHeight = 2000): array
{
    $result = [
        'valid' => false,
        'width' => 0,
        'height' => 0,
        'error' => ''
    ];
    
    $imageInfo = getimagesize($filePath);
    
    if ($imageInfo === false) {
        $result['error'] = 'Invalid image file.';
        return $result;
    }
    
    $result['width'] = $imageInfo[0];
    $result['height'] = $imageInfo[1];
    
    if ($imageInfo[0] > $maxWidth || $imageInfo[1] > $maxHeight) {
        $result['error'] = "Image dimensions exceed {$maxWidth}x{$maxHeight}px limit.";
        return $result;
    }
    
    $result['valid'] = true;
    return $result;
}

/**
 * Resize image
 * 
 * @param string $sourcePath Source image path
 * @param string $destPath Destination image path
 * @param int $maxWidth Maximum width
 * @param int $maxHeight Maximum height
 * @return bool Success status
 */
function resizeImage(string $sourcePath, string $destPath, int $maxWidth = 1200, int $maxHeight = 1200): bool
{
    $imageInfo = getimagesize($sourcePath);
    
    if ($imageInfo === false) {
        return false;
    }
    
    list($width, $height, $type) = $imageInfo;
    
    // Calculate new dimensions
    $ratio = min($maxWidth / $width, $maxHeight / $height);
    
    if ($ratio >= 1) {
        // No resize needed
        return copy($sourcePath, $destPath);
    }
    
    $newWidth = (int)($width * $ratio);
    $newHeight = (int)($height * $ratio);
    
    // Create image resource
    switch ($type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($sourcePath);
            break;
        case IMAGETYPE_WEBP:
            $source = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }
    
    // Create new image
    $dest = imagecreatetruecolor($newWidth, $newHeight);
    
    // Preserve transparency
    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagealphablending($dest, false);
        imagesavealpha($dest, true);
        $transparent = imagecolorallocatealpha($dest, 255, 255, 255, 127);
        imagefilledrectangle($dest, 0, 0, $newWidth, $newHeight, $transparent);
    }
    
    // Resize
    imagecopyresampled($dest, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
    
    // Save
    $result = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($dest, $destPath, 90);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($dest, $destPath, 9);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($dest, $destPath);
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($dest, $destPath, 90);
            break;
    }
    
    // Free memory
    imagedestroy($source);
    imagedestroy($dest);
    
    return $result;
}

/**
 * Get upload configuration from .env
 * 
 * @return array Upload configuration
 */
function getUploadConfig(): array
{
    return [
        'max_size' => (int)env('MAX_UPLOAD_SIZE', 5242880),
        'allowed_types' => explode(',', env('ALLOWED_IMAGE_TYPES', 'jpg,jpeg,png,gif,webp')),
        'upload_dir' => 'assets/images/'
    ];
}
