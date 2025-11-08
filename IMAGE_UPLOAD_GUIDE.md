# Image Upload System Guide

## Overview

The admin panel now supports direct image uploads instead of manually entering filenames. This makes it easier to manage images for projects, services, team members, and other content.

## Features

### ✅ What's New

**Before:**
- Had to manually upload images via FTP
- Enter filename in text field
- No validation or preview

**After:**
- Upload images directly from admin panel
- Automatic file validation
- Image preview before saving
- Secure filename generation
- Old image deletion on update

### 🎯 Supported Pages

Image upload is now available on:
- ✅ **Projects** - Project images
- ✅ **Services** - Service icons/images
- ✅ **Team** - Team member photos
- ✅ **Testimonials** - Client photos
- ✅ **Blog Posts** - Featured images

## How to Use

### Upload New Image

1. **Navigate to Admin Page**
   - Go to Projects, Services, Team, etc.
   - Click "Add New" or "Edit"

2. **Select Image File**
   - Click "Choose File" button
   - Select image from your computer
   - See instant preview

3. **Save**
   - Fill in other required fields
   - Click "Save" or "Update"
   - Image is automatically uploaded

### Update Existing Image

1. **Edit Item**
   - Click "Edit" on any item
   - See current image displayed

2. **Upload New Image**
   - Click "Choose File"
   - Select new image
   - See preview of new image

3. **Save Changes**
   - Click "Update"
   - Old image is automatically deleted
   - New image is saved

## File Requirements

### Allowed Formats
- ✅ JPG/JPEG
- ✅ PNG
- ✅ GIF
- ✅ WebP

### File Size Limit
- **Maximum:** 5MB per file
- Configurable in `.env` file

### Recommended Dimensions
- **Projects:** 1200x800px
- **Services:** 800x600px
- **Team:** 400x400px (square)
- **Blog:** 1200x630px

## Security Features

### 1. File Validation

**Extension Check:**
- Only allowed image types accepted
- Prevents uploading of dangerous files

**MIME Type Check:**
- Validates actual file content
- Prevents fake extensions

**Size Check:**
- Enforces maximum file size
- Prevents server overload

### 2. Secure Filenames

**Auto-Generated:**
```
Format: img_[unique-id]_[timestamp].[ext]
Example: img_673e4f2a1b3c8_1732012345.jpg
```

**Benefits:**
- Prevents filename conflicts
- Avoids special characters
- Unique for each upload

### 3. Safe Storage

**Upload Directory:**
- `assets/images/`
- Protected by proper permissions
- Organized structure

**File Permissions:**
- Set to 644 (read-only for web)
- Prevents execution
- Secure access

### 4. Old File Cleanup

**Automatic Deletion:**
- Old images deleted on update
- Prevents disk space waste
- Maintains clean directory

## Technical Details

### Upload Function

```php
uploadImage($file, $uploadDir, $allowedTypes, $maxSize)
```

**Parameters:**
- `$file` - $_FILES array element
- `$uploadDir` - Upload directory path
- `$allowedTypes` - Array of allowed extensions
- `$maxSize` - Maximum file size in bytes

**Returns:**
```php
[
    'success' => true/false,
    'filename' => 'generated-filename.jpg',
    'error' => 'error message if failed'
]
```

### Configuration

Edit `.env` file:

```env
# Maximum upload size (in bytes)
MAX_UPLOAD_SIZE=5242880

# Allowed image types (comma-separated)
ALLOWED_IMAGE_TYPES=jpg,jpeg,png,gif,webp
```

## Usage Examples

### Projects Page

```php
// Upload handling
if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
    $uploadResult = uploadImage($_FILES['image']);
    
    if ($uploadResult['success']) {
        $image = $uploadResult['filename'];
    } else {
        $error_message = $uploadResult['error'];
    }
}
```

### Form HTML

```html
<form method="POST" enctype="multipart/form-data">
    <input type="file" 
           name="image" 
           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
           onchange="previewImage(this)">
    
    <div id="imagePreview">
        <img id="preview" src="" alt="Preview">
    </div>
</form>
```

### Preview JavaScript

```javascript
function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}
```

## Troubleshooting

### Upload Failed

**Error: "File size exceeds limit"**
- Solution: Reduce image size or increase MAX_UPLOAD_SIZE in .env

**Error: "Invalid file type"**
- Solution: Ensure file is JPG, PNG, GIF, or WebP

**Error: "Failed to move uploaded file"**
- Solution: Check directory permissions (755 for folder, 644 for files)

### Image Not Displaying

**Problem:** Uploaded but not showing

