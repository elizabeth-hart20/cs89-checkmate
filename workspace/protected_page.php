<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';
error_reporting(E_ALL ^ E_NOTICE);  
 
sec_session_start();


if (isset($_GET['email'])) {
  $email = $_GET['email'];
}
else {
  echo '<script>console.log("email not set, sad!")</script>';
  $email = " ";
}
$data_query = "SELECT * FROM phone_data WHERE email = '$email'";
$data=mysqli_query($mysqli, $data_query);
$date = time();

$mysqldate = date ("Y-m-d", $date);


$usernameQuery = "SELECT username FROM members WHERE email = '$email' LIMIT 1";
$usernameResult = mysqli_query($mysqli, $usernameQuery);
$username = mysqli_fetch_assoc($usernameResult)['username'];

$questionQueryOne = "SELECT question FROM survey_questions WHERE question_number = 1";
$questionOneResult = mysqli_query($mysqli, $questionQueryOne);
$questionOne = mysqli_fetch_assoc($questionOneResult)['question'];
$answerOne = getQuestionResponse($mysqli, $username, $mysqldate, 1);

$questionQueryTwo = "SELECT question FROM survey_questions WHERE question_number = 2";
$questionTwoResult = mysqli_query($mysqli, $questionQueryTwo);
$questionTwo = mysqli_fetch_assoc($questionTwoResult)['question'];
$answerTwo = getQuestionResponse($mysqli, $username, $mysqldate, 2);


$questionQueryThree = "SELECT question FROM survey_questions WHERE question_number = 3";
$questionThreeResult = mysqli_query($mysqli, $questionQueryThree);
$questionThree = mysqli_fetch_assoc($questionThreeResult)['question'];
$answerThree = getQuestionResponse($mysqli, $username, $mysqldate, 3);

$questionQueryFour = "SELECT question FROM survey_questions WHERE question_number = 4";
$questionFourResult = mysqli_query($mysqli, $questionQueryFour);
$questionFour = mysqli_fetch_assoc($questionFourResult)['question'];
$answerFour = getQuestionResponse($mysqli, $username, $mysqldate, 4);

$number_id = 1;

// get average stats for all and average for current user
$userMinuteQuery = "SELECT AVG(minuteCount) FROM phone_data WHERE email = '$email'";
$userMinuteAverage = getAverage($userMinuteQuery, $mysqli, "minuteCount");

$userPickupQuery = "SELECT AVG(pickupCount) FROM phone_data WHERE email = '$email'";
$userPickupAverage = getAverage($userPickupQuery, $mysqli, "pickupCount");

$minuteQuery = "SELECT AVG(minuteCount) FROM phone_data";
$minuteAverage = getAverage($minuteQuery, $mysqli, "minuteCount");

$pickupQuery = "SELECT AVG(pickupCount) FROM phone_data";
$pickupAverage = getAverage($pickupQuery, $mysqli, "pickupCount");

$data=mysqli_query($mysqli,"SELECT * FROM phone_data WHERE email = '$email'");
while ($row = mysqli_fetch_array($data)) {
	$minuteRows[]=(int)($row['minuteCount']);
	$pickupRows[]=(int)($row['pickupCount']);
}

$stand_dev_minute = round(Stand_Deviation($minuteRows), 2);
$stand_dev_pickup = round(Stand_Deviation($pickupRows), 2);

$goals = getCurrentGoals($mysqli, $username);
$pickupGoal = mysqli_fetch_assoc($goals)['pickupGoal'];
$goals = getCurrentGoals($mysqli, $username);
$minuteGoal = mysqli_fetch_assoc($goals)['minuteGoal'];

	$receiverUsername = $username;
	$userQuery = "SELECT email from members WHERE username = '$receiverUsername'";
	$friendResult = mysqli_query($mysqli, $userQuery);
	if (!$friendResult || mysqli_num_rows($friendResult) == 0) {
		echo '<script>console.log("No member with given username")</script>';
	}
	else {
		$messageQuery = "SELECT * FROM user_messages WHERE receiver = '$username'";
		$messageResult = mysqli_query($mysqli, $messageQuery);
		while($row = $messageResult->fetch_row()) {
			$messages[] = $row;
		}
	}

