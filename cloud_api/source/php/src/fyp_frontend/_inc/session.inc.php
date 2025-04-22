<?php
   //filter_var
   session_start();
   date_default_timezone_set('Asia/Hong_Kong');
   if ( !array_key_exists('expiry',$_SESSION) // have not logged in
   || time() > $_SESSION['expiry'] ) { // session is expired
   session_destroy();
   session_unset();
   // redirect to login form
   header('Location: ../login');
   exit();
   } else {
      $_SESSION["expiry"] = time() + 900; // EXTEND 15 mins session
   }

   $str_client_ip = $_SERVER['REMOTE_ADDR'];
?>