<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

if ($_SESSION['userrole'] == 1) {
    header("location:home.php"); // Kick Admins back to home
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

// Pull transaction number max

$gettrans = "SELECT MAX(transactionID) AS latestTransaction FROM transactions WHERE status <= 2";
$transresult = mysqli_query($link, $gettrans); //runs the qry
$currenttransID = mysqli_fetch_array($transresult);
$transactionID = $currenttransID['latestTransaction'];

$gettrans = "SELECT MAX(batchID) AS latestBatch FROM transactions WHERE status <= 2";
$batchresult = mysqli_query($link, $gettrans); //runs the qry
$currentbatchID = mysqli_fetch_array($batchresult);
$batchID = $currentbatchID['latestBatch'] + 1;


if(isset($_POST['SubmitBatch'])) {

    if (!isset($_SESSION['transcount'])) {
            $_SESSION['transcount'] = 1;
        }
        $transactionID = $transactionID + $_SESSION['transcount'];
    
        $account = $_POST['Account'];		
        $submitter = $_SESSION['id'];
        $description = "Adjusting Entry: " . $_POST['Desc'];
        $debit = $_POST['debit'];	
        $credit = $_POST['credit'];	
		
        $sqlupd = "INSERT INTO `transactions` (`transactionID`, `batchID`, `AccountID`, `SubmitterID`, `debit`, `credit`, `status`, `description`) 
        VALUES ('$transactionID', '$batchID', '$account', '$submitter', '$debit', '$credit', '0', '$description')";
    
        $edit = mysqli_query($link, $sqlupd); //runs the qry
        if($edit) //entered if acct created successfully
        {
            header("location:addadjusting.php"); //Reload page
            exit;
        }
        else
        {
            echo $batchID . " Could Not Add Line, SQL Returned Error";
        }
}

if(isset($_POST['NextTransaction'])) {
	$_SESSION['transcount'] = $_SESSION['transcount'] + 1;
}

//Add logic to post batch
 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Entry Details</title>
		<link href="css/style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <link rel="icon" href="images/favicon.ico">
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>

		<!-- DataTables scripts and styling -->
        <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
		<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.js" charset="utf8" type="text/javascript"></script>
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css">
		<script src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js" type="text/javascript"></script>
		
		<style type="text/css">
        .wrapper{
            width: 650px;
            margin: 0 auto;
        }
        .page-header h2{
            margin-top: 0;
        }
        table tr td:last-child a{
            margin-right: 15px;
        }
    	</style>
	</head>

	<body class="loggedin">
    <nav class="navtop">
			<div>
				<img src="images/logo.png" width="60" alt="Logo">
				<h1>Accounting Pro</h1>
                <a href="accounts.php"></i>Back</a>
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
					<a href="addadjusting.php"><i class="fas fa-user-circle"></i>Create Adjusting Entry</a>
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
			<hr>
			<h2>Reporting</h2>
			<?php 
				if ($_SESSION['userrole'] == '2') { 
					?><a href="generatereports.php"><i class="fas fa-user-circle"></i>Generate Reports</a>
					<?php 
				}
				?>
			</div>
		</nav>
		<div class="content">
			<h2>Add Adjusting Entry </h2>

			<h3> Transaction Information </h3>
				<form action="" method="post">
                <select name="Account" id="accountID">
                        <?php
						$sqlSelect="SELECT faccountID, faccount FROM faccount WHERE active = 1";
						$accountlist = mysqli_query($link, $sqlSelect);
                        while ($row = mysqli_fetch_array($accountlist)) {
                            echo "<option value='" . $row['faccountID'] . "'>" . $row['faccount'] . ",  ID: " . $row['faccountID'] . "</option>";
                        }
                        ?>
                    </select>     </br>
				<label> Credit:</label><input type="number" name="credit" value = 0><br>
				<label> Debit:</label><input type="number" name="debit" value = 0><br>	
                <input type = "textarea" name = "Desc" placeholder="Description"><br>			
				<input type="submit" value="Submit" name="SubmitBatch" >
				</form>
			</div>
		</div>
	</body>
</html>