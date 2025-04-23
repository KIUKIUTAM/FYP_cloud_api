<?php
function dbconnect() {
  $dsn = 'mysql:dbname=cloud_api;host=mysql;charset=UTF8';
  $dbuser = 'drone123';
  $dbpwd = '1234';
  try {
    $pdo = new PDO($dsn, $dbuser, $dbpwd);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  } catch (PDOException $e) {
    die('Database Error: ' . $e->getMessage());
  }
}