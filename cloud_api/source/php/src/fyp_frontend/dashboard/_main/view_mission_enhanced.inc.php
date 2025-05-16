<?php
require_once '../vendor/autoload.php';
use Aws\Exception\AwsException;
function getMission() {
    // Check if the user is logged in
    if (!isset($_SESSION['userid'])) {
        echo '<div class="alert alert-danger">Please <a href="../login/">login</a> to view missions.</div>';
        return;
    }
    $user_id = $_SESSION['userid'];

    // Get mission ID from URL
    $mission_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Include the database connection
    require_once '../_inc/db.inc.php'; 
    $pdo = dbconnect();

    try {
        // Get mission details
        $stmt = $pdo->prepare("SELECT * FROM missiontable 
                             WHERE mission_id = ? AND user_id = ?");
        $stmt->execute([$mission_id, $user_id]);
        $mission = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$mission) {
            echo '<div class="alert alert-danger">Mission not found or access denied.</div>';
            return;
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo '<div class="alert alert-danger">Error loading mission data.</div>';
        return;
    }

    // Configure Minio client
    require_once 'minio_config.inc.php';
    $minio = getMinioClient();

    $images = [];
    try {
        // Get objects with error handling
        $result = $minio->listObjectsV2([
            'Bucket' => 'crack-detection',
            'Prefix' => "mission_{$mission_id}/processed/"
        ]);

        $contents = $result['Contents'] ?? [];
        
        foreach ($contents as $object) {
            if (str_ends_with($object['Key'], '/')) continue;

            // Generate browser-accessible URL using our new function
            $publicUrl = getMinioPublicUrl($object['Key']);
            
            // Presigned URL for secure access (keep this as it's working correctly)
            $cmd = $minio->getCommand('GetObject', [
                'Bucket' => 'crack-detection',
                'Key'    => $object['Key']
            ]);
            
            $presignedRequest = $minio->createPresignedRequest($cmd, '+15 minutes');
            $presignedUrl = (string)$presignedRequest->getUri();

            // Extract metadata from filename (if available)
            $filename = basename($object['Key']);
            $metadata = [];
            
            // Check if filename contains detection data (e.g. crack_severity_high_20240421.jpg)
            if (preg_match('/crack_severity_(low|medium|high)/', $filename, $matches)) {
                $metadata['detection_type'] = 'crack';
                $metadata['severity'] = $matches[1];
            } elseif (strpos($filename, 'car_detected') !== false) {
                $metadata['detection_type'] = 'car';
            }

            $images[] = [
                'processed_image_path' => $publicUrl,
                'presigned_path' => $presignedUrl,
                'filename' => $filename,
                'full_path' => $object['Key'],
                'metadata' => $metadata
            ];
        }
    } catch (AwsException $e) {
        error_log("Minio Error: " . $e->getMessage());
        echo '<div class="alert alert-danger">Image storage error. Please try again later.</div>';
    }
    require_once '../dashboard/_main/images_upload.inc.php';
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title><?php echo htmlspecialchars($mission['mission_name']); ?> - Building Inspection</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <style>
            :root {
                --primary-color: #0d6efd;
                --secondary-color: #6c757d;
                --success-color: #198754;
                --danger-color: #dc3545;
                --warning-color: #ffc107;
                --info-color: #0dcaf0;
                --light-color: #f8f9fa;
                --dark-color: #212529;
            }
            
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f5f7fa;
                overflow-x: hidden;
            }
            
            .card {
                border-radius: 10px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
                border: none;
                margin-bottom: 20px;
            }
            
            .card-header {
                background-color: #fff;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                padding: 15px 20px;
                font-weight: 600;
                border-radius: 10px 10px 0 0 !important;
            }
            
            .nav-tabs .nav-link {
                border: none;
                color: var(--secondary-color);
                padding: 10px 15px;
                font-weight: 500;
            }
            
            .nav-tabs .nav-link.active {
                color: var(--primary-color);
                background-color: transparent;
                border-bottom: 2px solid var(--primary-color);
            }
            
            .btn-primary {
                background-color: var(--primary-color);
                border-color: var(--primary-color);
                padding: 8px 16px;
                font-weight: 500;
            }
            
            .btn-outline-primary {
                color: var(--primary-color);
                border-color: var(--primary-color);
                padding: 8px 16px;
                font-weight: 500;
            }
            
            .badge {
                padding: 6px 10px;
                font-weight: 500;
            }
            
            .photo-album {
                padding: 15px;
            }
            
            .photo-album::-webkit-scrollbar {
                width: 8px;
            }
            
            .photo-album::-webkit-scrollbar-track {
                background: #f1f1f1;
                border-radius: 10px;
            }
            
            .photo-album::-webkit-scrollbar-thumb {
                background: #c1c1c1;
                border-radius: 10px;
            }
            
            .photo-album::-webkit-scrollbar-thumb:hover {
                background: #a1a1a1;
            }
            
            .image-card {
                position: relative;
                border-radius: 8px;
                overflow: hidden;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                margin-bottom: 15px;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            
            .image-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            }
            
            .image-card img {
                width: 100%;
                height: 180px;
                object-fit: cover;
                border-radius: 8px 8px 0 0;
            }
            
            .image-card .card-footer {
                padding: 8px 12px;
                background-color: white;
                border-top: none;
            }
            
            .image-card .card-overlay {
                position: absolute;
                top: 10px;
                right: 10px;
                display: flex;
                gap: 5px;
            }
            
            .image-card .card-overlay .btn {
                width: 32px;
                height: 32px;
                padding: 0;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                background-color: rgba(255, 255, 255, 0.9);
                color: #333;
            }
            
            .image-card .detection-badge {
                position: absolute;
                top: 10px;
                left: 10px;
                padding: 5px 10px;
                border-radius: 20px;
                font-size: 12px;
                font-weight: 600;
            }
            
            .severity-high {
                background-color: rgba(220, 53, 69, 0.9);
                color: white;
            }
            
            .severity-medium {
                background-color: rgba(255, 193, 7, 0.9);
                color: #212529;
            }
            
            .severity-low {
                background-color: rgba(25, 135, 84, 0.9);
                color: white;
            }
            
            .modal-content {
                border-radius: 10px;
                border: none;
            }
            
            .modal-header {
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                padding: 15px 20px;
            }
            
            .modal-body {
                padding: 20px;
            }
            
            .progress {
                height: 8px;
                border-radius: 4px;
            }
            
            .map-controls {
                position: absolute;
                top: 10px;
                right: 10px;
                z-index: 1000;
                display: flex;
                flex-direction: column;
                gap: 5px;
            }
            
            .map-control-btn {
                width: 40px;
                height: 40px;
                border-radius: 4px;
                background-color: white;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                cursor: pointer;
                color: #333;
            }
            
            .map-control-btn:hover {
                background-color: #f8f9fa;
            }
            
            .info-item {
                margin-bottom: 10px;
            }
            
            .info-item .label {
                font-weight: 600;
                color: #6c757d;
                font-size: 14px;
            }
            
            .info-item .value {
                font-size: 16px;
            }
            
            .filter-btn {
                padding: 6px 12px;
                border-radius: 20px;
                margin-right: 5px;
                font-size: 14px;
                font-weight: 500;
            }
            
            .filter-btn.active {
                background-color: var(--primary-color);
                color: white;
            }
            
            /* Animation for loading */
            @keyframes pulse {
                0% { opacity: 0.6; }
                50% { opacity: 1; }
                100% { opacity: 0.6; }
            }
            
            .pulse-animation {
                animation: pulse 1.5s infinite ease-in-out;
            }
            
            /* Drone Monitor Button */
            .drone-monitor-btn {
                background-color: #2563eb;
                color: white;
                border: none;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            
            .drone-monitor-btn:hover {
                background-color: #1d4ed8;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(37, 99, 235, 0.3);
            }
            
            .drone-monitor-btn i {
                font-size: 1.1rem;
            }
            
            /* Responsive adjustments */
            @media (max-width: 768px) {
                .image-card img {
                    height: 140px;
                }
            }
        </style>
    </head>
    <body>

    <div class="container-fluid py-4">
        <!-- Header with Mission Title and Status -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1"><?php echo htmlspecialchars($mission['mission_name']); ?></h2>
                <p class="text-muted mb-0">
                    <i class="fas fa-map-marker-alt me-1"></i> 
                    <?php echo htmlspecialchars($mission['address']); ?>
                </p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-<?php echo ($mission['status'] === 'completed') ? 'success' : 'warning'; ?> me-2">
                    <?php echo ucfirst($mission['status']); ?>
                </span>
                
                <!-- Drone Monitor Button -->
                <a href="DroneFleetMonitor.html?mission_id=<?php echo $mission_id; ?>" class="btn drone-monitor-btn">
                    <i class="fas fa-drone"></i>
                    Monitor Drones
                </a>
                
                <button class="btn btn-primary" onclick="generateReport(<?php echo $mission_id; ?>, this)">
                    <i class="fas fa-file-pdf me-2"></i>Generate Report
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Left Column - Map and Info -->
            <div class="col-lg-8">
                <!-- Map Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Inspection Location</h5>
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-secondary" id="toggleMapType">
                                <i class="fas fa-layer-group me-1"></i>Toggle Map Type
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0 position-relative">
                        <div id="missionMap" style="height: 400px; width: 100%; border-radius: 0 0 10px 10px;"></div>
                        
                        <!-- Map Controls -->
                        <div class="map-controls">
                            <div class="map-control-btn" id="zoomIn" title="Zoom In">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="map-control-btn" id="zoomOut" title="Zoom Out">
                                <i class="fas fa-minus"></i>
                            </div>
                            <div class="map-control-btn" id="centerMap" title="Center Map">
                                <i class="fas fa-crosshairs"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Upload Section (if mission is not completed) -->
                <?php if (strtolower($mission['status']) !== 'completed'): ?>
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Upload & Detect Images</h5>
                        </div>
                        <div class="card-body">
                            <?php 
                            // Display flash messages if they exist
                            if (isset($_SESSION['flash_success'])) {
                                echo '<div class="alert alert-success">' . $_SESSION['flash_success'] . '</div>';
                                unset($_SESSION['flash_success']);
                            }
                            if (isset($_SESSION['flash_error'])) {
                                echo '<div class="alert alert-danger">' . $_SESSION['flash_error'] . '</div>';
                                unset($_SESSION['flash_error']);
                            }
                            ?>
                            <form method="POST" enctype="multipart/form-data" id="uploadForm">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Detection Type</label>
                                        <select class="form-select" name="detection_type" required>
                                            <option value="crack">Wall Crack Detection</option>
                                            <option value="car">Car Detection</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Building Section</label>
                                        <select class="form-select" name="building_section">
                                            <option value="exterior">Exterior Wall</option>
                                            <option value="interior">Interior Wall</option>
                                            <option value="foundation">Foundation</option>
                                            <option value="roof">Roof</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Select Images</label>
                                    <div class="input-group">
                                        <input class="form-control" type="file" name="mission_images[]" 
                                            multiple accept=".png,.jpg,.jpeg,.gif" id="imageInput">
                                        <button class="btn btn-outline-secondary" type="button" id="clearFiles">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Supported formats: png, jpg, jpeg, gif</div>
                                </div>
                                
                                <div id="imagePreview" class="mb-3 d-none">
                                    <label class="form-label">Image Preview</label>
                                    <div class="row g-2" id="previewContainer"></div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Upload & Detect
                                </button>
                                <div class="mt-3" id="uploadProgress" style="display: none;">
                                    <div class="progress mb-2">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted">Processing images... This may take a few minutes.</small>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column - Mission Details -->
            <div class="col-lg-4">
                <!-- Mission Details Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Mission Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="label">Mission ID</div>
                            <div class="value"><?php echo $mission_id; ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="label">Location</div>
                            <div class="value"><?php echo htmlspecialchars($mission['location']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="label">Start Date</div>
                            <div class="value"><?php echo date('M j, Y', strtotime($mission['start_time'])); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="label">End Date</div>
                            <div class="value">
                                <?php echo !empty($mission['end_time']) ? date('M j, Y', strtotime($mission['end_time'])) : 'In Progress'; ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="label">Status</div>
                            <div class="value">
                                <span class="badge bg-<?php echo ($mission['status'] === 'completed') ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($mission['status']); ?>
                                </span>
                            </div>
                        </div>
                        
                        <!-- Drone Monitoring Section -->
                        <div class="info-item mt-4">
                            <div class="label">Drone Monitoring</div>
                            <div class="value">
                                <a href="DroneFleetMonitor.html?mission_id=<?php echo $mission_id; ?>" class="btn btn-sm btn-outline-primary w-100">
                                    <i class="fas fa-drone me-2"></i>Open Drone Fleet Monitor
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Notes Card -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Notes</h5>
                        <?php if (strtolower($mission['status']) !== 'completed'): ?>
                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editNotesModal">
                                <i class="fas fa-edit"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($mission['notes'])): ?>
                            <div class="notes-content">
                                <?php echo nl2br(htmlspecialchars($mission['notes'], ENT_QUOTES, 'UTF-8')); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">No notes have been added to this mission.</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Detection Summary Card -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Detection Summary</h5>
                    </div>
                    <div class="card-body">
                        <?php
                        // Count detections by type and severity
                        $crackCount = 0;
                        $highSeverityCount = 0;
                        $mediumSeverityCount = 0;
                        $lowSeverityCount = 0;
                        $carCount = 0;
                        
                        foreach ($images as $image) {
                            if (!empty($image['metadata'])) {
                                if ($image['metadata']['detection_type'] === 'crack') {
                                    $crackCount++;
                                    if ($image['metadata']['severity'] === 'high') {
                                        $highSeverityCount++;
                                    } elseif ($image['metadata']['severity'] === 'medium') {
                                        $mediumSeverityCount++;
                                    } elseif ($image['metadata']['severity'] === 'low') {
                                        $lowSeverityCount++;
                                    }
                                } elseif ($image['metadata']['detection_type'] === 'car') {
                                    $carCount++;
                                }
                            }
                        }
                        ?>
                        
                        <div class="mb-3">
                            <h6>Wall Cracks</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Cracks Detected:</span>
                                <span class="fw-bold"><?php echo $crackCount; ?></span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <div>
                                    <span class="badge bg-danger me-1">High: <?php echo $highSeverityCount; ?></span>
                                    <span class="badge bg-warning text-dark me-1">Medium: <?php echo $mediumSeverityCount; ?></span>
                                    <span class="badge bg-success">Low: <?php echo $lowSeverityCount; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h6>Other Detections</h6>
                            <div class="d-flex justify-content-between">
                                <span>Cars Detected:</span>
                                <span class="fw-bold"><?php echo $carCount; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Photo Album Section -->
        <div class="card mt-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Inspection Photos</h5>
                <div>
                    <button class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#galleryModal">
                        <i class="fas fa-th me-1"></i>View Gallery
                    </button>
                    <button class="btn btn-sm btn-primary" onclick="downloadMissionFile(<?php echo $mission_id; ?>)">
                        <i class="fas fa-download me-1"></i>Download All
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filter Buttons -->
                <div class="mb-3">
                    <button class="btn btn-sm filter-btn active" data-filter="all">All Photos</button>
                    <button class="btn btn-sm filter-btn" data-filter="crack">Cracks</button>
                    <button class="btn btn-sm filter-btn" data-filter="high">High Severity</button>
                    <button class="btn btn-sm filter-btn" data-filter="medium">Medium Severity</button>
                    <button class="btn btn-sm filter-btn" data-filter="low">Low Severity</button>
                    <button class="btn btn-sm filter-btn" data-filter="car">Cars</button>
                </div>
                
                <!-- Photos Grid -->
                <div class="photo-album" style="max-height: 600px; overflow-y: auto;">
                    <?php if (!empty($images)): ?>
                        <div class="row g-3" id="photoGrid">
                            <?php foreach ($images as $index => $image): ?>
                                <?php
                                // Determine filter classes
                                $filterClasses = 'image-item';
                                if (!empty($image['metadata'])) {
                                    if ($image['metadata']['detection_type'] === 'crack') {
                                        $filterClasses .= ' filter-crack';
                                        if (!empty($image['metadata']['severity'])) {
                                            $filterClasses .= ' filter-' . $image['metadata']['severity'];
                                        }
                                    } elseif ($image['metadata']['detection_type'] === 'car') {
                                        $filterClasses .= ' filter-car';
                                    }
                                }
                                ?>
                                <div class="col-md-3 col-sm-4 col-6 <?php echo $filterClasses; ?>">
                                    <div class="image-card">
                                        <img src="<?php echo htmlspecialchars($image['processed_image_path']); ?>" 
                                             class="clickable-image"
                                             data-bs-toggle="modal"
                                             data-bs-target="#imageModal"
                                             data-image-index="<?php echo $index; ?>"
                                             alt="Inspection photo">
                                        
                                        <?php if (!empty($image['metadata'])): ?>
                                            <?php if ($image['metadata']['detection_type'] === 'crack' && !empty($image['metadata']['severity'])): ?>
                                                <span class="detection-badge severity-<?php echo $image['metadata']['severity']; ?>">
                                                    <?php echo ucfirst($image['metadata']['severity']); ?> Severity
                                                </span>
                                            <?php elseif ($image['metadata']['detection_type'] === 'car'): ?>
                                                <span class="detection-badge bg-info">Car Detected</span>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        
                                        <div class="card-overlay">
                                            <a href="_main/direct_download.php?mission_id=<?php echo $mission_id; ?>&path=<?php echo urlencode($image['processed_image_path']); ?>" 
                                               class="btn btn-sm"
                                               title="Download Image">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                        
                                        <div class="card-footer">
                                            <small class="text-muted"><?php echo basename($image['filename']); ?></small>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-images fa-3x mb-3"></i>
                            <p>No images available for this mission</p>
                            <?php if (strtolower($mission['status']) !== 'completed'): ?>
                                <p>Upload images to begin inspection</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalImageTitle">Image Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-0">
                    <div class="position-relative">
                        <img src="" id="modalImage" style="max-height: 80vh; max-width: 100%;">
                        <div class="position-absolute top-0 end-0 m-3">
                            <button class="btn btn-light btn-sm me-2" id="prevImage">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <button class="btn btn-light btn-sm" id="nextImage">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                    <div class="bg-light p-3">
                        <div class="row">
                            <div class="col-md-8">
                                <h6 class="text-start mb-2" id="modalImageFilename"></h6>
                                <div class="text-start" id="modalImageMetadata"></div>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="#" id="modalDownloadLink" class="btn btn-primary">
                                    <i class="fas fa-download me-2"></i>Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Gallery Modal -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Photo Gallery</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <!-- Gallery Filter Buttons -->
                        <div class="mb-4 text-center">
                            <button class="btn btn-outline-primary me-2 gallery-filter active" data-filter="all">All Photos</button>
                            <button class="btn btn-outline-primary me-2 gallery-filter" data-filter="crack">Cracks</button>
                            <button class="btn btn-outline-danger me-2 gallery-filter" data-filter="high">High Severity</button>
                            <button class="btn btn-outline-warning me-2 gallery-filter" data-filter="medium">Medium Severity</button>
                            <button class="btn btn-outline-success me-2 gallery-filter" data-filter="low">Low Severity</button>
                            <button class="btn btn-outline-info gallery-filter" data-filter="car">Cars</button>
                        </div>
                        
                        <!-- Gallery Grid -->
                        <div class="row g-4" id="galleryGrid">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $index => $image): ?>
                                    <?php
                                    // Determine filter classes
                                    $filterClasses = 'gallery-item';
                                    if (!empty($image['metadata'])) {
                                        if ($image['metadata']['detection_type'] === 'crack') {
                                            $filterClasses .= ' filter-crack';
                                            if (!empty($image['metadata']['severity'])) {
                                                $filterClasses .= ' filter-' . $image['metadata']['severity'];
                                            }
                                        } elseif ($image['metadata']['detection_type'] === 'car') {
                                            $filterClasses .= ' filter-car';
                                        }
                                    }
                                    ?>
                                    <div class="col-lg-3 col-md-4 col-sm-6 <?php echo $filterClasses; ?>">
                                        <div class="card h-100">
                                            <img src="<?php echo htmlspecialchars($image['processed_image_path']); ?>" 
                                                 class="card-img-top"
                                                 style="height: 200px; object-fit: cover; cursor: pointer;"
                                                 data-bs-toggle="modal"
                                                 data-bs-target="#imageModal"
                                                 data-image-index="<?php echo $index; ?>"
                                                 alt="Inspection photo">
                                            
                                            <?php if (!empty($image['metadata'])): ?>
                                                <?php if ($image['metadata']['detection_type'] === 'crack' && !empty($image['metadata']['severity'])): ?>
                                                    <span class="position-absolute top-0 start-0 m-2 badge bg-<?php echo ($image['metadata']['severity'] === 'high' ? 'danger' : ($image['metadata']['severity'] === 'medium' ? 'warning' : 'success')); ?>">
                                                        <?php echo ucfirst($image['metadata']['severity']); ?> Severity
                                                    </span>
                                                <?php elseif ($image['metadata']['detection_type'] === 'car'): ?>
                                                    <span class="position-absolute top-0 start-0 m-2 badge bg-info">Car Detected</span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <div class="card-body">
                                                <p class="card-text small text-truncate"><?php echo basename($image['filename']); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center py-5">
                                    <i class="fas fa-images fa-3x mb-3 text-muted"></i>
                                    <p class="text-muted">No images available for this mission</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Edit Notes Modal -->
    <?php if (strtolower($mission['status']) !== 'completed'): ?>
    <div class="modal fade" id="editNotesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Mission Notes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="notesForm" action="_main/update_notes.php" method="POST">
                        <input type="hidden" name="mission_id" value="<?php echo $mission_id; ?>">
                        <div class="mb-3">
                            <label for="missionNotes" class="form-label">Notes</label>
                            <textarea class="form-control" id="missionNotes" name="notes" rows="6"><?php echo htmlspecialchars($mission['notes'] ?? ''); ?></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveNotes">Save Notes</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Drone Monitor Modal -->
    <div class="modal fade" id="droneMonitorInfoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Drone Fleet Monitor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>The Drone Fleet Monitor allows you to:</p>
                    <ul>
                        <li>Track drone positions in real-time</li>
                        <li>Monitor battery levels and telemetry data</li>
                        <li>View flight paths and trajectories</li>
                        <li>Receive alerts for low battery or connection issues</li>
                    </ul>
                    <p>This will open in a new window to allow simultaneous monitoring while working with inspection data.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <a href="DroneFleetMonitor.html?mission_id=<?php echo $mission_id; ?>" class="btn btn-primary" target="_blank">
                        <i class="fas fa-external-link-alt me-2"></i>Open Monitor
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include necessary JS files -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBN3s1Zaa-s3p7rqP7MI2fP4VzdlhEeHCc&callback=initMap" async defer></script>
    
    <script>
    // Global variables
    let map;
    let marker;
    let currentMapType = 'hybrid';
    let images = <?php echo json_encode($images); ?>;
    let currentImageIndex = 0;
    
    // Initialize Map
    function initMap() {
        const coords = <?php echo json_encode(explode(',', $mission['location'])); ?>;
        const lat = parseFloat(coords[0]);
        const lng = parseFloat(coords[1]);
        const center = { lat: lat, lng: lng };

        map = new google.maps.Map(document.getElementById('missionMap'), {
            zoom: 18,
            center: center,
            mapTypeId: google.maps.MapTypeId.HYBRID,
            mapTypeControl: false,
            fullscreenControl: true,
            streetViewControl: true,
            zoomControl: false,
            rotateControl: true
        });
        
        // Add marker for mission location
        marker = new google.maps.Marker({
            position: center,
            map: map,
            title: '<?php echo htmlspecialchars($mission['mission_name']); ?>',
            animation: google.maps.Animation.DROP
        });
        
        // Add info window
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="width: 200px;">
                    <h6 style="margin-bottom: 5px;"><?php echo htmlspecialchars($mission['mission_name']); ?></h6>
                    <p style="margin-bottom: 5px; font-size: 14px;"><?php echo htmlspecialchars($mission['address']); ?></p>
                    <p style="margin-bottom: 0; font-size: 12px;">
                        <strong>Status:</strong> <?php echo ucfirst($mission['status']); ?>
                    </p>
                </div>
            `
        });
        
        // Open info window when marker is clicked
        marker.addListener('click', () => {
            infoWindow.open(map, marker);
        });
        
        // Add event listeners for custom map controls
        document.getElementById('zoomIn').addEventListener('click', () => {
            map.setZoom(map.getZoom() + 1);
        });
        
        document.getElementById('zoomOut').addEventListener('click', () => {
            map.setZoom(map.getZoom() - 1);
        });
        
        document.getElementById('centerMap').addEventListener('click', () => {
            map.setCenter(center);
            map.setZoom(18);
        });
        
        document.getElementById('toggleMapType').addEventListener('click', () => {
            currentMapType = currentMapType === 'hybrid' ? 'satellite' : 'hybrid';
            map.setMapTypeId(google.maps.MapTypeId[currentMapType.toUpperCase()]);
        });
    }
    
    // Download all mission files
    function downloadMissionFile(missionId) {
        try {
            // Show loading state
            const button = event.target.closest('button');
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Downloading...';
            
            // Create a hidden iframe to handle the download
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
            iframe.src = '/fyp_frontend/dashboard/_main/download_images.php?id=' + missionId;
            
            // Set a timeout to reset the button after 1 second
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-download me-1"></i>Download All';
            }, 1000);
        } catch (error) {
            console.error('Error in downloadMissionFile:', error);
            alert('Error downloading images. Please try again.');
        }
    }
    
    // Generate report
    function generateReport(missionId, button) {
        try {
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
            
            // Create a hidden iframe to handle the download
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
            iframe.src = '/fyp_frontend/dashboard/_main/generate_report.php?id=' + missionId;
            
            // Set a timeout to reset the button after 2 seconds
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-file-pdf me-2"></i>Generate Report';
            }, 2000);
        } catch (error) {
            console.error('Error in generateReport:', error);
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-file-pdf me-2"></i>Generate Report';
            }
        }
    }
    
    // Open drone monitor in new window
    function openDroneMonitor(missionId) {
        window.open('DroneFleetMonitor.html?mission_id=' + missionId, '_blank');
    }
    
    // Image preview functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Image upload preview
        const imageInput = document.getElementById('imageInput');
        const previewContainer = document.getElementById('previewContainer');
        const imagePreview = document.getElementById('imagePreview');
        const clearFilesBtn = document.getElementById('clearFiles');
        
        if (imageInput) {
            imageInput.addEventListener('change', function() {
                previewContainer.innerHTML = '';
                
                if (this.files.length > 0) {
                    imagePreview.classList.remove('d-none');
                    
                    for (let i = 0; i < Math.min(this.files.length, 8); i++) {
                        const file = this.files[i];
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            const col = document.createElement('div');
                            col.className = 'col-3';
                            
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'img-thumbnail';
                            img.style.height = '100px';
                            img.style.objectFit = 'cover';
                            
                            col.appendChild(img);
                            previewContainer.appendChild(col);
                        }
                        
                        reader.readAsDataURL(file);
                    }
                    
                    if (this.files.length > 8) {
                        const col = document.createElement('div');
                        col.className = 'col-3 d-flex align-items-center justify-content-center';
                        col.innerHTML = `<div class="text-center">+${this.files.length - 8} more</div>`;
                        previewContainer.appendChild(col);
                    }
                } else {
                    imagePreview.classList.add('d-none');
                }
            });
            
            if (clearFilesBtn) {
                clearFilesBtn.addEventListener('click', function() {
                    imageInput.value = '';
                    previewContainer.innerHTML = '';
                    imagePreview.classList.add('d-none');
                });
            }
        }
        
        // File upload form
        const form = document.getElementById('uploadForm');
        const progressBar = document.querySelector('#uploadProgress .progress-bar');
        const progressContainer = document.getElementById('uploadProgress');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const fileInput = this.querySelector('input[type="file"]');
                if (fileInput.files.length === 0) {
                    e.preventDefault();
                    alert('Please select at least one image to upload.');
                    return false;
                }
                
                // Show progress indicator
                progressContainer.style.display = 'block';
                progressBar.style.width = '25%';
                progressBar.classList.add('progress-bar-striped', 'progress-bar-animated');
                
                // Simple animation to show activity (not actual progress)
                let progress = 25;
                const interval = setInterval(function() {
                    progress += 5;
                    if (progress > 95) progress = 95; // Don't reach 100% until complete
                    progressBar.style.width = progress + '%';
                }, 500);
            });
        }
        
        // Image modal functionality
        const imageModal = document.getElementById('imageModal');
        if (imageModal) {
            imageModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                currentImageIndex = parseInt(button.getAttribute('data-image-index'));
                updateModalImage();
            });
            
            // Previous and next buttons
            document.getElementById('prevImage').addEventListener('click', function() {
                currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
                updateModalImage();
            });
            
            document.getElementById('nextImage').addEventListener('click', function() {
                currentImageIndex = (currentImageIndex + 1) % images.length;
                updateModalImage();
            });
        }
        
        // Filter functionality for photo grid
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Update active button
                filterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Filter images
                const items = document.querySelectorAll('.image-item');
                items.forEach(item => {
                    if (filter === 'all') {
                        item.style.display = 'block';
                    } else {
                        if (item.classList.contains('filter-' + filter)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    }
                });
            });
        });
        
        // Gallery filter functionality
        const galleryFilterButtons = document.querySelectorAll('.gallery-filter');
        galleryFilterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Update active button
                galleryFilterButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                
                // Filter images
                const items = document.querySelectorAll('.gallery-item');
                items.forEach(item => {
                    if (filter === 'all') {
                        item.style.display = 'block';
                    } else {
                        if (item.classList.contains('filter-' + filter)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    }
                });
            });
        });
        
        // Save notes functionality
        const saveNotesBtn = document.getElementById('saveNotes');
        if (saveNotesBtn) {
            saveNotesBtn.addEventListener('click', function() {
                document.getElementById('notesForm').submit();
            });
        }
        
        // Add event listener for drone monitor info modal
        const droneMonitorLinks = document.querySelectorAll('.drone-monitor-btn');
        droneMonitorLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                // If user holds Ctrl or Command key, let the default behavior happen (open in new tab)
                if (e.ctrlKey || e.metaKey) {
                    return;
                }
                
                e.preventDefault();
                const href = this.getAttribute('href');
                
                // Show info modal or open directly based on user preference
                if (localStorage.getItem('showDroneMonitorInfo') === 'false') {
                    window.open(href, '_blank');
                } else {
                    const modal = new bootstrap.Modal(document.getElementById('droneMonitorInfoModal'));
                    modal.show();
                }
            });
        });
    });
    
    // Update modal image
    function updateModalImage() {
        if (currentImageIndex >= 0 && currentImageIndex < images.length) {
            const image = images[currentImageIndex];
            document.getElementById('modalImage').src = image.processed_image_path;
            document.getElementById('modalImageFilename').textContent = image.filename;
            document.getElementById('modalDownloadLink').href = '_main/direct_download.php?mission_id=<?php echo $mission_id; ?>&path=' + encodeURIComponent(image.processed_image_path);
            
            // Update metadata display
            let metadataHtml = '';
            if (image.metadata && image.metadata.detection_type) {
                if (image.metadata.detection_type === 'crack') {
                    let severityClass = '';
                    if (image.metadata.severity === 'high') {
                        severityClass = 'text-danger fw-bold';
                    } else if (image.metadata.severity === 'medium') {
                        severityClass = 'text-warning fw-bold';
                    } else if (image.metadata.severity === 'low') {
                        severityClass = 'text-success fw-bold';
                    }
                    
                    metadataHtml = `
                        <div>
                            <span class="badge bg-primary me-2">Crack Detection</span>
                            <span class="badge bg-secondary me-2">Severity:</span>
                            <span class="${severityClass}">${image.metadata.severity ? image.metadata.severity.toUpperCase() : 'Unknown'}</span>
                        </div>
                    `;
                } else if (image.metadata.detection_type === 'car') {
                    metadataHtml = `
                        <div>
                            <span class="badge bg-info me-2">Car Detection</span>
                        </div>
                    `;
                }
            }
            document.getElementById('modalImageMetadata').innerHTML = metadataHtml;
            
            // Update modal title to include image index
            document.getElementById('modalImageTitle').textContent = `Image ${currentImageIndex + 1} of ${images.length}`;
        }
    }
    </script>
    </body>
    </html>
<?php }?>