**Solutions:**
1. Check file exists in `assets/images/`
2. Verify filename in database
3. Check file permissions
4. Clear browser cache

### Preview Not Working

**Problem:** No preview after selecting file

**Solutions:**
1. Check JavaScript console for errors
2. Ensure browser supports FileReader API
3. Verify preview div IDs match

### Permission Denied

**Problem:** Cannot upload files

**Solutions:**
1. Check `assets/images/` directory exists
2. Set directory permissions: `chmod 755 assets/images/`
3. Ensure web server has write access

## Best Practices

### 1. Image Optimization

**Before Upload:**
- Resize images to recommended dimensions
- Compress images (use tools like TinyPNG)
- Convert to WebP for better performance

**Tools:**
- Photoshop / GIMP
- Online: tinypng.com, squoosh.app
- Bulk: ImageOptim, JPEGmini

### 2. Naming Convention

**Descriptive Names:**
- Use clear, descriptive names
- Avoid special characters
- Keep it short

**Examples:**
- ✅ `modern-villa-project.jpg`
- ✅ `john-doe-portrait.jpg`
- ❌ `IMG_1234.jpg`
- ❌ `photo (1).jpg`

### 3. Organization

**Folder Structure:**
```
assets/images/
├── projects/
├── services/
├── team/
└── blog/
```

**Benefits:**
- Easy to find images
- Better organization
- Faster backups

### 4. Regular Cleanup

**Monthly Tasks:**
- Delete unused images
- Check for duplicates
- Optimize large files
- Backup important images

## Advanced Features

### Image Resizing

Automatically resize uploaded images:

```php
resizeImage($sourcePath, $destPath, $maxWidth, $maxHeight);
```

**Example:**
```php
$uploadResult = uploadImage($_FILES['image']);
if ($uploadResult['success']) {
    $sourcePath = 'assets/images/' . $uploadResult['filename'];
    resizeImage($sourcePath, $sourcePath, 1200, 800);
}
```

### Dimension Validation

Check image dimensions:

```php
$validation = validateImageDimensions($filePath, 2000, 2000);
if (!$validation['valid']) {
    echo $validation['error'];
}
```

### Multiple Uploads

Upload multiple images at once:

```html
<input type="file" name="images[]" multiple>
```

```php
foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
    $file = [
        'name' => $_FILES['images']['name'][$key],
        'type' => $_FILES['images']['type'][$key],
        'tmp_name' => $tmp_name,
        'error' => $_FILES['images']['error'][$key],
        'size' => $_FILES['images']['size'][$key]
    ];
    
    $result = uploadImage($file);
}
```

## API Reference

### uploadImage()

Upload and validate image file.

**Signature:**
```php
uploadImage(
    array $file,
    string $uploadDir = 'assets/images/',
    array $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'],
    int $maxSize = 5242880
): array
```

### deleteUploadedFile()

Delete uploaded file from server.

**Signature:**
```php
deleteUploadedFile(
    string $filename,
    string $uploadDir = 'assets/images/'
): bool
```

### generateUniqueFilename()

Generate unique filename with timestamp.

**Signature:**
```php
generateUniqueFilename(string $extension): string
```

### formatBytes()

Convert bytes to human-readable format.

**Signature:**
```php
formatBytes(int $bytes, int $precision = 2): string
```

### resizeImage()

Resize image to fit within max dimensions.

**Signature:**
```php
resizeImage(
    string $sourcePath,
    string $destPath,
    int $maxWidth = 1200,
    int $maxHeight = 1200
): bool
```

## FAQs

**Q: Can I upload multiple images at once?**
A: Currently single upload per field. Multiple upload can be added.

**Q: What happens to old images?**
A: Automatically deleted when updating with new image.

**Q: Can I use my own filenames?**
A: No, filenames are auto-generated for security.

**Q: How do I increase upload limit?**
A: Update MAX_UPLOAD_SIZE in .env file.

**Q: Are images compressed automatically?**
A: No, compress before upload for best results.

**Q: Can I upload from URL?**
A: Not currently supported. Upload from computer only.

## Support

For issues with image uploads:

1. Check this guide
2. Verify file meets requirements
3. Check server error logs
4. Ensure proper permissions
5. Contact system administrator

## Related Files

- `includes/upload.php` - Upload handler
- `admin/projects.php` - Projects with upload
- `admin/services.php` - Services with upload
- `admin/team.php` - Team with upload
- `.env` - Upload configuration

---

**Last Updated:** November 8, 2024
**Version:** 1.0
**Feature:** Image Upload System
