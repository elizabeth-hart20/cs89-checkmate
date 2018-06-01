<?php

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();
var_dump($_POST);
  var_dump($_SERVER['HTTP_REFERER']);

$set_date = $_POST["set_date"];
$friendUsername = $_POST['friendUsername'];

$userQuery = "SELECT * from phone_data WHERE ";
  
  
  
  
  
 header('Location: ' . $_SERVER['HTTP_REFERER']);