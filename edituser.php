<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}


//Set a page variable based on if page was entered via profile or users page and parses for a person to be editing. Forces to default for non admins
if(isset($_GET['u'])) {
	$editu = $_GET['u'];
} else {
	$editu = $_SESSION['id'];
}

if(isset($_GET['r'])) {
	$return = $_GET['r'];
} else {
	$return = 2;
}



$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="css/style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	
	<body class="loggedin">
		<nav class="navtop">
			<div>
            <h1>Accounting Pro</h1>
				<?php
					if ($return == 1):
						?><a href="Users2.php"></i>Back</a><?php 
					elseif ($return == 2):
						?><a href="profile.php"></i>Back</a><?php 
					endif;
				?>

			</div>
		</nav>
		<div class="content">
			<h2>Editing User</h2>
			<div>
			   <?php
			   echo "<h3> Editing User " . $editu . "</h3>"
			   ?>
			</div>
		</div>
	</body>
</html>