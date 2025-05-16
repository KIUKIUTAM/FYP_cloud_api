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

            $images[] = [
                'processed_image_path' => $publicUrl, // Use public URL for browser access
                'presigned_path' => $presignedUrl,   // Alternative secure URL
                'filename' => basename($object['Key']),
                'full_path' => $object['Key']       // Store full bucket path
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
        <title><?php echo htmlspecialchars($mission['mission_name']); ?> - Mission Details</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        <style>
        .photo-album::-webkit-scrollbar {
            width: 8px;
        }
        .photo-album::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .photo-album::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .clickable-image:hover {
            opacity: 0.9;
            transform: scale(1.02);
            transition: all 0.2s ease;
        }
        /* Drone Monitor Button Styles */
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
        </style>
    </head>
    <body>

    <!-- Mission Page Layout -->
    <div class="col-md-9 mx-auto col-lg-10 px-md-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
            <h1 class="h2">Details</h1>
            <!-- Add Drone Monitor Button in the header -->
            <a href="DroneFleetMonitor.html?mission_id=<?php echo $mission_id; ?>" class="btn drone-monitor-btn" target="_blank">
                <i class="fas fa-drone"></i> Monitor Drones
            </a>
        </div>
        <div class="row g-4 mt-4">
            <!-- Left Column - Map Section -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><h5>Map Route</h5></div>
                    <div class="card-body p-0">
                        <div id="missionMap" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>
            </div>

            <!-- Right Column - Mission Info -->
            <div class="col-md-4">
                <div class="card h-60">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><?php echo htmlspecialchars($mission['mission_name']); ?></h5>
                </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Mission ID</dt>
                            <dd class="col-sm-8"><?php echo $mission_id; ?></dd>

                            <dt class="col-sm-4">Address</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($mission['address']); ?></dd>

                            <dt class="col-sm-4">Location</dt>
                            <dd class="col-sm-8"><?php echo htmlspecialchars($mission['location']); ?></dd>

                            <dt class="col-sm-4">Start Date</dt>
                            <dd class="col-sm-8"><?php echo date('M j, Y', strtotime($mission['start_time'])); ?></dd>

                            <dt class="col-sm-4">End Date</dt>
                            <dd class="col-sm-8">
                                <?php echo !empty($mission['end_time']) ? date('M j, Y', strtotime($mission['end_time'])) : 'N/A'; ?>
                            </dd>
    
                            <dt class="col-sm-4">Status</dt>
                            <dd class="col-sm-8">
                            <span class="badge bg-<?php echo ($mission['status'] === 'completed') ? 'success' : 'warning'; ?> px-2 py-1">
                                <?php echo ucfirst($mission['status']); ?>
                            </span>
                            </dd>

                        </dl>
                        <div class="mt-4 d-flex gap-2">
                            <button class="btn btn-primary" onclick="generateReport(<?php echo $mission_id; ?>, this)">
                                <i class="fas fa-file-pdf me-2"></i>Generate Report
                            </button>
                            <!-- Add Drone Monitor Button in the mission info card -->
                            <a href="DroneFleetMonitor.html?mission_id=<?php echo $mission_id; ?>" class="btn drone-monitor-btn">
                                <i class="fas fa-drone"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card h-25">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Notes</h5>
                </div>
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-4">Notes</dt>
                            <dd class="col-sm-8">
                                <?php 
                                echo nl2br(htmlspecialchars($mission['notes'] ?? '', ENT_QUOTES, 'UTF-8'));
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
                
                <!-- Add Drone Monitoring Section -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="mb-0">Drone Fleet</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">Monitor your drone fleet in real-time during the inspection mission.</p>
                        <a href="DroneFleetMonitor.html?mission_id=<?php echo $mission_id; ?>" class="btn btn-primary w-100" target="_blank">
                            <i class="fas fa-drone me-2"></i>Open Drone Monitor
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Add Upload Section -->
        <?php if (strtolower($mission['status']) !== 'completed'): ?>
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card mb-4">
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
                                <div class="mb-3">
                                    <label class="form-label">Detection Type</label>
                                    <select class="form-select" name="detection_type" required>
                                        <option value="crack">Wall Crack Detection</option>
                                        <option value="car">Car Detection</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Select Images</label>
                                    <input class="form-control" type="file" name="mission_images[]" 
                                        multiple accept=".png,.jpg,.jpeg,.gif">
                                    <div class="form-text">Supported formats: png, jpg, jpeg, gif</div>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Upload & Detect
                                </button>
                                <div class="mt-3" id="uploadProgress" style="display: none;">
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small class="text-muted">Processing...</small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <!-- Photo Album -->
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Photo Album</h5>
                        <div>
                            <!-- Add Drone Monitor Button in the photo album header -->
                            <a href="DroneFleetMonitor.html?mission_id=<?php echo $mission_id; ?>" class="btn drone-monitor-btn btn-sm me-2">
                                <i class="fas fa-drone me-1"></i>Monitor Drones
                            </a>
                            <button class="btn btn-primary btn-sm" onclick="downloadMissionFile(<?php echo $mission_id; ?>)">
                                <i class="fas fa-download me-2"></i>Download All Images
                            </button>
                        </div>
                    </div>
                    <div class="card-body photo-album" style="max-height: 60vh; overflow-y: auto;">
                        <?php if (!empty($images)): ?>
                            <div class="row g-2">
                                <?php foreach ($images as $image): ?>
                                    <div class="col-sm-2">
                                        <div class="position-relative">
                                            <img src="<?php echo htmlspecialchars($image['processed_image_path']); ?>" 
                                                class="img-thumbnail clickable-image"
                                                data-original="<?php echo htmlspecialchars($image['processed_image_path']); ?>"
                                                alt="Mission photo"
                                                style="cursor: pointer; width: 220px; height: 150px; object-fit: cover;">
                                            <div class="position-absolute bottom-0 end-0 m-1">
                                                <a href="_main/direct_download.php?mission_id=<?php echo $mission_id; ?>&path=<?php echo urlencode($image['processed_image_path']); ?>" 
                                                   class="btn btn-sm btn-light"
                                                   title="Download Image">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                No images available for this mission
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add event listeners to all download buttons
            const downloadButtons = document.querySelectorAll('.download-image');
            downloadButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const missionId = this.getAttribute('data-mission-id');
                    const imageKey = this.getAttribute('data-image-key');
                    
                    // Redirect to the download script
                    window.location.href = 'download_single_image.php?mission_id=' + missionId + '&image_key=' + imageKey;
                });
            });
        });
        function downloadMissionFile(missionId) {
            try {
                // Show loading state
                const button = event.target;
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
                    button.innerHTML = '<i class="fas fa-download me-2"></i>Download All Images';
                }, 1000);
            } catch (error) {
                console.error('Error in downloadMissionFile:', error);
                alert('Error downloading images. Please try again.');
            }
        }
    </script>
    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <img src="" id="modalImage" style="max-height: 90vh; width: 117vh;">
                </div>
            </div>
        </div>
    </div>

    <!-- Drone Monitor Info Modal -->
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
    <!-- For example, Bootstrap JS and its dependencies -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Include FontAwesome for icons -->
    <script src="https://kit.fontawesome.com/yourfontawesomekit.js" crossorigin="anonymous"></script>
    <script>
    // Image Modal Controller
    document.querySelectorAll('.clickable-image').forEach(img => {
        img.addEventListener('click', () => {
            document.getElementById('modalImage').src = img.dataset.original;
            new bootstrap.Modal(document.getElementById('imageModal')).show();
        });
    });

    // Add event listener for drone monitor buttons
    document.addEventListener('DOMContentLoaded', function() {
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
    </script>
    <script>
    // Initialize Map
    function initMap() {
        const coords = <?php echo json_encode(explode(',', $mission['location'])); ?>;
        const lat = parseFloat(coords[0]);
        const lng = parseFloat(coords[1]);

        const map = new google.maps.Map(document.getElementById('missionMap'), {
            zoom: 16,
            center: { lat: lat, lng: lng },
            mapTypeId: 'hybrid'
        });
        
        new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: map,
            title: 'Mission Location'
        });
    }
    
    
    // Load the map after the page has loaded
    window.addEventListener('load', initMap);
    </script>
    <!-- Include Google Maps API -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBN3s1Zaa-s3p7rqP7MI2fP4VzdlhEeHCc&"></script>

    <script>
    // AJAX File Upload
    document.addEventListener('DOMContentLoaded', function() {
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
                
                // Simple animation to show activity (not actual progress)
                let progress = 25;
                const interval = setInterval(function() {
                    progress += 5;
                    if (progress > 95) progress = 95; // Don't reach 100% until complete
                    progressBar.style.width = progress + '%';
                }, 500);
                
            });
        }
    });
    </script>

    <script>
    function generateReport(missionId, button) {
        try {
            // Show loading state
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Generating...';
            
            // Make the request using the correct path
            console.log('Redirecting to generate_report.php?id=' + missionId);
            
            // Create a hidden iframe to handle the download
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);
            iframe.src = '/fyp_frontend/dashboard/_main/generate_report.php?id=' + missionId;
            
            // Set a timeout to reset the button after 1 second
            setTimeout(() => {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-file-pdf me-2"></i>Generate Report';
            }, 1000);
        } catch (error) {
            console.error('Error in generateReport:', error);
            if (button) {
                button.disabled = false;
                button.innerHTML = '<i class="fas fa-file-pdf me-2"></i>Generate Report';
            }
        }
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>

<?php }?>
