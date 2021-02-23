<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

if ($_SESSION['userrole'] != 1) {
    header("location:home.php"); // Kick Non Admins backl to home
    exit;
}

include 'scripts/accountscripts.php'; 

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';
//Connect to the DB
$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

//Fires update query when form is submitted
if(isset($_POST['Create'])) {
	//this part assigns the variables from the form, and utilizes the provided functions to create Uname and Password
    $generatedUser = generateUsernameByName($_POST['firstname'], $_POST['lastname']);
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

	
    $date = date("Y/m/d"); //grabs current date

	//creates the qry to add a user
	$sqlupd = "INSERT INTO `accounts` (`username`, `password`, `Fname`, `Lname`, `StreetAddress`, `City`, `State`, `Zip`, `DOB`, `Email`, `SecurityQ1`, `SecurityA1`, `SecurityQ2`, `SecurityA2`) 
    VALUES ('$generatedUser', '$hashed', '$newFname', '$newLname', '$newStreet', '$newCity', '$newState', $newZip, '$newDOB', '$newEmail', '$q1', '$a1', '$q2', '$a2')";


    $edit = mysqli_query($link, $sqlupd); //actually creates the user
    if($edit) //entered if user created successfully to perform followup tasks 
    {
		//uses the prepared statements method to pull down the ID of the newly created user via the username
        if ($stmt = $link->prepare('SELECT id FROM accounts WHERE username = ?')) {
            $stmt->bind_param('s', $generatedUser);
            $stmt->execute();
            $stmt->store_result();
        
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($qry);
                $stmt->fetch();
			}}
			
		//call the functions from accountscripts to perform the final password actions using the pulled ID	
        setPasswordExpire($qry);
        storePassword($qry);
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
			<a href="scripts/logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			<h4> Logged In As: <?=$_SESSION['name']?> </h4>
			</div>
		</nav>
		<nav class="navside">
			<div>
			<hr>
			<h2>Navigation</h2>
			<a href="home.php"><i class="fas fa-user-circle"></i>Home</a>
			<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
			<hr>
			<?php
				if ($_SESSION['userrole'] == '1'):
					?><h2>User Management</h2>	
					<a href="users2.php"><i class="fas fa-user-circle"></i>Users</a>
					<a href="adduser.php"><i class="fas fa-user-circle"></i>Add A User</a>
					<hr><?php 
					endif;
					
				?>
			<h2>Account Management</h2>
			<a href="accounts.php"><i class="fas fa-user-circle"></i>Accounts</a>
			<?php
				if ($_SESSION['userrole'] == '1'):
					?><a href="addaccount.php"><i class="fas fa-user-circle"></i>Add An Account</a>
					<?php 
					endif;	
				?>
			<a href="eventlog.php"><i class="fas fa-user-circle"></i>Event Log</a>
			</div>
		</nav>
		<div class="content">
			<h2>Creating New User</h2>	
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
                    <input type="text" name="q1" placeholder="Security Question 1"><br>
                    <input type="text" name="a1" placeholder="Security Answer 1"><br>
                    <input type="text" name="q2" placeholder="Security Question 2"><br>
                    <input type="text" name="a2" placeholder="Security Answer 2"><br>
					<input type="date" name="dob"><br>
					<input type="submit" value="Create" name="Create" >
				</form>
			</div>
		</div>
	</body>
</html>