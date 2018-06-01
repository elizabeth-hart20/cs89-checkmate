<?php

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

sec_session_start();

    $username = $_POST['username'];
    $email = $_POST["email"];
    $question_number = intval($_POST["number_id"]);
    $answer = $_POST["answer"];
    $reg_date = $_POST["reg_date"];

    $total_query = mysqli_query($mysqli, "SELECT question_number FROM survey_questions ORDER BY question_number DESC LIMIT 1;");
    $question_total_string = mysqli_fetch_assoc($total_query)['question_number'];
    $question_total = intval($question_total_string);

    $answer_sql = "INSERT INTO member_surveys (username, question_number, reg_date, answer) VALUES ('$username', " . $question_number . ", 
      '" . ($mysqltime = date ("Y-m-d", $reg_date)) . "', '" . $answer . "' )";


header('Location: ' . $_SERVER['HTTP_REFERER']);