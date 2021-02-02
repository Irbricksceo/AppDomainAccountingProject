<?php

include 'accountscripts.php';

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';
//Connect to the DB
$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$email = $_POST['email'];


$sql = "SELECT SecurityQ1, SecurityQ2, SecurityA1, SecurityA2, id  FROM accounts WHERE Email='$email'";
//Gets current user data
$qry = mysqli_query($link, $sql);
$data = mysqli_fetch_array($qry);

//Fires update query when form is submitted
if(isset($_POST['Change'])) {
	$newpass = $_POST['newPass'];
	$hashed = password_hash($_POST['newPass'], PASSWORD_DEFAULT);
	$a1 = $_POST['answer1'];
	$a2 = $_POST['answer2'];


	//Check if questions match answers
	if($data['SecurityA1'] != $a1 || $data['SecurityA2'] != $a2) {
		echo 'incorrect answers for security questions.';
		exit;
    }
	//check if pass was already used
	$id = $data['id'];

	$sql2 = "SELECT * FROM pastpassword WHERE ID = '$id'";
		if($result = mysqli_query($link, $sql2)){
			if(mysqli_num_rows($result) > 0){
					while($row = mysqli_fetch_array($result)){
						if (password_verify($newpass, $row['Password'])) {
							echo 'You have already used that password, please choose another.';
							exit;
					}}
				// Free result set
				mysqli_free_result($result);
	}}
	//update password
	$sqlupd = "UPDATE accounts SET password = '$hashed' WHERE Email='$email'";
	$edit = mysqli_query($link, $sqlupd);
	if($edit) {
		setPasswordExpire($data['id']);
        storePassword($data['id']);
        header("index.html"); // return to home page
        exit;
    }
    else
    {
        echo mysqli_error();
	} 
} 



?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Forgot Password</title>
		<link href="css/style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
		<div class = "alter">
   		<form action="" method="post">
			<h1> Forgot Password? Don't Worry <?php echo $email; ?>, Reset It Here! </h1>
			<input type="hidden" name = email value="<?php echo $email; ?>">

    		<label for="newPass">  New Password: </label> <br>
    		<input type= "text" name="newPass" userEmail="email"> <br>
	
			<label for= "answer1"> <?php echo $data['SecurityQ1']; ?></label> <br>
			<input type= "text" name="answer1">	<br>

			<label for= "answer2"> <?php echo $data['SecurityQ2']; ?></label> <br>
			<input type= "text" name="answer2">	<br>   
			<input type="submit" value="Change" name="Change" >
		 </form>
		</div>
	</body>
</html>