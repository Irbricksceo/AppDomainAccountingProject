<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
//If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
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
				if ($_SESSION['userrole'] == '1') {
					?><h2>User Management</h2>	
					<a href="users2.php"><i class="fas fa-user-circle"></i>Users</a>
					<a href="adduser.php"><i class="fas fa-user-circle"></i>Add A User</a>
					<hr><?php 
				} else {
					?><h2>Transactions</h2>	
					<a href="addtransaction.php"><i class="fas fa-user-circle"></i>Create Batch</a>
					<a href="approvebatch.php"><i class="fas fa-user-circle"></i>Review Batch</a>
					<a href="entries.php"><i class="fas fa-user-circle"></i>Journal</a>	
					<hr><?php
				}	
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
			<h2>Home Page</h2> 
			<div class="tooltip">Hover For Help
  				<span class="tooltiptext">Use the navigation bars to access features.</span>
			</div>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>
			
			<div>
			<p>Welcome to Accounting Pro, your online tool for all your accounting needs.</p>
			<?php 
			if ($_SESSION['userrole'] == '1'):
				?><h3> As an administrator, you currently have access to tools for managing users on the platform. Try it out in the users tab!</h3><?php 
			elseif ($_SESSION['userrole'] == '2'):
				$sqlSelect="SELECT DISTINCT BatchID FROM transactions WHERE status = 0 ";
                $result = mysqli_query($link, $sqlSelect);
                echo "<h3> Notice: There are " . mysqli_num_rows($result) . " batches awaiting approval </h3>"; 
			elseif ($_SESSION['userrole'] == '3'):
				$sqlSelect="SELECT DISTINCT BatchID FROM transactions WHERE status = 0 ";
                $result = mysqli_query($link, $sqlSelect);
                echo "<h3> Notice: There are " . mysqli_num_rows($result) . " batches awaiting approval </h3>"; 
			else:
				?><h3> Unable to retrieve role, please logout and login again. If this problem persists, please contact your administrator.</h3><?php 
			endif;
			?>
			</div>
		</div>
	</body>
</html>