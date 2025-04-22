<?php
require '../../vendor/autoload.php';

use Aws\S3\S3Client;

$s3 = new S3Client([
    'version'     => 'latest',
    'region'      => 'us-east-1', 
    'endpoint'    => 'http://minio:9000',
    'use_path_style_endpoint' => true,
    'credentials' => [
        'key'    => 'EUEvKo0hWLDvsnoPojsD',
        'secret' => 'ZqXptllPy7ypnK96Capv5rU6H5RF4a5ms3w8lHP6',
    ],
]);

try {
    // List buckets to test connection
    $buckets = $s3->listBuckets();
    echo "Connection successful! Buckets: ";
    print_r($buckets['Buckets']);
} catch (Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}