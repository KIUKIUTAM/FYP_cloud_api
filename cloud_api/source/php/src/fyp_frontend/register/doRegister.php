<?php
session_start();

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.php");
    exit();
}

// Include database connection
require_once '../_inc/db.inc.php';

// Validate input
$username = trim($_POST['username']);
$password = trim($_POST['password']);
$confirm_password = trim($_POST['confirm_password']);
$realname = trim($_POST['realname']);
$email = trim($_POST['email']);

// Simple validation
if (empty($username) || empty($password) || empty($realname) || empty($email)) {
    header("Location: index.php?error=All fields are required");
    exit();
}

// Check if password and confirm password match
if ($password !== $confirm_password) {
    header("Location: index.php?error=Passwords do not match");
    exit();
}

// Connect to database
try {
    $pdo = dbconnect();
    
    // Check if username already exists
    $stmt = $pdo->prepare("SELECT * FROM usertable WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() > 0) {
        header("Location: index.php?error=Username already exists");
        exit();
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT * FROM usertable WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->rowCount() > 0) {
        header("Location: index.php?error=Email already in use");
        exit();
    }
    
    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO usertable (username, password, realname, email, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $hashed_password, $realname, $email, 1]); // Role 1 = regular user
    
    $user_id = $pdo->lastInsertId();
    
    // Redirect to login page with success message
    header("Location: ../login/index.php?registration=success");
    exit();
    
} catch (PDOException $e) {
    header("Location: index.php?error=Database error: " . $e->getMessage());
    exit();
}
?>