<?php
    session_start();
    $hasSession = false;
    if (array_key_exists('userid',$_SESSION)){
      $hasSession = true;
    }
    session_destroy();
    session_unset();
    if (!$hasSession){
      header('location: ../login/');
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Drone Navigation System - Logout">
    <meta name="author" content="Drone Navigation Team">
    <title>Drone Navigation System - Logout</title>

    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
      body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #ffffff;
        font-size: 0.875rem;
        min-height: 100vh;
        min-height: -webkit-fill-available;
      }
      
      html {
        height: -webkit-fill-available;
      }
      
      .navbar {
        background-color: #333;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border-bottom: 1px solid #eaeaea;
      }
      
      .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: #ffffff;
        pointer-events: none;
        text-decoration: none;
      }
      
      .navbar-nav .nav-link {
        color: #ffffff;
      }
      

      
      .navbar-nav .nav-link i {
        color: #ffffff;
      }
      
      .hero-section {
        background-color: #ffffff;
        color: rgba(0, 0, 0, .85);
        padding: 4rem 0;
        margin-bottom: 2rem;
        border-bottom: 1px solid #eaeaea;
      }
      
      .btn-primary {
        background-color: #ffffff;
        border-color: rgba(0, 0, 0, .85);
        color: rgba(0, 0, 0, .85);
        padding: 0.5rem 1.5rem;
        border-radius: 5px;
        font-weight: 500;
      }
      
      .btn-primary:hover {
        background-color: #f8f9fa;
        border-color: rgba(0, 0, 0, .85);
        color: rgba(0, 0, 0, .85);
      }
    </style>
  </head>
  <body>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-md navbar-light sticky-top">
      <div class="container">
        <a class="navbar-brand" href="#">
          <i class="bi bi-drone-fill me-2"></i>Drone Navigation
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <a class="nav-link " aria-current="page" href="../index.php">
                <i class="bi bi-house-door-fill me-1"></i>Home
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="../login/index.php">
                <i class="bi bi-box-arrow-in-right me-1"></i>Login
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
      <div class="container">
        <div class="row justify-content-center text-center">
          <div class="col-lg-8">
            <h2 class="fw-bold mb-3" style="color: rgba(0, 0, 0, .85);">You have been logged out</h2>
            <p class="lead mb-4">Thank you for using our drone navigation system. You can log back in anytime.</p>
            <a href="../login/" class="btn btn-primary btn-lg px-4">
              <i class="bi bi-box-arrow-in-right me-2"></i>Log In Again
            </a>
          </div>
        </div>
      </div>
    </section>

    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
