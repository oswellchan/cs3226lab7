<?php 
require_once 'connect.php';
global $db;
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] != 0) {
  header("Location: login.php");
  exit;
}

$query = "UPDATE SOLUTION SET NUM_MATCHED=0, SCORE=0;";
$res = $db->query($query);

header("Location: admin.php");
?>