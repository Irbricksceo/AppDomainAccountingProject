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
/*
$rowSQL = mysql_query( "SELECT MAX(transactionID) AS max FROM `transactions`;" );
$row = mysql_fetch_array( $rowSQL );
$largestNumber = $row['max'] + '1';

$rowbSQL = mysql_query( "SELECT MAX(batchID) AS max FROM `transactions`;" );
$rowb = mysql_fetch_array( $rowbSQL );
$largestNumberb = $rowb['max'] + '1';
*/



//Fires update query when form is submitted
if(isset($_POST['Create'])) {
    //will actually run the sql
	$line = $_POST['lineID'];					
	$account = $_POST['accountID'];		
	$submitter = $_SESSION['id'];
	$debit = $_POST['debit'];	
	$credit = $_POST['credit'];	
	$status = $_POST['status'];
	$transactionID = $_POST['transactionID'];

	//This should  set $result to 0 if there is an entry in transactions where the status is 3 then set $maxtrans to either the highest transaction num or 1 higher
	/*			
	$result = mysql_query("SELECT id FROM transactions WHERE status = '3'");
	if(mysql_num_rows($result) == 0) 
		{
			$maxtrans = 'SELECT MAX( transactionID ) FROM transactions';
		}
	else 
		{
			$maxtrans = 'SELECT MAX( transactionID ) FROM transactions';
			$maxtrans += 1;
		}
	*/

	

	


	$sqlupd = "INSERT INTO `transactions` (`lineID`, `transactionID`, `BatchID`, `AccountID`, `SubmitterID`, `debit`, `credit`, `status`) 
    VALUES ('$lineID', '$maxtrans', '$Batch', '$account', '$submitter', $debit, '$credit', '3')";

    $edit = mysqli_query($link, $sqlupd); //runs the qry
    if($edit) //entered if acct created successfully
    {
        //*header("location:accounts.php"); // return to users page
		header("location:home.php"); // return to home page
        exit;
    }
    else
    {
        echo "Could Not Add Account, SQL Returned Error";
    }
	//generate batch ID && transaction ID **********************************************************
}
 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Create New Account</title>
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
			<h2>Create New Transaction</h2>	
			<div class="tooltip">Hover For Help
  				<span class="tooltiptext">Make Sure All Fields Are Filled And Press Submit To Add Accounts.</span>
			</div>
			<div>
				<h3> Transaction Information </h3>
				<form action="" method="post">
                    

				<input type="text" name="AccountID" placeholder="AccountID" ><br>
				<input type="number" name="credit" placeholder="credit" ><br>
				<input type="number" name="debit" placeholder="debit" ><br>

					<!--
					<input type="text" name="Name" placeholder="Account Name" ><br>
                    <label for="Category">Category:</label>
                    <select name="Category" id="Category">
                        <option value="1">Assets</option>
                        <option value="2">Liabilities</option>
                        <option value="3">Equity</option>
                        <option value="4">Revenues</option>
                        <option value="5">Expenses</option>
                    </select> <br>
                    <input type="text" name="Subcategory" placeholder="Subcategory" ><br>
                    <input type="text" name="Comment" placeholder="Comment" ><br>
                    <input type="text" name="Description" placeholder="Description" ><br>
                    <input type="number" name="StartingBalance" placeholder="Starting Balance" ><br>
					-->
					
					
					<input type="submit" value="Create" name="Create" >
				</form>
			</div>
		</div>
	</body>
</html>