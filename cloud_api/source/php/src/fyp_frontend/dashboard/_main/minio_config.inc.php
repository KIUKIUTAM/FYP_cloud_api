<?php
// Include the AWS SDK autoloader
require_once __DIR__ . '/../../vendor/autoload.php';
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

function getMinioClient() {
    return new S3Client([
        'version' => 'latest',
        'region' => 'ap-east-1',
        'endpoint' => 'http://host.docker.internal:9000',  // Changed to use internal Docker network
        'use_path_style_endpoint' => true,
        'credentials' => [
            'key' => $_ENV['MINIO_ACCESS_KEY'],
            'secret' => $_ENV['MINIO_SECRET_KEY'],
        ],
        'http' => [
            'verify' => false,
            'timeout' => 5,
            'connect_timeout' => 5
        ]
    ]);
}

// Function to get public URLs for browser access
function getMinioPublicUrl($objectKey, $bucket = 'crack-detection') {
    // Use localhost for browser access since we're accessing through Nginx
    return "http://localhost:9000/{$bucket}/" . rawurlencode($objectKey);
}

function createMissionFolders($mission_id, $reset = true, $bucket = 'crack-detection') {
    try {
        $minio = getMinioClient();
        $folders = [
            "mission_{$mission_id}/",
            "mission_{$mission_id}/original/",
            "mission_{$mission_id}/processed/"
        ];
        
        // Delete existing folders if reset is true
        if ($reset) {
            error_log("Checking for existing mission folders: mission_{$mission_id}");
            
            try {
                // List all objects in the mission folder
                $objects = $minio->listObjectsV2([
                    'Bucket' => $bucket,
                    'Prefix' => "mission_{$mission_id}/"
                ]);
                
                if (isset($objects['Contents']) && count($objects['Contents']) > 0) {
                    error_log("Found existing mission folders, deleting...");
                    
                    // Prepare objects to delete
                    $objectsToDelete = [];
                    foreach ($objects['Contents'] as $object) {
                        $objectsToDelete[] = ['Key' => $object['Key']];
                    }
                    
                    // Delete all objects
                    $minio->deleteObjects([
                        'Bucket' => $bucket,
                        'Delete' => [
                            'Objects' => $objectsToDelete
                        ]
                    ]);
                    error_log("Deleted existing mission folders: mission_{$mission_id}");
                }
            } catch (Exception $e) {
                error_log("Error deleting existing mission folders: " . $e->getMessage());
            }
        }
        
        // Create folders
        foreach ($folders as $folder) {
            try {
                $minio->putObject([
                    'Bucket' => $bucket,
                    'Key' => $folder,
                    'Body' => ''
                ]);
                error_log("Created folder: $folder");
            } catch (Exception $e) {
                error_log("Error creating folder $folder: " . $e->getMessage());
            }
        }
    } catch (Exception $e) {
        error_log("Error creating mission folders: " . $e->getMessage());
        throw $e;
    }
}