?>


<script>
	<?php
    $data=mysqli_query($mysqli,"SELECT * FROM phone_data WHERE email = '$email'");
    ?>
    var pickupData=[<?php 
        while($pickupInfo=mysqli_fetch_array($data))
            echo $pickupInfo['pickupCount'].','; /* We use the concatenation operator '.' to add comma delimiters after each data value. */
        ?>];
    <?php
    $data=mysqli_query($mysqli,"SELECT * FROM phone_data WHERE email = '$email'");
    ?>
    var minuteData=[<?php 
      while($minuteInfo=mysqli_fetch_array($data))
          echo $minuteInfo['minuteCount'].','; /* We use the concatenation operator '.' to add comma delimiters after each data value. */
      ?>];
    <?php
		$date_query = "SELECT * FROM phone_data WHERE email = '$email'";
    $data=mysqli_query($mysqli, $date_query);
    ?>
    var dateLabel=[<?php 
    while($dateInfo=mysqli_fetch_array($data))
        echo '"'.$dateInfo['reg_date'].'", '; /* The concatenation operator '.' is used here to create string values from our database names. */        
    ?>];
	
	var pickupGoalLabel = [];
	for (var i = 0; i < dateLabel.length; i++) {
		pickupGoalLabel[i] = <?php echo $pickupGoal; ?>;
	}
	
	var minuteGoalLabel = [];
	for (var i = 0; i < dateLabel.length; i++) {
		minuteGoalLabel[i] = <?php echo $minuteGoal; ?>;
	}

	
	function hideFunction(divID, button) {
    var x = document.getElementById(divID);
    if (x.style.display === "none") {
        x.style.display = "inline-block";
				button.style.display = 'none';
    } else {
        x.style.display = "none";
    }
	}
	
	function showStats() {
		console.log('in showstats')
		var friendStats = document.getElementById("friendStats");
		console.log(friendStats.style);
		if (friendStats.style.display === "none") {
				console.log("showing stats inline-block");
        friendStats.style.display = "inline-block";
    }
	}
	
</script>


<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
        <title>Checkmate</title>
        <link rel="stylesheet" href="styles/main.css" />
<!-- 				<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/tables-min.css"> -->
        <script src= "https://cdn.zingchart.com/zingchart.min.js"></script>
		    <script> zingchart.MODULESDIR = "https://cdn.zingchart.com/modules/";
		    ZC.LICENSE = ["569d52cefae586f634c54f86dc99e6a9","ee6b7db5b51705a13dc2339db3edaf6d"];</script></head>
				<meta name="keywords" content="" />
				<meta name="description" content="" />
</head>

<body>

