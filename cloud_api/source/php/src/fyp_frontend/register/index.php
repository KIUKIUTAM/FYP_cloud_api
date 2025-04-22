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
    <meta name="description" content="Drone Navigation System - Registration">
    <meta name="author" content="Drone Navigation Team">
    <title>Drone Navigation System - Register</title>

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
        <!-- Registration alerts -->
        <?php
            if (array_key_exists('error', $_GET)){
        ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
        </div>
        <?php
            }
        ?>
        
        <div class="logo-container">
            <i class="bi bi-drone-fill logo-icon"></i>
            <div class="logo">Drone Navigation</div>
            <p class="text-muted">Create a new account</p>
        </div>
        
        <form method="post" action="doRegister.php">
            <div class="form-floating">
                <input type="text" class="form-control" id="username" name="username" required>
                <label for="username"><i class="bi bi-person-fill me-2"></i>Username</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" required>
                <label for="password"><i class="bi bi-lock-fill me-2"></i>Password</label>
            </div>
            <div class="form-floating">
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                <label for="confirm_password"><i class="bi bi-lock-fill me-2"></i>Confirm Password</label>
            </div>
            <div class="form-floating">
                <input type="text" class="form-control" id="realname" name="realname" required>
                <label for="realname"><i class="bi bi-person-badge-fill me-2"></i>Full Name</label>
            </div>
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" required>
                <label for="email"><i class="bi bi-envelope-fill me-2"></i>Email Address</label>
            </div>

            <div class="d-grid gap-2">
                <button class="btn btn-primary py-2" type="submit">
                    <i class="bi bi-person-plus-fill me-2"></i>Register
                </button>
                <a class="btn btn-secondary py-2" href="../login/" role="button">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Back to Login
                </a>
            </div>
        </form>
    </main>

    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>