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
$transactionID = $currenttransID['latestTransaction'] + 1;

$gettrans = "SELECT MAX(batchID) AS latestBatch FROM transactions WHERE status <= 2";
$batchresult = mysqli_query($link, $gettrans); //runs the qry
$currentbatchID = mysqli_fetch_array($batchresult);
$batchID = $currentbatchID['latestBatch'] + 1;

echo $transactionID;
echo $batchID;

//Query to add a line from the form
if(isset($_POST['Create'])) {
    //will actually run the sql
	$line = $_POST['lineID'];					
	$account = $_POST['accountID'];		
	$submitter = $_SESSION['id'];
	$debit = $_POST['debit'];	
	$credit = $_POST['credit'];	
	$status = $_POST['status'];
	$transactionID = $_POST['transactionID'];

	$sqlupd = "INSERT INTO `transactions` (`lineID`, `transactionID`, `AccountID`, `SubmitterID`, `debit`, `credit`, `status`) 
    VALUES ('$lineID', '$transactionID', '$account', '$submitter', $debit, '$credit', '0')";

    $edit = mysqli_query($link, $sqlupd); //runs the qry
    if($edit) //entered if acct created successfully
    {
		header("location:addtransaction.php"); //Reload page
        exit;
    }
    else
    {
        echo "Could Not Add Account, SQL Returned Error";
    }
}

//Add logic to post batch
 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Add Transaction</title>
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
			</div>
		</nav>
		<div class="content">
			<h2>Add Transaction </h2>
			<div class="tooltip">Hover For Help
  				<span class="tooltiptext">This page allows users to make transactions to be insterted into a batch for approval.</span>
			</div>
			<div>

			<table id="addtransactionTable">
				<thead>
				<tr>
					<th>ID</th>
					<th>Account Code</th>
					<th>Account Name</th>
					<th>Debit</th>
					<th>Credit</th>
				</tr>
				</thead>
			</table>

			<script type="text/javascript">
				$(document).ready(function() {
					$('#addtransactionTable').dataTable({
						"processing": true,
						"ajax": "addtransactionFetchData.php",
						"language": {
							"emptyTable": "No data was found in the database.",	//Used if no SQL data was found 
							"zeroRecords": "No data available in table."	//Used to display msg after filtering
						},
						"rowGroup": {
							"dataSrc": 0,								//0 is the index value for transactionID
						},
						"columns": [
							{ data: 'transactionID', sWidth: '10%'},
							{ data: 'accountID', sWidth: '10%' },
							{ data: 'faccount' },
							{ data: 'debit' },
							{ data: 'credit' },
						]
					});  
				});
			</script>

	
            </div>
			<div>
				<h3> Transaction Information </h3>
				<form action="" method="post">
                    
				<input type="text" name="TransactionID" placeholder="transactionID" ><br>
				<input type="text" name="batchID" placeholder="batchID" ><br>
				<input type="text" name="accountID" placeholder="accountID" ><br>
				<input type="text" name="credit" placeholder="credit" ><br>
				<input type="text" name="debit" placeholder="debit" ><br>

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