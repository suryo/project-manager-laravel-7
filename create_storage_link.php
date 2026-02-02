<?php
/**
 * Storage Link Alternative - For Servers with Symlink Disabled
 * Upload this file to your server root and access via browser
 * Example: https://yourdomain.com/create_storage_link.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$target = __DIR__ . '/storage/app/public';
$link = __DIR__ . '/public/storage';

echo "<h2>🔧 Storage Link Setup</h2>";
echo "<hr>";

// Check if symlink is disabled
$disabled_functions = explode(',', ini_get('disable_functions'));
$symlink_disabled = in_array('symlink', $disabled_functions);

if ($symlink_disabled) {
    echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin-bottom: 20px;'>";
    echo "<strong>⚠️ Warning:</strong> symlink() function is disabled on this server.<br>";
    echo "This is common on shared hosting for security reasons.<br>";
    echo "</div>";
}

// Check if link already exists
if (file_exists($link)) {
    if (is_link($link)) {
        echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
        echo "✅ <strong>Storage link already exists!</strong><br>";
        echo "Link: <code>" . $link . "</code><br>";
        echo "Target: <code>" . readlink($link) . "</code><br>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545;'>";
        echo "❌ <strong>Error:</strong> 'public/storage' exists but is not a symbolic link.<br>";
        echo "It appears to be a regular directory.<br><br>";
        echo "<strong>Action Required:</strong><br>";
        echo "1. Backup the existing 'public/storage' directory if it contains files<br>";
        echo "2. Delete or rename it<br>";
        echo "3. Refresh this page<br>";
        echo "</div>";
    }
} else {
    // Try to create symbolic link
    echo "<h3>Attempting to create symbolic link...</h3>";
    
    if (!$symlink_disabled && @symlink($target, $link)) {
        echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
        echo "✅ <strong>Storage link created successfully!</strong><br>";
        echo "Link: <code>" . $link . "</code><br>";
        echo "Target: <code>" . $target . "</code><br>";
        echo "<br><strong style='color: #dc3545;'>IMPORTANT: Delete this file now for security!</strong>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545; margin-bottom: 20px;'>";
        echo "❌ <strong>Failed to create symbolic link.</strong><br>";
        echo "Reason: symlink() is disabled or insufficient permissions.<br>";
        echo "</div>";
        
        echo "<h3>📋 Alternative Solutions:</h3>";
        
        // Solution 1: Copy files (not recommended)
        echo "<div style='background: #e7f3ff; padding: 15px; border-left: 4px solid #2196F3; margin-bottom: 15px;'>";
        echo "<h4>Option 1: Copy Files (Not Recommended)</h4>";
        echo "<p>This will copy files from storage/app/public to public/storage.</p>";
        echo "<p><strong>⚠️ Warning:</strong> You'll need to re-copy files every time you upload new files.</p>";
        echo "<form method='post' style='margin-top: 10px;'>";
        echo "<input type='hidden' name='action' value='copy_files'>";
        echo "<button type='submit' style='background: #2196F3; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 4px;'>Copy Files Now</button>";
        echo "</form>";
        echo "</div>";
        
        // Solution 2: Use Route (recommended)
        echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
        echo "<h4>✅ Option 2: Use Route-Based Serving (Recommended)</h4>";
        echo "<p>This is the best solution when symlink is disabled.</p>";
        echo "<ol>";
        echo "<li>The controller <code>StorageFileController.php</code> is already created</li>";
        echo "<li>Add this route to your <code>routes/web.php</code>:</li>";
        echo "</ol>";
        echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto;'>";
        echo htmlspecialchars("Route::get('/storage/{path}', [App\Http\Controllers\StorageFileController::class, 'serve'])
    ->where('path', '.*')
    ->name('storage.serve');");
        echo "</pre>";
        echo "<p><strong>Note:</strong> Files will be served through Laravel routing instead of direct access.</p>";
        echo "</div>";
    }
}

// Handle copy files action
if (isset($_POST['action']) && $_POST['action'] === 'copy_files') {
    echo "<hr><h3>📁 Copying Files...</h3>";
    
    if (!is_dir($target)) {
        echo "<div style='background: #f8d7da; padding: 15px; border-left: 4px solid #dc3545;'>";
        echo "❌ Source directory does not exist: <code>$target</code>";
        echo "</div>";
    } else {
        // Create public/storage directory if not exists
        if (!is_dir($link)) {
            mkdir($link, 0755, true);
        }
        
        // Recursive copy function
        function recursiveCopy($src, $dst) {
            $dir = opendir($src);
            @mkdir($dst, 0755, true);
            $count = 0;
            
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        $count += recursiveCopy($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                        $count++;
                    }
                }
            }
            closedir($dir);
            return $count;
        }
        
        $fileCount = recursiveCopy($target, $link);
        
        echo "<div style='background: #d4edda; padding: 15px; border-left: 4px solid #28a745;'>";
        echo "✅ <strong>Files copied successfully!</strong><br>";
        echo "Total files copied: <strong>$fileCount</strong><br>";
        echo "<br><strong style='color: #856404;'>⚠️ Remember:</strong> You need to re-run this every time you upload new files!";
        echo "</div>";
    }
}

echo "<hr>";
echo "<h3>📊 System Information</h3>";
echo "<table style='border-collapse: collapse; width: 100%;'>";
echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Target directory exists:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>" . (is_dir($target) ? "✅ Yes" : "❌ No") . "</td></tr>";
echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Target path:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'><code>$target</code></td></tr>";
echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Link exists:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>" . (file_exists($link) ? "✅ Yes" : "❌ No") . "</td></tr>";
echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Link path:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'><code>$link</code></td></tr>";
echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Is symbolic link:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>" . (is_link($link) ? "✅ Yes" : "❌ No") . "</td></tr>";
echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>symlink() disabled:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'>" . ($symlink_disabled ? "❌ Yes" : "✅ No") . "</td></tr>";

if (is_link($link)) {
    echo "<tr><td style='padding: 8px; border: 1px solid #ddd;'><strong>Link points to:</strong></td><td style='padding: 8px; border: 1px solid #ddd;'><code>" . readlink($link) . "</code></td></tr>";
}

echo "</table>";

echo "<hr>";
echo "<div style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin-top: 20px;'>";
echo "<strong>🔒 Security Reminder:</strong><br>";
echo "Delete this file (<code>create_storage_link.php</code>) after you're done!<br>";
echo "Leaving it accessible is a security risk.";
echo "</div>";
?>
