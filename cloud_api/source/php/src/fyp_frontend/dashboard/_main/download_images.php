<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../_inc/db.inc.php';
require_once __DIR__ . '/minio_config.inc.php';

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$mission_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['userid'];

if ($mission_id <= 0) {
    die('Error: Invalid mission ID');
}

$pdo = dbconnect();

try {
    // Verify mission belongs to user
    $stmt = $pdo->prepare("SELECT * FROM missiontable WHERE mission_id = ? AND user_id = ?");
    $stmt->execute([$mission_id, $user_id]);
    $mission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mission) {
        die('Error: Mission not found or access denied');
    }

    // Get Minio client
    $minio = getMinioClient();

    // Create a zip file
    $zip = new ZipArchive();
    $zipFileName = "mission_{$mission_id}_images_" . date('Y-m-d_H-i') . ".zip";
    $tmpFile = tempnam(sys_get_temp_dir(), 'zip');
    
    if ($zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        die('Error: Could not create zip file');
    }

    // Get all processed images
    $result = $minio->listObjectsV2([
        'Bucket' => 'crack-detection',
        'Prefix' => "mission_{$mission_id}/processed/"
    ]);

    $contents = $result['Contents'] ?? [];

    foreach ($contents as $object) {
        if (str_ends_with($object['Key'], '/')) continue;
        
        $cmd = $minio->getCommand('GetObject', [
            'Bucket' => 'crack-detection',
            'Key'    => $object['Key']
        ]);
        
        $presignedRequest = $minio->createPresignedRequest($cmd, '+15 minutes');
        $presignedUrl = (string)$presignedRequest->getUri();
        
        // Download the image
        $response = file_get_contents($presignedUrl);
        if ($response !== false) {
            // Add to zip with original filename
            $filename = basename($object['Key']);
            $zip->addFromString($filename, $response);
        }
    }

    $zip->close();

    // Send the file to the browser
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
    header('Content-Length: ' . filesize($tmpFile));
    readfile($tmpFile);

    // Clean up
    unlink($tmpFile);
    exit;

} catch (Exception $e) {
    error_log("Error downloading images: " . $e->getMessage());
    die('Error: Failed to download images');
}
?>