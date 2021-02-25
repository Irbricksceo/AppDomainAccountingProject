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
if ($stmt = $con->prepare('SELECT faccount, fdescription, normalside, fcategory, fsubcategory, debit, credit, fdatecreated, userID, comment, active FROM faccount WHERE faccountID = ?')){
	// In this case we can use the account ID to get the account info.
	$stmt->bind_param('i', $_GET['u']);
	$stmt->execute();
	$stmt->bind_result($faccount, $fdescription, $normalside, $fcategory, $fsubcategory, $debit, $credit, $fdatecreated, $userID, $comment, $active);
	$stmt->fetch();
	$stmt->close();
}

if ($_SESSION['userrole'] == '1'):
	$role = "Administrator";
elseif ($_SESSION['userrole'] == '2'):
	$role = "Manager";
elseif ($_SESSION['userrole'] == '3'):
	$role = "User";
else:
	$role = "Undefined";
endif;

if ($active == 1)
	$active = "Yes";
else
	$active = "No";

if ($normalside == 0)
	$normalside = "Debit";
else
	$normalside	 = "Credit";

switch ($fcategory){
    case 1:
	    $fcategory = "Asset";
        break;
    case 2: 
        $fcategory = "Liability";
        break;
    case 3:
        $fcategory = "Equity";
        break;
    case 4:
        $fcategory = "Revenue";
        break;
    case 5:
        $fcategory = "Expense";
        break;
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
			<h2>Account Information</h2>
			<div>
				<p>Your account details are below:</p>
				<table>
					<tr>
						<td>Account name:</td>
						<td><?=$_GET['u']?></td>
					</tr>
					<tr>
						<td>description:</td>
						<td><?=$fdescription?></td>
					</tr>
					<tr>
						<td>Normal Side:</td>
						<td><?=$normalside?></td>
					</tr>
					<tr>
						<td>Category:</td>
						<td><?=$fcategory?></td>
					</tr>
					<tr>
						<td>subcategory:</td>
						<td><?=$fsubcategory?></td>
					</tr>
					<tr>
						<td>debit:</td>
						<td><?=$debit?></td>
					</tr>
					<tr>
						<td>credit:</td>
						<td><?=$credit?></td>
					</tr>
					<tr>
						<td>Date Created:</td>
						<td><?=$fdatecreated?></td>
					</tr>
					<tr>
						<td>created by:</td>
						<td><?=$userID?></td>
					</tr>
					<tr>
						<td>comment:</td>
						<td><?=$comment?></td>
					</tr>
					<tr>
						<td>Active:</td>
						<td><?=$active?></td>
					</tr>
				</table>
			</div>
		</div>
	</body>
</html>