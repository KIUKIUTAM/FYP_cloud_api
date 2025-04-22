<?php

// Get the mission ID from the query parameter
$mission_id = $_GET['mission_id'];

// Path to the mission file (You need to replace this with the actual path)
$mission_file_path = 'dashboard\_main\uploads' . $mission_id . '.zip';

// Check if the file exists
if (file_exists($mission_file_path)) {
    // Set headers to initiate the file download
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($mission_file_path) . '"');
    header('Content-Length: ' . filesize($mission_file_path));
    
    // Read the file and output its contents
    readfile($mission_file_path);
    exit;
} else {
    echo "The mission file does not exist.";
}
?>