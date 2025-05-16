<?php
require '../../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

// Create MinIO client with explicit configuration
$minio = new S3Client([
    'version' => 'latest',
    'region' => 'ap-east-1',
    'endpoint' => 'http://host.docker.internal:9000',
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key' => '3ZOI6Z2wSmtovXRzhDfr',
        'secret' => 'UrAZe6pc0fgkGMpSOB4WxglvjC5ejCSSfdhhDp0I',
    ],
    'http' => [
        'verify' => false, // Disable SSL verification for local testing
        'timeout' => 5,
        'connect_timeout' => 5
    ]
]);

try {
    // Test connection by listing buckets
    echo "Testing MinIO connection...\n";
    
    // 1. Check if bucket exists
    $bucket = 'crack-detection';
    echo "Checking if bucket '$bucket' exists...\n";
    if ($minio->doesBucketExist($bucket)) {
        echo "Bucket exists.\n";
    } else {
        echo "Bucket does not exist. Creating...\n";
        $minio->createBucket($bucket);
        echo "Bucket created successfully.\n";
    }
    
    // 2. Test folder creation
    $testFolder = "test_folder/";
    echo "Creating test folder...\n";
    $result = $minio->putObject([
        'Bucket' => $bucket,
        'Key' => $testFolder,
        'Body' => ''
    ]);
    echo "Test folder created successfully.\n";
    
    // 3. List objects to verify
    echo "Listing objects in bucket...\n";
    $objects = $minio->listObjectsV2([
        'Bucket' => $bucket
    ]);
    foreach ($objects['Contents'] as $object) {
        echo "Found object: " . $object['Key'] . "\n";
    }
    
    echo "\nAll tests passed successfully!\n";
    
} catch (AwsException $e) {
    echo "AWS Error: " . $e->getMessage() . "\n";
    echo "AWS Error Code: " . $e->getAwsErrorCode() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