<div class="wrapper">

	<header class="header">
		<span id="welcome">Welcome, <?php echo $username ?>!</span>
		<span id="checkmate"><b>Checkmate: </b>No More Phone Addiction</span><br>
			<form action="includes/logout.php" method="get">
				<input type="submit" value="Logout">
			</form>
	</header><!-- .header-->
	
	<div class="containerGraph">
			<main class="content">
					<div id='myChart'></div>
      <script>
				function dateFormat(dateArray) {
					for (var i=0; i<dateArray.length; i++) {
						var date = new Date(dateArray[i]);

						var year = date.getFullYear();
						var month = date.getMonth()+1;
						var day = date.getDate();

						if (day < 10) {
							day = '0' + day;
						}
						if (month < 10) {
							month = '0' + month;
						}

						var formattedDate = month + '-' + day + '-' + year;
						dateArray[i] = formattedDate;
					}
					return dateArray;
					
				}
				
        window.onload=function(){
          zingchart.render({
              id:"myChart",
              width:"100%",
              height:400,
              data:{
              "type":"line",
							"labels":[
								{
									"text": "Standard Deviation: \n Minutes: <?php echo $stand_dev_minute ?> \n Pickups: <?php echo $stand_dev_pickup ?>",
									"font-size":"10",
									"x":"1%",
									"y":"2%",
									"border-radius":"5px",
									"fontStyle": 'bold'
								}
							],
              "legend":{
    
              },
              "title":{
                  "text":"My Phone Data"
              },
              "scale-x":{
                  "labels":dateFormat(dateLabel)
              },
              "series":[
                {
                      "values":minuteData,
                      "text": "Minutes on Phone"
                  },
								{
                      "values":minuteGoalLabel,
                      "text": "Minutes Goal",
											"line-style":"dashed",
											"marker": {
												"size": 0
											}
                  },
                {
                      "values":pickupData,
                      "text": "Pickup Count"
                  },
								{
                      "values":pickupGoalLabel,
                      "text": "Pickup Goal",
											"line-style":"dashed",
											"marker": {
												"size": 0
											}
                  }
          		]
          }
          });
          };

      </script>
		</div><!-- .container-->

		<div class="containerSurvey">
			<table style="display:table;">
				<tr>
					<td>
				<div id="surveyQuestionOne" class="surveyQuestion">
					<form method="POST" class="surveyForm" >
						<label>
					<b ><?php echo $questionOne;?></b> <br>
							</label>
					<input type="hidden" name="questionName" id="questionName" value="surveyQuestionOne">
					<input type="hidden" name="username" id="username_id" value="<?php echo $username; ?>">
					<input type="hidden" name="email" id="email_id" value="<?php echo $email; ?>">
					<input type="hidden" name="reg_date" id="date_id" value="<?php echo $date; ?>">
					<input type="hidden" name="number_id" id="number_id" value=1>
					<textarea name="answer" rows="5" cols="40"></textarea><br>
					<button type="submit" value="submit">Submit</button>
				</form>
				</div>
						</td>
					<td>
				<div class="questionHistory">
					<button class="button" onclick="hideFunction('historyOneDiv', this)">See Response History</button>
					<div id="historyOneDiv" class="historyDiv">
						 <p>
							  <?php 
							 		$pastDay = subtractDate($mysqldate, '1');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '1');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
								<?php 
							 		$pastDay = subtractDate($mysqldate, '2');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '1');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							 	<?php 
							 		$pastDay = subtractDate($mysqldate, '3');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '1');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
								 <?php 
										$pastDay = subtractDate($mysqldate, '4');
										$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '1');
										if ($pastResponse != false) {
											echo $pastDay . ': ' . $pastResponse;
										}
										else {
											echo $pastDay . ': No survey data';
										}
									?><br>
							 	<?php 
							 		$pastDay = subtractDate($mysqldate, '5');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '1');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
						</p>
					</div>

				</div>
						</td>
			</tr>

			<tr>
				<td>
				<div id="surveyQuestionTwo" class="surveyQuestion">
					<form method="POST">
					<b><?php echo $questionTwo;?></b> <br>
					<input type="hidden" name="username" id="username_id" value="<?php echo $username; ?>">
					<input type="hidden" name="email" id="email_id" value="<?php echo $email; ?>">
					<input type="hidden" name="reg_date" id="date_id" value="<?php echo $date; ?>">
					<input type="hidden" name="number_id" id="number_id" value=2>
					<textarea name="answer" rows="5" cols="40"></textarea><br>
					<button type="submit" value="submit">Submit</button>
				</form>
				</div>
				</td>
				<td>
				<div class="questionHistory">
					<button class="button" onclick="hideFunction('historyTwoDiv', this)">See Response History</button>

					<div id="historyTwoDiv" class="historyDiv">
						 <p>
							 <?php 
							 		$pastDay = subtractDate($mysqldate, '1');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '2');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '2');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '2');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '3');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '2');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '4');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '2');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '5');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '2');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
						</p>
					</div>

				</div>
				</td>
			</tr>
			<tr>
				<td>
				<div id="surveyQuestionThree" class="surveyQuestion">
					<form method="POST">
					<b><?php echo $questionThree;?></b> <br>
					<input type="hidden" name="username" id="username_id" value="<?php echo $username; ?>">
					<input type="hidden" name="email" id="email_id" value="<?php echo $email; ?>">
					<input type="hidden" name="reg_date" id="date_id" value="<?php echo $date; ?>">
					<input type="hidden" name="number_id" id="number_id" value=3>
					<textarea name="answer" rows="5" cols="40"></textarea><br>
					<button type="submit" value="submit">Submit</button>
				</form>
				</div>
					</td>
				<td>
				<div class="questionHistory">
					<button class="button" onclick="hideFunction('historyThreeDiv', this)">See Response History</button>

					<div id="historyThreeDiv" class="historyDiv">
						 <p>
							 <?php 
							 		$pastDay = subtractDate($mysqldate, '1');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '3');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '2');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '3');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '3');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '3');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '4');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '3');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '5');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '3');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
						</p>
					</div>

				</div>
				</td>
			</tr>
				
			<tr>
				<td>
				<div id="surveyQuestionFour" class="surveyQuestion">
					<form method="POST">
					<b><?php echo $questionFour;?></b> <br>
					<input type="hidden" name="username" id="username_id" value="<?php echo $username; ?>">
					<input type="hidden" name="email" id="email_id" value="<?php echo $email; ?>">
					<input type="hidden" name="reg_date" id="date_id" value="<?php echo $date; ?>">
					<input type="hidden" name="number_id" id="number_id" value=4>
					<textarea name="answer" rows="5" cols="40"></textarea><br>
					<button type="submit" value="submit">Submit</button>
				</form>
				</div>
					</td>
				<td>
				<div class="questionHistory">
					<button class="button" onclick="hideFunction('historyFourDiv', this)">See Response History</button>

					<div id="historyFourDiv" class="historyDiv">
						 <p>
							 <?php 
							 		$pastDay = subtractDate($mysqldate, '1');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '4');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '2');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '4');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '3');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '4');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '4');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '3');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
							<?php 
							 		$pastDay = subtractDate($mysqldate, '5');
							 		$pastResponse = getQuestionResponse($mysqli, $username, $pastDay, '4');
							 		if ($pastResponse != false) {
										echo $pastDay . ': ' . $pastResponse;
									}
							 		else {
										echo $pastDay . ': No survey data';
									}
							  ?><br>
						</p>
					</div>

				</div>
				</td>
			</tr>
				
			</table>

			
			<div id="thankYouMessage"><br>
				<b>Thank you for answering today's questions!</b>
			</div>

		</div>

		<div class="newGoals">
			<form method="POST" action="setGoal.php">
					<b>New goals?</b> <br>
					<input type="hidden" name="username" id="username_id" value="<?php echo $username; ?>">
					<input type="hidden" name="set_date" id="date_id" value="<?php echo $date; ?>">
					Pickups: <textarea name="pickupGoal" rows="1" cols="5"></textarea><br>
					Minutes: <textarea name="minuteGoal" rows="1" cols="5"></textarea><br>
					<button type="submit" value="submit" name="goals">Submit</button>
				</form>
			</div>
		<div class="containerStats">
			<table class="pure-table pure-table-bordered">
			<thead>
					<tr>
							<th>Average:</th>
							<th>Pickups</th>
							<th>Minutes</th>
							<th>Minutes/Pickup</th>
					</tr>
			</thead>

			<tbody>
					<tr>
							<td><b>My Stats</b></td>
							<td><?php echo (int)$userPickupAverage;?></td>
							<td><?php echo (int)$userMinuteAverage;?></td>
							<td><?php 
								if ((int)$userPickupAverage == 0) {
									echo 0;
								}
								else echo round(((int)$userMinuteAverage / (int)$userPickupAverage), 2);
								?></td>
					</tr>

					<tr>
							<td><b>Community</b></td>
							<td><?php echo (int)$pickupAverage;?></td>
							<td><?php echo (int)$minuteAverage;?></td>
							<td><?php 
								if ((int)$userMinuteAverage == 0) {
									echo 0;
								}
								else echo round(((int)$minuteAverage / (int)$pickupAverage), 2);
								?></td>
					</tr>

					<tr>
							<td><b>My Goal</b></td>
							<td><?php echo $pickupGoal; ?></td>
							<td><?php echo $minuteGoal; ?></td>
							<td><?php 
								if ((int)$pickupGoal == 0 || $pickupGoal == NULL) {
									echo '';
								}
								else echo round(((int)$minuteGoal / (int)$pickupGoal), 2); 
								?></td>
					</tr>
			</tbody>
			</table>
	</div>
		
