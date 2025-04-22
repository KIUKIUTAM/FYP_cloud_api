<?php
// Prevent errors from being displayed to browser
ini_set('display_errors', 0);
error_reporting(0);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../_inc/db.inc.php';
require_once __DIR__ . '/minio_config.inc.php';

session_start();

// Get parameters
$mission_id = isset($_GET['mission_id']) ? intval($_GET['mission_id']) : 0;
$path = isset($_GET['path']) ? urldecode($_GET['path']) : '';
$user_id = $_SESSION['userid'] ?? 0;

if ($mission_id <= 0 || empty($path)) {
    die('Error: Invalid parameters');
}

// Connect to database
$pdo = dbconnect();

try {
    // Verify mission belongs to user
    $stmt = $pdo->prepare("SELECT * FROM missiontable WHERE mission_id = ? AND user_id = ?");
    $stmt->execute([$mission_id, $user_id]);
    $mission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mission) {
        die('Error: Mission not found or access denied');
    }

    // Extract filename from path
    $filename = basename($path);
    
    // Get MinIO client and download directly from there
    $minio = getMinioClient();
    
    try {
        // Get the object
        $result = $minio->getObject([
            'Bucket' => 'crack-detection',
            'Key' => "mission_{$mission_id}/processed/{$filename}"
        ]);
        
        // Set headers to force download
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        
        if (isset($result['ContentLength'])) {
            header('Content-Length: ' . $result['ContentLength']);
        }
        
        // Output file and stop the script
        echo $result['Body'];
        exit;
        
    } catch (Exception $e) {
        // Try with presigned URL as a fallback
        try {
            $cmd = $minio->getCommand('GetObject', [
                'Bucket' => 'crack-detection',
                'Key' => "mission_{$mission_id}/processed/{$filename}"
            ]);
            
            $presignedRequest = $minio->createPresignedRequest($cmd, '+15 minutes');
            $presignedUrl = (string)$presignedRequest->getUri();
            
            // Use stream context to suppress errors
            $context = stream_context_create([
                'http' => [
                    'ignore_errors' => true,
                    'follow_location' => true,
                    'max_redirects' => 3,
                ]
            ]);
            
            $fileContent = @file_get_contents($presignedUrl, false, $context);
            
            if ($fileContent !== false) {
                // Set headers
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . strlen($fileContent));
                
                // Output file and stop
                echo $fileContent;
                exit;
            } else {
                throw new Exception("Failed to download via presigned URL");
            }
        } catch (Exception $innerEx) {
            die('Error: File could not be downloaded. Please try again later.');
        }
    }
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}
?>