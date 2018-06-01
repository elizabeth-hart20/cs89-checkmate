<?php

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();


$senderUsername = $_POST['senderUsername'];
$receiverUsername = $_POST["receiverUsername"];
$send_date = $_POST["send_date"];
$message = $_POST["message"];
// $mysqli = $_POST('mysqli');


$message_query = "INSERT INTO user_messages (receiver, sender, date, message) 
  VALUES ('$receiverUsername', '$senderUsername', '$send_date', '$message')"; 

$status = mysqli_query($mysqli, $message_query);
    if ($status == false) {
            echo '<script>console.log("false")</script>';
        } else {
            echo '<script>console.log("true")</script>';
        }


header('Location: ' . $_SERVER['HTTP_REFERER']);

