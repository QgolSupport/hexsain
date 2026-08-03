<?php
$directory = "C:/xampp/htdocs/files"; // Directory path
$image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'xlsx']; // Allowed extensions

// Function to check if a file has allowed extension
function isAllowedFile($file) {
    global $image_extensions;
    $extension = pathinfo($file, PATHINFO_EXTENSION);
    return in_array(strtolower($extension), $image_extensions);
}

// Function to recursively find files
function findFiles($dir) {
    $files = [];
    if (!is_dir($dir)) {
        return $files;
    }
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item == '.' || $item == '..') continue;
        $fullPath = $dir . '/' . $item;
        if (is_dir($fullPath)) {
            $files = array_merge($files, findFiles($fullPath)); // Recurse into subdirectories
        } elseif (is_file($fullPath) && isAllowedFile($fullPath)) {
            $files[] = $fullPath; // Add allowed files
        }
    }
    return $files;
}

// Handle file download before any output
if (isset($_POST['download'])) {
    $file_to_download = $_POST['file_to_download'];
    if (file_exists($file_to_download)) {
        $extension = pathinfo($file_to_download, PATHINFO_EXTENSION);
        $mimeType = $extension === 'xlsx' 
            ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            : 'application/octet-stream';
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . basename($file_to_download) . '"');
        header('Content-Length: ' . filesize($file_to_download));
        readfile($file_to_download);
        exit;
    } else {
        echo "<p>The file does not exist: $file_to_download</p>";
    }
}

// Fetch files
$files = findFiles($directory);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="/favicon.png" type="image/x-icon">
    <title>File Viewer and Downloader</title>
    <style>
        /* General styles for the page */
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; }
        .container { width: 90%; max-width: 1200px; margin: auto; padding: 20px; }
        .file-list { display: flex; flex-wrap: wrap; justify-content: space-between; }
        .file-item { padding: 15px; margin: 10px; background-color: white; border-radius: 5px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        .file-item a, .file-item button { display: block; margin-top: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h1>Files</h1>
    <div class="file-list">
        <?php if (!empty($files)) {
            foreach ($files as $file) {
                $relativePath = str_replace($directory . '/', '', $file); // Relative path
                echo "<div class='file-item'>";
                echo "<a href=\"$file\" target=\"_blank\">$relativePath</a>";
                echo "<form action='' method='post'>";
                echo "<input type='hidden' name='file_to_download' value=\"" . htmlspecialchars($file, ENT_QUOTES) . "\">";
                echo "<button type='submit' name='download'>Download</button>";
                echo "</form>";
                echo "</div>";
            }
        } else {
            echo "<p>No files found in the directory.</p>";
        } ?>
    </div>
</div>
</body>
</html>