<?php
	$status = false;

	if (isset($_POST) && count($_POST) > 0 && strlen($_POST["answer"]) > 0) {
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
    $status = mysqli_query($mysqli, $answer_sql);
    if ($status == false) {
            echo '<script>console.log("false")</script>';
        } else {
            echo '<script>console.log("true")</script>';
        }
		
	}

	if (strlen($_POST['statSubmit']) > 0) {
		$friendUsername = $_POST['friendUsername'];

		$userQuery = "SELECT email from members WHERE username = '$friendUsername'";
		$friendResult = mysqli_query($mysqli, $userQuery);
		if (!$friendResult || mysqli_num_rows($friendResult) == 0) {
			echo '<script>console.log("No member with given username")</script>';
		}
		else {
			$friendEmail = mysqli_fetch_assoc($friendResult)['email'];
			
			$pastDay = subtractDate($mysqldate, '1');
			$pastResponseOne = getQuestionResponse($mysqli, $friendUsername, $pastDay, '1');
			$pastResponseTwo = getQuestionResponse($mysqli, $friendUsername, $pastDay, '2');
			$pastResponseThree = getQuestionResponse($mysqli, $friendUsername, $pastDay, '3');
			$pastResponseFour = getQuestionResponse($mysqli, $friendUsername, $pastDay, '4');
			
			
			$friendGoals = getCurrentGoals($mysqli, $friendUsername);
			$friendPickupGoal = mysqli_fetch_assoc($friendGoals)['pickupGoal'];
			$friendGoals = getCurrentGoals($mysqli, $friendUsername);
			$friendMinuteGoal = mysqli_fetch_assoc($friendGoals)['minuteGoal'];
			
			$friendData=mysqli_query($mysqli,"SELECT * FROM phone_data WHERE email = '$friendEmail' ORDER BY reg_date DESC LIMIT 5");
			while ($row = mysqli_fetch_array($friendData)) {
				$friendMinuteRows[]=(int)($row['minuteCount']);
				$friendPickupRows[]=(int)($row['pickupCount']);
			}
			
			$friendMinuteQuery = "SELECT AVG(minuteCount) FROM phone_data WHERE email = '$friendEmail'";
			$friendMinuteAverage = getAverage($friendMinuteQuery, $mysqli, "minuteCount");

			$friendPickupQuery = "SELECT AVG(pickupCount) FROM phone_data WHERE email = '$friendEmail'";
			$friendPickupAverage = getAverage($friendPickupQuery, $mysqli, "pickupCount");
			
		}
	
	}

