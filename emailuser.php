<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

//Set a page variable based on if page was entered via profile or users page and parses for a person to be editing. Forces to default for non admins
if(isset($_GET['u'])&& $_SESSION['userrole'] == 1) {
	$editu = $_GET['u'];
} else {
	$editu = $_SESSION['id'];
}

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';
//Connect to the DB
$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if ($stmt = $link->prepare('SELECT email FROM accounts WHERE id = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
    $stmt->bind_param('i', $editu);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($qry);
        $stmt->fetch();
}}

//Fires update query when form is submitted

if(isset($_POST['sendemail'])) {
	$to_email = $qry;
	$body = $_POST['body'];
	$subject = $_POST['subject'];
	$headers = "From: server.acctpro@gmail.com";

    if (mail($to_email, $subject, $body, $headers)) {
        echo "Email successfully sent";
        header("location:emailuser.php?u=$editu");
    } else {
        echo "Email sending failed...";
        header("location:emailuser.php?u=$editu");
    }
} 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="css/style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link rel="icon" href="images/favicon.ico">
	</head>
	
	<body class="loggedin">
		<nav class="navtop">
			<div>
            <img src="images/logo.png" width="60" alt="Logo">
            <h1>Accounting Pro</h1>
				<?php
					?><a href="users2.php"></i>Back</a><?php 
				?>
			<h4> Logged In As: <?=$_SESSION['name']?> </h4>
			</div>
		</nav>
		<div class="content">
			<h2>Compose Email</h2>
			<div>
			   <?php
			   echo "<h3> Emailing " . $qry . "</h3>"
			   ?>
			</div>	
			<div>
				<form action="" method="post">
                    <input type="text" name="subject" placeholder="Subject"><br>
                    <input type="textarea" name="body" placeholder="Body"><br>
					<input type="submit" value="Send" name="sendemail" >
				</form>
				<?php
				?>
			</div>
		</div>
	</body>
</html>