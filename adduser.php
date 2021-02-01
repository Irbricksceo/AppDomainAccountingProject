<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

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

if ($_SESSION['userrole'] != 1) {
    header("location:home.php"); // Kick Non Admins backl to home
    exit;
}



//Fires update query when form is submitted
if(isset($_POST['Create'])) {
    $generatedUser = "temp";
    $hashed = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$newEmail = $_POST['email'];
	$newFname = $_POST['firstname'];
	$newLname = $_POST['lastname'];
	$newStreet = $_POST['street'];
	$newCity = $_POST['city'];
	$newState = $_POST['state'];
	$newZip = $_POST['zip'];
    $newDOB = $_POST['dob'];
    $q1 = $_POST['q1'];
	$a1 = $_POST['a1'];
	$q2 = $_POST['q2'];
    $a2 = $_POST['a2'];   


    $date = date("Y/m/d");

	$sqlupd = "INSERT INTO `accounts` (`username`, `password`, `Fname`, `Lname`, `StreetAddress`, `City`, `State`, `Zip`, `DOB`, `Email`) 
    VALUES ('$generatedUser', '$hashed', '$newFname', '$newLname', '$newStreet', '$newCity', '$newState', $newZip, '$newDOB', '$newEmail')";
    /*
	$sqlupd = "INSERT INTO `accounts` (`username`, `password`, `Fname`, `Lname`, `StreetAddress`, `City`, `State`, `Zip`, `DOB`, `SecurityQ1`, `SecurityA1`, `SecurityQ2`, `SecurityA2`, `userrole`, `Active`, `Email`) 
    VALUES ('$generatedUser', '$hashed', '$newFname', '$newLname', '$newStreet', '$newCity', '$newState', $newZip, '$newDOB',  $q1, '$a1', $q2, '$a2', 3,  0, '$newEmail')";
    */
    $edit = mysqli_query($link, $sqlupd);
    if($edit)
    {
        header("location:users2.php"); // return to users page
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
						?><a href="Users2.php"></i>Back</a><?php 
				?>

			</div>
		</nav>
		<div class="content">
			<h2>Editing User</h2>
			<div>
			   <?php
			   echo "<h3> Creating New User</h3>"
			   ?>
			</div>	
			<div>
				<h3> Personal Information </h3>
				<form action="" method="post">
                    <input type="text" name="email" placeholder="Email" ><br>
                    <input type="text" name="password" placeholder="Password" ><br>
					<input type="text" name="firstname" placeholder="First Name" ><br>
					<input type="text" name="lastname" placeholder="Last Name" ><br>
					<input type="text" name="street" placeholder="Street Address" ><br>
					<input type="text" name="city" placeholder="City" ><br>
					<input type="text" name="state" placeholder="State" ><br>
                    <input type="text" name="zip" placeholder="Zip" ><br>
                    <input type="textarea" name="q1" placeholder="Security Question 1"><br>
                    <input type="textarea" name="a1" placeholder="Security Answer 1"><br>
                    <input type="textarea" name="q2" placeholder="Security Question 2"><br>
                    <input type="textarea" name="a2" placeholder="Security Answer 2"><br>
					<input type="date" name="dob"><br>
					<input type="submit" value="Create" name="Create" >
				</form>
			</div>
		</div>
	</body>
</html>