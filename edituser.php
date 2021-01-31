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
//Connect to the DB
$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
//Primes the query to pull current data
$sql = "SELECT * FROM accounts WHERE id='$editu'";

//Gets current user data
$qry = mysqli_query($link, $sql);
$data = mysqli_fetch_array($qry);

//Fires update query when form is submitted
if(isset($_POST['update'])) {
	$newEmail = $_POST['email'];
	$sqlupd = "UPDATE accounts SET Email = '$newEmail' WHERE id='$editu'";
	$edit = mysqli_query($link, $sqlupd);
	if($edit)
    {
        header("location:edituser.php?r=$return&u=$editu"); // reload page
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
			   echo "<h3> Editing User " . $data['username'] . "</h3>"
			   ?>
			</div>	
			<div>
				<form action="" method="post">
					<input type="text" name="email" placeholder="Email" value="<?php echo $data['Email'];?> ">

					<input type="submit" value="Update" name="update" >
				</form>
			</div>
		</div>
	</body>
</html>