<?php
require_once '_inc/db.inc.php'; 
$pdo = dbconnect();
  try {
    $pdo = dbconnect();
    $sql = 'SELECT * from usertable';	  
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $dbUser = $stmt->fetch();
  } catch(PDOException $e) {
	  die($e->getMessage());	  
  }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Drone Navigation System - Modern aerial monitoring and detection">
    <meta name="author" content="Drone Navigation Team">
    <title>Drone Navigation System - Aerial Monitoring</title>

    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
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
      
      .feather {
        width: 16px;
        height: 16px;
      }
      
      main {
        height: 100vh;
        height: -webkit-fill-available;
        max-height: 100vh;
        overflow-x: auto;
        overflow-y: hidden;
      }
      
      .navbar {
        background-color: #333;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      }
      
      .navbar-brand {
        font-weight: 700;
        font-size: 1.5rem;
        color: #ffffff;
      }
      
      .hero-section {
        background-color: #ffffff;
        color: rgba(0, 0, 0, .85);
        padding: 4rem 0;
        margin-bottom: 2rem;
        border-bottom: 1px solid #f0f0f0;
      }
      
      .feature-card {
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 1.5rem;
        border: 1px solid #eaeaea;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      }
      
      .feature-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
      }
      
      .feature-icon {
        font-size: 2.5rem;
        margin-bottom: 1rem;
        color: rgba(0, 0, 0, .85);
      }
      
      .feature-card .card-title {
        font-weight: 600;
        color: rgba(0, 0, 0, .85);
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
        border-color: #4e4376;
        color: rgba(0, 0, 0, .85);
      }
      
      .btn-outline-primary {
        background-color: transparent;
        color: rgba(0, 0, 0, .85);
        border-color: rgba(0, 0, 0, .85);
      }
      
      .btn-outline-primary:hover {
        background-color: #f8f9fa;
        color: #4e4376;
        border-color: #4e4376;
      }
      
      footer {
        background-color: #f8f9fa;
        color: rgba(0, 0, 0, .85);
        padding: 2rem 0;
        margin-top: 3rem;
        border-top: 1px solid #eaeaea;
      }
      
      /* Sidebar styles */
      .sidebar {
        position: -webkit-sticky;
        position: sticky;
        top: 0;
        height: 100vh;
        overflow-y: auto;
        z-index: 1000;
      }
      
      @media (max-width: 767.98px) {
        .main-content {
          margin-left: 0;
          width: 100%;
        }
      }
      
      .sidebar-sticky {
        height: calc(100vh - 48px);
        overflow-x: hidden;
        overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
      }
      
      .sidebar .nav-link {
        font-weight: 500;
        color: rgba(0, 0, 0, .85);
      }
      
      .sidebar .nav-link .feather {
        margin-right: 4px;
        color: #727272;
      }
      
      .sidebar .nav-link.active {
        color: #2470dc;
      }
      
      .sidebar .nav-link:hover .feather,
      .sidebar .nav-link.active .feather {
        color: inherit;
      }
      
      .sidebar-heading {
        font-size: 0.75rem;
        color: rgba(0, 0, 0, .85);
      }
      
      /* Navbar styles */
      .navbar .navbar-toggler {
        top: 0.25rem;
        right: 1rem;
      }
      
      .navbar .form-control {
        padding: 0.75rem 1rem;
      }
      
      .form-control-dark {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.1);
      }
      
      .form-control-dark:focus {
        border-color: transparent;
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.25);
      }
      
      /* Button toggle styles */
      .dropdown-toggle { 
        outline: 0; 
      }
      
      .btn-toggle {
        padding: .25rem .5rem;
        font-weight: 600;
        color: rgba(0, 0, 0, .65);
        background-color: transparent;
      }
      
      .btn-toggle:hover,
      .btn-toggle:focus {
        color: rgba(0, 0, 0, .85);
        background-color: #d2f4ea;
      }
      
      .btn-toggle::before {
        width: 1.25em;
        line-height: 0;
        content: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='rgba%280,0,0,.5%29' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M5 14l6-6-6-6'/%3e%3c/svg%3e");
        transition: transform .35s ease;
        transform-origin: .5em 50%;
      }
      
      .btn-toggle[aria-expanded="true"] {
        color: rgba(0, 0, 0, .85);
      }
      
      .btn-toggle[aria-expanded="true"]::before {
        transform: rotate(90deg);
      }
      
      .btn-toggle-nav a {
        padding: .1875rem .5rem;
        margin-top: .125rem;
        margin-left: 1.25rem;
      }
      
      .btn-toggle-nav a:hover,
      .btn-toggle-nav a:focus {
        background-color: #d2f4ea;
      }
      
      .scrollarea {
        overflow-y: auto;
      }
      
      .hero-drone-img {
        max-width: 400px;
        height: auto;
      }
    </style>
  </head>
  <body>
    
    <!-- Navigation -->
    <nav class="navbar navbar-expand-md navbar-dark sticky-top">
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
              <a class="nav-link active" aria-current="page" href="#">
                <i class="bi bi-house-door-fill me-1"></i>Home
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="#features">
                <i class="bi bi-grid-fill me-1"></i>Features
              </a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="login/">
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
        <div class="row align-items-center">
          <div class="col-lg-6">
            <h1 class="display-4 fw-bold mb-3" style="color: rgba(0, 0, 0, .85);">Advanced Drone Navigation System</h1>
            <p class="lead mb-4">Automate your aerial monitoring and detection with our state-of-the-art drone navigation platform</p>
            <div class="d-grid gap-2 d-md-flex justify-content-md-start">
              <a href="login/" class="btn btn-primary btn-lg px-4 me-md-2">
                <i class="bi bi-box-arrow-in-right me-2"></i>Get Started
              </a>
              <a href="#features" class="btn btn-outline-primary btn-lg px-4">
                <i class="bi bi-info-circle me-2"></i>Learn More
              </a>
            </div>
          </div>
          <div class="col-lg-6 text-center">
            <img src="assets/img/drone.svg" class="img-fluid hero-drone-img" alt="Drone Illustration">
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="container py-5">
      <div class="row text-center mb-5">
        <div class="col-12">
          <h2 class="fw-bold" style="color: rgba(0, 0, 0, .85);">Key Features</h2>
          <p class="lead text-muted">Our platform offers everything you need for efficient aerial monitoring</p>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-4">
          <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
              <i class="bi bi-map feature-icon"></i>
              <h3 class="card-title">Mission Planning</h3>
              <p class="card-text">Plan complex flight paths with precise coordinates and mission parameters for optimal coverage.</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
              <i class="bi bi-camera feature-icon"></i>
              <h3 class="card-title">Crack Detection</h3>
              <p class="card-text">Utilize AI-powered image recognition to automatically detect and categorize structural cracks.</p>
            </div>
          </div>
        </div>
        
        <div class="col-md-4">
          <div class="card feature-card h-100">
            <div class="card-body text-center p-4">
              <i class="bi bi-cloud-download feature-icon"></i>
              <h3 class="card-title">Image Storage</h3>
              <p class="card-text">Access and download mission images securely stored in our MinIO-powered cloud storage system.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Call to Action -->
    <section class="bg-light py-5 mt-5">
      <div class="container">
        <div class="row justify-content-center text-center">
          <div class="col-lg-8">
            <h2 class="fw-bold mb-3" style="color: rgba(0, 0, 0, .85);">Ready to Start?</h2>
            <p class="lead mb-4">Log in to access the complete functionality of our drone navigation system.</p>
            <a href="login/" class="btn btn-primary btn-lg px-4">
              <i class="bi bi-person-plus-fill me-2"></i>Sign In Now
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- Footer -->
    <footer>
      <div class="container">
        <div class="row">
          <div class="col-md-6">
            <h5 style="color: rgba(0, 0, 0, .85);">Drone Navigation System</h5>
            <p class="small">Advanced aerial monitoring and detection platform</p>
          </div>
          <div class="col-md-6 text-md-end">
            <p class="small">&copy; <?php echo date('Y'); ?> Drone Navigation. All rights reserved.</p>
          </div>
        </div>
      </div>
    </footer>

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
