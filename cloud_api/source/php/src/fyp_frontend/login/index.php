<?php
    session_start();
    if (array_key_exists('userid',$_SESSION)){
      header('location: ../dashboard/');
    }
?>



<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Drone Navigation System - Login">
    <meta name="author" content="Drone Navigation Team">
    <title>Drone Navigation System - Login</title>

    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      body {
        height: 100vh;
        display: flex;
        align-items: center;
        background: #ffffff;
      }
      
      .form-signin {
        max-width: 400px;
        padding: 30px;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 10px;
        border: 3px solid #eaeaea;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }
      
      .form-signin input {
        margin-bottom: 15px;
        border-radius: 5px;
      }
      
      .logo-container {
        text-align: center;
        margin-bottom: 20px;
      }
      
      .logo {
        font-size: 2.5rem;
        font-weight: 700;
        color: rgba(0, 0, 0, .85);
        margin-bottom: 10px;
      }
      
      .logo-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        color: rgba(0, 0, 0, .85);
      }
      
      .btn-primary {
        background-color: #ffffff;
        border-color: rgba(0, 0, 0, .85);
        color: rgba(0, 0, 0, .85);
      }
      
      .btn-primary:hover {
        background-color: #f8f9fa;
        border-color: #4e4376;
        color: #4e4376;
      }
      
      .btn-secondary {
        background-color: #ffffff;
        border-color: #6c757d;
        color: #6c757d;
      }
      
      .btn-secondary:hover {
        background-color: #f8f9fa;
        border-color: #6c757d;
        color: #6c757d;
      }
    </style>
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  </head>
  <body class="text-center">
   
    <main class="form-signin w-100 m-auto">
        <!-- If there is a failed login attempt, show the message-->
        <?php
            if (array_key_exists('attempt', $_GET)){
                if ($_GET['attempt'] == 1){
        ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>Login failed! Please check your credentials.
        </div>
        <?php
                }
            }
        ?>
        <?php
            if (array_key_exists('banned', $_GET)){
                if ($_GET['banned'] == 1){
        ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-shield-exclamation me-2"></i>Your IP address has been temporarily blocked.
        </div>
        <?php
                }
            }
        ?>
        <?php
            if (array_key_exists('registration', $_GET)){
                if ($_GET['registration'] == 'success'){
        ?>
        <div class="alert alert-success" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>Registration successful! You can now log in.
        </div>
        <?php
                }
            }
        ?>
        
        <div class="logo-container">
            <i class="bi bi-drone-fill logo-icon"></i>
            <div class="logo">Drone Navigation</div>
            <p class="text-muted">Sign in to access your dashboard</p>
        </div>
        
        <form method="post" action="doLogin.php">
            <div class="form-floating">
                <input type="text" class="form-control" id="floatingInput" name="username" required>
                <label for="floatingInput"><i class="bi bi-person-fill me-2"></i>Username</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="floatingPassword" name="password" required>
                <label for="floatingPassword"><i class="bi bi-lock-fill me-2"></i>Password</label>
            </div>

            <div class="form-check text-start mb-3">
                <input type="checkbox" class="form-check-input" id="remember" value="remember-me">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-primary py-2" type="submit">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Sign in
                </button>
                <a class="btn btn-secondary py-2" href="../" role="button">
                    <i class="bi bi-house-door-fill me-2"></i>Back to Home
                </a>
            </div>
            <div class="mt-3 text-center">
                <p>Don't have an account? <a href="../register/">Register here</a></p>
            </div>
        </form>
    </main>

    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
