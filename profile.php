<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// We don't have the password or email info stored in sessions so instead we can get the results from the database.
$stmt = $con->prepare('SELECT password, email, DOB, Fname, Lname, StreetAddress, City, State, Zip, PasswordExpire, DateCreated FROM accounts WHERE id = ?');
// In this case we can use the account ID to get the account info.
$stmt->bind_param('i', $_SESSION['id']);
$stmt->execute();
$stmt->bind_result($password, $email, $DOB, $Fname, $Lname, $Street, $City, $State, $Zip, $Passexp, $Join);
$stmt->fetch();
$stmt->close();

if ($_SESSION['userrole'] == '1'):
	$role = "Administrator";
elseif ($_SESSION['userrole'] == '2'):
	$role = "Manager";
elseif ($_SESSION['userrole'] == '3'):
	$role = "User";
else:
	$role = "Undefined";
endif;
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
			</div>
		</nav>
		<div class="content">
			<h2>Profile Page</h2>
			<div>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Username:</td>
						<td><?=$_SESSION['name']?></td>
					</tr>
					<tr>
						<td>Password:</td>
						<td><?=$password?></td>
					</tr>
					<tr>
						<td>Email:</td>
						<td><?=$email?></td>
					</tr>
					<tr>
						<td>Role:</td>
						<td><?=$role?></td>
					</tr>
					<tr>
						<td>DOB:</td>
						<td><?=$DOB?></td>
					</tr>
					<tr>
						<td>Member Since:</td>
						<td><?=$Join?></td>
					</tr>
					<tr>
						<td>Name:</td>
						<td><?=$Fname?> <?=$Lname?></td>
					</tr>
					<tr>
						<td>Address:</td>
						<td><?=$Street?></td>
					</tr>
					<tr>
						<td>City:</td>
						<td><?=$City?></td>
					</tr>
					<tr>
						<td>State:</td>
						<td><?=$State?></td>
					</tr>
					<tr>
						<td>Zip:</td>
						<td><?=$Zip?></td>
					</tr>
				</table>
				<a href="edituser.php"></i>Edit</a>
			</div>
		</div>
	</body>
</html>