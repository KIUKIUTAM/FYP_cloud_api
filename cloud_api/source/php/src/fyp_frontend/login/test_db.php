<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=cloud_api', 'root', 'password');
    echo "Connection successful!";
    foreach ($pdo->query("SHOW DATABASES") as $row) {
        print_r($row);
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}