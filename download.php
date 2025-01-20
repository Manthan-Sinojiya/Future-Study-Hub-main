<?php
// download.php

if(isset($_GET['file'])) {
    $file = urldecode($_GET['file']);

    if(file_exists($file)) {
        // Set headers
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        // Flush system output buffer
        flush();
        // Read the file and send its contents
        readfile($file);
        // Exit script
        exit;
    } else {
        echo "File not found.";
    }
}
?>