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

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

//Gets Account to view from the URL
if(isset($_GET['u'])) {
	$transactionID = $_GET['u'];
} else {
	$transactionID = 000; //defaults ID to prevent breaking when accessed without a value.
}


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
			<a href="eventlog.php"><i class="fas fa-user-circle"></i>Event Log</a>
			</div>
		</nav>
		<div class="content">
			<h2>Details For Transaction Number: <?php echo "$transactionID" ?> </h2>
			<div class="tooltip">Hover For Help
  				<span class="tooltiptext">This page shows the details for a transaction.</span>
			</div>
			<div>

				<table id="entryDetailsTable">
						<thead>
						<tr>
							<th>Account Code</th>
							<th>Account Name</th>
							<th>Debit</th>
							<th>Credit</th>
						</tr>
						</thead>
					</table>

					<script type="text/javascript">
						$(document).ready(function() {
							$('#entryDetailsTable').dataTable({
								"processing": true,
								"ajax": {
									url: "entrydetailsFetchData.php",
									data: {
										"transactionID": "<?php echo $transactionID ?>",
									}
								},
								"language": {
									"emptyTable": "No data was found in the database.",	//Used if no SQL data was found 
									"zeroRecords": "No data available in table."	//Used to display msg after filtering
								},
								"columns": [
									{ data: 'accountID', sWidth: '10%' },
									{ data: 'faccount' },
									{ data: 'debit' },
									{ data: 'credit' },
								]
							});  
						});
					</script>

                <?php
                    //NEED TO ADD DESCRIPTION AND SOURCE FILE... eh, maybe not
                    /*
                    if ($stmt = $link->prepare('SELECT description, sourceDocument FROM transactions WHERE transactionID = ?')){
                        $stmt->bind_param('i', $transactionID);
                        $stmt->execute();
                        $stmt->bind_result($description, $sourceDocument);
                        $stmt->fetch();
                        $stmt->close();
                    }*/
				?>

                    <hr>
                    <p>Description: </th>
                    <br>
                    <p>Files Attatched: </th>         

            </div>
		</div>
	</body>
</html>