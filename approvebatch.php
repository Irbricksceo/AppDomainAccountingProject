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

include "scripts/batchscripts.php";

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if(isset($_POST['Review'])) {
    $batch = $_POST['Batch'];
	header("location:approvebatch.php?b=$batch"); // reload page	
} 

if(isset($_POST['UpdateStatus'])) {
    $batch = $_GET['b'];
    $newstatus = $_POST['Action'];
	processBatch($link, $batch, $newstatus);
    header("location:approvebatch.php");	
} 

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Batch Review</title>
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
			<h2>Batch Review</h2>
			<div>
            <?php
            if(!isset($_GET['b'])) {
                $sqlSelect="SELECT DISTINCT BatchID FROM transactions WHERE status = 0 ";
                $result = mysqli_query($link, $sqlSelect);

                echo "<p> There are " . mysqli_num_rows($result) . " batches awaitng approval </p>"; 
                ?>
                <hr>
				<form action="" method="post">
                    <select name="Batch" id="Batch">
                        <?php
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<option value='" . $row['BatchID'] . "'>" . $row['BatchID'] . "</option>";
                        }
                        ?>
                    </select> 
					<input type="submit" value="Review Selected" name="Review" >
                </form>
            <?php
            } else {
            $batch = $_GET['b'];
            echo "<p> Reviewing Batch " . $batch . ".</p>";
            $sqlSelect="SELECT * FROM transactions WHERE batchID = $batch";
            $result = mysqli_query($link, $sqlSelect);
            ?>
			
			<!-- Create HTML table with header columns -->
			<table id="batchReviewTable">
				<thead>
				<tr>
					<th>ID</th>
					<th>Account Code</th>
					<th>Account Name</th>
					<th>Debit</th>
					<th>Credit</th>
					<th>Submitter</th>
					<th>Date Created</th>
					<th>Description</th>
				</tr>
				</thead>
			</table>

			<!-- Setup DataTables and link with HTML table -->
			<script type="text/javascript">
				$(document).ready(function() {
					$('#batchReviewTable').dataTable({
						//Setup DataTables with extra parameters
						"processing": true,								//Displays a processing message while fetching data
						"ajax": {
							url: "approvebatchFetchData.php",			//Source of fetch data script
							data: {
								"batchID": "<?php echo $batch ?>",		//Pass parameters to fetch script
							},
						},
						"language": {
							"emptyTable": "No data was found in the database.",	//Used if no SQL data was found 
							"zeroRecords": "No data available in table."	//Used to display msg after filtering
						},
						"rowGroup": {
							"dataSrc": 0,								//0 is the index value for transactionID
						},
						//Link row variables to be displayed with row variables from fetch script (need to match variable naming in fetch script)
						//Note: Variables below will be placed into table sequentially according to order below,
						//		ensure the order below matches order of listed HTML header columns.
						"columns": [
							{ data: 'transactionID', sWidth: '5%' },	//sWidth sets column width
							{ data: 'accountID', sWidth: '10%' },
							{ data: 'faccount' },
							{ data: 'debit' },
							{ data: 'credit' },
							{ data: 'submitterID', sWidth: '10%' },
							{ data: 'datecreated' },
							{data: 'description'},
						]
					});  
				});
			</script>
            
            <form action="" method="post">
                    <select name="Action" id="Action">
                        <option value="1">Approve</option>
                        <option value="2">Decline</option>
                    </select> 
					<input type="submit" value="Update Status" name="UpdateStatus" >
            <?php
            } ?>
            </div>
		</div>
	</body>
</html>