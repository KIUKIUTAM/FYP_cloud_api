<?php
  //Check session
  include_once('../_inc/session.inc.php');
  include_once('_main/getMainBody.php'); 
  // show content to logged in users
  $str_username = $_SESSION['username'];
  $str_userid = $_SESSION['userid'];
?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Hugo 0.108.0">
    <title>Drone Dashboard</title>

    <link rel="canonical" href="https://getbootstrap.com/docs/5.3/examples/dashboard/">


    <link href="../assets/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }

      .b-example-divider {
        height: 3rem;
        background-color: rgba(0, 0, 0, .1);
        border: solid rgba(0, 0, 0, .15);
        border-width: 1px 0;
        box-shadow: inset 0 .5em 1.5em rgba(0, 0, 0, .1), inset 0 .125em .5em rgba(0, 0, 0, .15);
      }

      .b-example-vr {
        flex-shrink: 0;
        width: 1.5rem;
        height: 100vh;
      }

      .bi {
        vertical-align: -.125em;
        fill: currentColor;
      }

      .nav-scroller {
        position: relative;
        z-index: 2;
        height: 2.75rem;
        overflow-y: hidden;
      }

      .nav-scroller .nav {
        display: flex;
        flex-wrap: nowrap;
        padding-bottom: 1rem;
        margin-top: -1px;
        overflow-x: auto;
        text-align: center;
        white-space: nowrap;
        -webkit-overflow-scrolling: touch;
      }
    </style>

    
    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">
    <link href="sidebars.css" rel="stylesheet">
  </head>
  <body>
  <?php include_once('_inc/icon_svg.inc'); ?>
    
<div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse p-0">
      <?php include_once('_inc/sidebar.inc.php'); ?>
    </nav>
    <div class="col-md-9 col-lg-10 main-content px-0">
      <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
      <?php include_once('_inc/navbar.inc.php'); ?>
      </header>

    <?php

      if (array_key_exists('page', $_GET)){
        $pageMin = 1;
        $pageMax = 7;

        if (!filter_var($_GET['page'], FILTER_VALIDATE_INT, array("options"=> array("min_range"=>$pageMin, "max_range"=>$pageMax)))){
          //Invalid page id, send home page
          getMainBody(0);
        } else {
          
          $requested_page = intval($_GET['page']);
          getMainBody($requested_page);
        }

      } else {

        getMainBody(0);
        
      }

    ?>
    </div>
  </div>
</div>

    <script src="../assets/dist/js/bootstrap.bundle.min.js"></script>

      <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js" integrity="sha384-uO3SXW5IuS1ZpFPKugNNWqTZRRglnUJK6UAZ/gxOX80nxEkN9NcGZTftn6RzhGWE" crossorigin="anonymous"></script><script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js" integrity="sha384-zNy6FEbO50N+Cg5wap8IKA4M/ZnLJgzc6w2NqACZaK0u0FXfOWRRJOnQtpZun8ha" crossorigin="anonymous"></script><script src="dashboard.js"></script>
  </body>
</html>
