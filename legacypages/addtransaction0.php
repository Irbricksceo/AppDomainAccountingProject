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
//Connect to the DB
$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

//Fires update query when form is submitted
if(isset($_POST['Create'])) {
	//this part assigns the variables from the form
	$newtransaction = $_POST['transaction'];
	$newaccount = $_POST['account'];
	$newsubmitterID = $_POST['submitterID'];
	$newdebit = $_POST['debit'];
	$newcredit = $_POST['credit'];
    $newstatus = $_POST['status'];



	//creates the qry to add a user
	$sqlupd = "INSERT INTO `transactions` ( `transaction`, `account`, `submitterID`, `debit`, `credit`, `status`) 
    VALUES ( '$newtransaction', '$newaccount', '$newsubmitterID', '$newdebit', $newcredit, '$newstatus')";


$edit = mysqli_query($link, $sqlupd); //runs the qry
	if($edit) //entered if acct created successfully
	{
		header("location:home.php"); // return to users page
		exit;
	}
	else
	{
	echo "Could Not Add Account, SQL Returned Error";
	}

}
 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Add User</title>
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
			<a href="accounts.php"><i class="fas fa-user-circle"></i>Accounts</a> <!-- ?? -->
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
			<div class="tooltip">Hover For Help
  				<span class="tooltiptext">Make Sure All Fields Are Filled And Press Submit To Add Users.</span>
			</div>
			<div>
				<h3> Entry Information </h3>
				<form action="" method="post">
					<input type="text" name="transaction" placeholder="transaction" ><br>
					<input type="text" name="account" placeholder="account" ><br>
					<input type="text" name="submitterID" placeholder="submitterID" ><br>
					<input type="text" name="debit" placeholder="debit" ><br>
                    <input type="text" name="credit" placeholder="credit" ><br>
                    <input type="text" name="status" placeholder="status"><br>
					<input type="submit" value="Create" name="Create" >
				</form>
			</div>
		</div>
	</body>
</html>