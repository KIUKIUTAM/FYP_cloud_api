    <?php
require_once __DIR__ . '/minio_config.inc.php';

// Get MinIO client from configuration
$minio = getMinioClient();

// Process upload form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['mission_images'])) {
    // Reset flash messages
    unset($_SESSION['flash_error']);
    unset($_SESSION['flash_success']);
    
    // Handle both single and multiple file uploads
    $is_single_file = !is_array($_FILES['mission_images']['name']);
    
    if ($is_single_file) {
        // Convert single file to array format for consistent processing
        foreach ($_FILES['mission_images'] as $key => $value) {
            $_FILES['mission_images'][$key] = [$value];
        }
    }
    
    $successCount = 0;
    $errorCount = 0;
    $actualFiles = 0;
    
    // Count total valid files
    foreach ($_FILES['mission_images']['name'] as $key => $name) {
        $error = $_FILES['mission_images']['error'][$key];
        $tmpName = $_FILES['mission_images']['tmp_name'][$key];
        
        if ($error === UPLOAD_ERR_OK && !empty($tmpName) && !empty($name)) {
            $actualFiles++;
        }
    }
    
    // Process each valid file
    foreach ($_FILES['mission_images']['name'] as $key => $name) {
        // Skip invalid files
        $error = $_FILES['mission_images']['error'][$key];
        $tmpName = $_FILES['mission_images']['tmp_name'][$key];
        
        if ($error !== UPLOAD_ERR_OK || empty($tmpName) || empty($name)) {
            // Only count as an error if it's not UPLOAD_ERR_NO_FILE
            if ($error !== UPLOAD_ERR_NO_FILE && !empty($name)) {
                $errorCount++;
            }
            continue;
        }
        
        // File upload to Flask API
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'http://python:8000/upload',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'file' => new CURLFile(
                    $tmpName,
                    $_FILES['mission_images']['type'][$key],
                    $name
                ),
                'mission_id' => $mission_id,
                'detection_type' => $_POST['detection_type']
            ],
            CURLOPT_RETURNTRANSFER => true
        ]);
        
        $response = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);
        curl_close($ch);
        
        $responseData = json_decode($response, true);

        // Process successful response
        if ($responseData && !isset($responseData['error']) && $responseInfo['http_code'] == 200) {
            $successCount++;
        } else {
            $errorCount++;
        }
    }
    
    // Set session messages based on results
    if ($actualFiles > 0) {
        if ($successCount > 0) {
            $_SESSION['flash_success'] = "Successfully uploaded $successCount " . ($successCount == 1 ? "image" : "images");
            
            if ($errorCount > 0 && $errorCount != $actualFiles) {
                $_SESSION['flash_error'] = "$errorCount " . ($errorCount == 1 ? "image" : "images") . " failed to upload";
            }
        } 
    } else {
        $_SESSION['flash_error'] = "No valid images were selected for upload";
    }
    
    // Store messages in session variable
    session_write_close();
    
    // Redirect back to the mission page
    echo "<script>window.location.href = '?page=3&id={$mission_id}';</script>";
    exit();
}