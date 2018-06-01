<?php

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();

$username = $_POST["username"];
$set_date = $_POST["set_date"];
$pickupGoal = (int)$_POST['pickupGoal'];
$minuteGoal = (int)$_POST['minuteGoal'];

if(!is_int($pickupGoal) || !is_int($minuteGoal)) {
  echo '<script>console.log("Only integer goals accepted")</script>';
}

else {
  $insert_query = "INSERT INTO goals (username, set_date, pickupGoal, minuteGoal) 
  VALUES ('$username', '$set_date', '$pickupGoal', '$minuteGoal')";
$status = mysqli_query($mysqli, $insert_query);
    if ($status == false) {
            echo '<script>console.log("false")</script>';
        } else {
            echo '<script>console.log("true")</script>';
        }
}



header('Location: ' . $_SERVER['HTTP_REFERER']);