?>
		
	<div class="messageWrapper">
			<div id="leftMessages" style="margin-top: 10px;">
				<b> Messages from your friends! </b>
				<div id="messagesDiv" style="margin: 10px"> 
					<?php
						if (count($messages) > 0) {
							foreach ($messages as $message) {
								$currentMessage = $message[3] . ": " . $message[2] . ": " . $message[4];
								echo $currentMessage . '<br>';
							}
						} else {
							echo 'No messages yet. Send a friend a message to let them know you are here!';
						}
						
					?>
				</div><!-- #messageDiv -->
			</div>
			<div id="rightMessages">
				<div id="usernameForm">
					<form method="POST">
					<form>
						<b>Check up on your friends!</b> <br>
						<input type="hidden" name="set_date" id="date_id" value="<?php echo $date; ?>">
						Friend: <textarea name="friendUsername" rows="1" cols="20"></textarea><br>
						<button type="submit" value="statSubmit" name="statSubmit">Submit</button>
					</form>
				</div>
				
				<div id="messageForm">
					<form method="POST" action="sendMessage.php">
							<b>Send a friend a message!</b> <br>
							<input type="hidden" name="senderUsername" id="senderUsername" value="<?php echo $username; ?>">
							<input type="hidden" name="send_date" id="date_id" value="<?php echo $mysqldate; ?>">
							Friend: <textarea name="receiverUsername" rows="1" cols="20"></textarea><br>
							Message: <textarea name="message" rows="3" cols="20"></textarea><br>
							<button type="submit" name="messageSubmit">Submit</button>
					</form>
				</div>
	

				<div id="friendStats" style="display: none;"> 
					<table class="pure-table pure-table-bordered">
					<thead>
							<tr>
									<th><?php echo $friendUsername;?></th>
									<th>Pickups</th>
									<th>Minutes</th>
									<th>Minutes/Pickup</th>
							</tr>
					</thead>

					<tbody>
							<tr>
									<td><b>Averages</b></td>
									<td><?php echo (int)$friendPickupAverage;?></td>
									<td><?php echo (int)$friendMinuteAverage;?></td>
									<td><?php 
										if ((int)$friendPickupAverage == 0) {
											echo 0;
										}
										else echo round(((int)$friendMinuteAverage / (int)$friendPickupAverage), 2);
										?></td>
							</tr>

							<tr>
									<td><b>Goal</b></td>
									<td><?php echo $friendPickupGoal; ?></td>
									<td><?php echo $friendMinuteGoal; ?></td>
									<td><?php 
										if ((int)$friendPickupGoal == 0 || $friendPickupGoal == NULL) {
											echo '';
										}
										else echo round(((int)$friendMinuteGoal / (int)$friendPickupGoal), 2); 
										?></td>
							</tr>
						
							<tr>
									<td><b>Last 5 Days</b></td>
									<td><?php echo json_encode($friendPickupRows); ?></td>
									<td><?php echo json_encode($friendMinuteRows); ?></td>
									<td><?php 
										if ((int)$friendPickupGoal == 0 || $friendPickupGoal == NULL) {
											echo '';
										}
										else echo round(((int)$friendMinuteGoal / (int)$friendPickupGoal), 2); 
										?></td>
							</tr>
					</tbody>
					</table>
				</div><!-- #friendStats -->
			</div><!-- #rightMessages -->
		</div><!-- .messageWrapper -->

