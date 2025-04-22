<?php
// Start session and check if user is logged in
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log errors to file
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

// First load autoload.php
require_once __DIR__ . '/../../vendor/autoload.php';

// Then get mission ID
$mission_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['userid'];

if ($mission_id <= 0) {
    die('Error: Invalid mission ID');
}

// Now require the rest of the files
require_once __DIR__ . '/../../_inc/db.inc.php';
require_once __DIR__ . '/minio_config.inc.php';

// Get database connection
$pdo = dbconnect();

try {
    // Get mission details
    $stmt = $pdo->prepare("SELECT * FROM missiontable WHERE mission_id = ? AND user_id = ?");
    $stmt->execute([$mission_id, $user_id]);
    $mission = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mission) {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Mission not found or access denied']);
        exit;
    }

    // Get images related to this mission
    $stmt = $pdo->prepare("SELECT * FROM imagetable WHERE mission_id = ? ORDER BY upload_time");
    $stmt->execute([$mission_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Configure Minio client
    $minio = getMinioClient();

    // Get processed images from Minio
    $processedImages = [];
    try {
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
            
            $processedImages[] = [
                'url' => $presignedUrl,
                'key' => $object['Key'],
                'filename' => basename($object['Key'])
            ];
        }
    } catch (Exception $e) {
        error_log("Minio Error: " . $e->getMessage());
    }

    // Create new mPDF instance
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 15,
        'margin_bottom' => 15,
        'margin_header' => 0,
        'margin_footer' => 0
    ]);

    // Set document information
    $mpdf->SetCreator('Drone Mission System');
    $mpdf->SetAuthor('Drone Mission System');
    $mpdf->SetTitle('Mission Report - ' . $mission['mission_name']);
    $mpdf->SetSubject('Mission Report');
    $mpdf->SetKeywords('Mission, Report, Drone');

    // Add a page
    $mpdf->AddPage();

    // Create HTML content
    $html = '
    <h1 style="text-align: center;">Mission Report</h1>
    <h2 style="margin-top: 20px;">1. Mission Information</h2>
    <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
        <tr>
            <td style="width: 30%; border: 1px solid #000; padding: 5px;"><b>Mission ID</b></td>
            <td style="border: 1px solid #000; padding: 5px;">' . $mission['mission_id'] . '</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;"><b>Mission Name</b></td>
            <td style="border: 1px solid #000; padding: 5px;">' . htmlspecialchars($mission['mission_name']) . '</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;"><b>Location</b></td>
            <td style="border: 1px solid #000; padding: 5px;">' . htmlspecialchars($mission['location']) . '</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;"><b>Address</b></td>
            <td style="border: 1px solid #000; padding: 5px;">' . htmlspecialchars($mission['address'] ?? 'N/A') . '</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;"><b>Start Time</b></td>
            <td style="border: 1px solid #000; padding: 5px;">' . date('M j, Y H:i', strtotime($mission['start_time'])) . '</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;"><b>End Time</b></td>
            <td style="border: 1px solid #000; padding: 5px;">' . (!empty($mission['end_time']) ? date('M j, Y H:i', strtotime($mission['end_time'])) : 'N/A') . '</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;"><b>Status</b></td>
            <td style="border: 1px solid #000; padding: 5px;">' . ucfirst($mission['status']) . '</td>
        </tr>
        <tr>
            <td style="border: 1px solid #000; padding: 5px;"><b>Device SN</b></td>
            <td style="border: 1px solid #000; padding: 5px;">' . htmlspecialchars($mission['device_sn'] ?? 'N/A') . '</td>
        </tr>
    </table>

    <h2 style="margin-top: 20px;">Mission Notes:</h2>
    <p style="margin-top: 10px;">' . nl2br(htmlspecialchars($mission['notes'] ?? 'No notes provided.')) . '</p>

    <h2 style="margin-top: 20px;">2. Image Analysis</h2>
    <p style="margin-top: 10px;">Total Images: ' . count($processedImages) . '</p>

    <h3>Detection Status Summary:</h3>
    <ul>';
    
    // Add detection status summary
    // $detectionStatuses = [];
    $totalImages = 0; // Track total images processed
    
    foreach ($processedImages as $image) { // Use processedImages for consistency
        $totalImages++;
        
        // Get detection status safely
        // $status = isset($image['detection_status']) ? $image['detection_status'] : 'Unknown';
        // $status = !empty($status) ? $status : 'Unknown';
        
        // // Initialize status count if not exists
        // if (!isset($detectionStatuses[$status])) {
        //     $detectionStatuses[$status] = 0;
        // }
        // $detectionStatuses[$status]++;
    }
    
    // Add total images count
    $html .= '<li>Total Images Processed: ' . $totalImages . '</li>';
    
    // Add detection status counts
    // foreach ($detectionStatuses as $status => $count) {
    //     $html .= '<li>' . ucfirst($status) . ': ' . $count . ' images</li>';
    // }
    $html .= '</ul>';

    // Add image gallery section to HTML
    $html .= '<h2 style="margin-top: 20px; page-break-before: always;">3. Image Gallery</h2>';
    
    $imagesPerPage = 2;
    $imageCount = 0;
    $tempFiles = []; // To track temporary files

    foreach ($processedImages as $image) {
        // Add page break every 6 images
        if ($imageCount > 0 && $imageCount % $imagesPerPage === 0) {
            $html .= '<div style="page-break-before: always;"></div>';
        }

        $html .= '<div style="page-break-inside: avoid; margin-bottom: 20px;">';
        $html .= '<h3 style="margin-top: 10px;">Image: ' . $image['filename'] . '</h3>';
        
        try {
            $imageData = file_get_contents($image['url']);
            if ($imageData) {
                // Create temporary file
                $tempFile = tempnam(sys_get_temp_dir(), 'img_');
                file_put_contents($tempFile, $imageData);
                $tempFiles[] = $tempFile; // Remember to clean up later

                // Add image to HTML
                $html .= '<img src="' . $tempFile . '" 
                             style="max-width: 100%; 
                                    height: auto;
                                    border: 1px solid #ddd;
                                    margin: 10px 0;
                                    display: block;">';
            }
        } catch (Exception $e) {
            $html .= '<p style="color: red;">Error loading image: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
        
        $html .= '</div>';
        $imageCount++;
    }

    // Write all HTML to PDF in one go
    $mpdf->WriteHTML($html);

    // Clean up temporary files
    foreach ($tempFiles as $tempFile) {
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    // Output the PDF
    $filename = 'Mission_Report_' . $mission['mission_id'] . '_' . date('Ymd') . '.pdf';
    $mpdf->Output($filename, 'D'); // 'D' means download
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    exit;
} catch (Exception $e) {
    error_log("Error generating report: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error generating report: ' . $e->getMessage()]);
    exit;
}