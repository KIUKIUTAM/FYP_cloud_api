<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['userid'])) {
    header('Location: ../dashboard/');
    exit;
}

require_once '../_inc/db.inc.php';
require_once '../_inc/loginAttemptRecord.php';

// Function to sanitize input
function sanitize($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = sanitize($_POST['username']);
        $password = $_POST['password'];
        
        try {
            $pdo = dbconnect();
            $stmt = $pdo->prepare('SELECT username, password, user_id FROM usertable WHERE username = :username');
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Update login record
                if (!updateLoginRecord($_SERVER['REMOTE_ADDR'], 1)) {
                    header('Location: index.php?banned=1');
                    exit;
                }

                // Set session variables with secure flags
                $_SESSION['expiry'] = time() + 900; // 15 minutes
                $_SESSION['userid'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                header('Location: ../dashboard/');
                exit;
            } else {
                // Record failed login attempt
                if (!updateLoginRecord($_SERVER['REMOTE_ADDR'], 0)) {
                    header('Location: index.php?banned=1');
                    exit;
                }
                
                header('Location: index.php?attempt=1');
                exit;
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            header('Location: index.php?error=1');
            exit;
        }
    } else {
        header('Location: index.php?error=1');
        exit;
    }
} else {
    header('Location: index.php?error=1');
    exit;
}
?>