</div><!-- .wrapper -->

		

</body>
</html>

 <?php
$status = false;


	if (isset($_POST) && count($_POST) > 0 && strlen($_POST["answer"]) > 0) {
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
    $status = mysqli_query($mysqli, $answer_sql);
    if ($status == false) {
            echo '<script>console.log("false")</script>';
        } else {
            echo '<script>console.log("true")</script>';
        }
		
	}

	if (strlen($_POST['statSubmit']) > 0) {
		$friendUsername = $_POST['friendUsername'];

		$userQuery = "SELECT email from members WHERE username = '$friendUsername'";
		$friendResult = mysqli_query($mysqli, $userQuery);
		if (!$friendResult || mysqli_num_rows($friendResult) == 0) {
			echo '<script>console.log("No member with given username")</script>';
		}
		else {
			$friendEmail = mysqli_fetch_assoc($friendResult)['email'];
			
			$pastDay = subtractDate($mysqldate, '1');
			$pastResponseOne = getQuestionResponse($mysqli, $friendUsername, $pastDay, '1');
			$pastResponseTwo = getQuestionResponse($mysqli, $friendUsername, $pastDay, '2');
			$pastResponseThree = getQuestionResponse($mysqli, $friendUsername, $pastDay, '3');
			$pastResponseFour = getQuestionResponse($mysqli, $friendUsername, $pastDay, '4');
			
			$friendGoals = getCurrentGoals($mysqli, $friendUsername);
			$friendPickupGoal = mysqli_fetch_assoc($goals)['pickupGoal'];
			$friendMinuteGoal = mysqli_fetch_assoc($goals)['minuteGoal'];
			
			$friendData=mysqli_query($mysqli,"SELECT * FROM phone_data WHERE email = '$friendEmail' ORDER BY reg_date DESC LIMIT 5");
			while ($row = mysqli_fetch_array($friendData)) {
				$friendMinuteRows[]=(int)($row['minuteCount']);
				$friendPickupRows[]=(int)($row['pickupCount']);
			}
			
			$friendMinuteQuery = "SELECT AVG(minuteCount) FROM phone_data WHERE email = '$friendEmail'";
			$friendMinuteAverage = getAverage($friendMinuteQuery, $mysqli, "minuteCount");

			$friendPickupQuery = "SELECT AVG(pickupCount) FROM phone_data WHERE email = '$friendEmail'";
			$friendPickupAverage = getAverage($friendPickupQuery, $mysqli, "pickupCount");
			
			echo "<script> showStats(); </script>";			
			
		}
	}

?>

<?php
/* Close the connection */
$mysqli->close(); 
?>




