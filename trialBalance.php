<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

if ($_SESSION['userrole'] != 2) {
    header("location:home.php"); // Kick Non managers back to home
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

//Parse and store variables passed in URL
$startDate = $_GET['sDate'];
$endDate = $_GET['eDate'];

//Formatting date to be displayed in page header
$f_startDate = DateTime::createFromFormat("Y-m-d", $startDate);
$f_startDate = date_format($f_startDate, 'M d, Y');
$f_endDate = DateTime::createFromFormat("Y-m-d", $endDate);
$f_endDate = date_format($f_endDate, 'M d, Y');
    
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Generate Reports</title>
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
        table.dataTable tbody th, table.dataTable tbody td {
            padding: 2px 18px;      
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
			</div>
		</nav>
		
        <div class="content">
            <h2>Trial Balance For Dates: <?php echo "$f_startDate" ?> - <?php echo "$f_endDate" ?> </h2>

            <div>
                <table id="trialBalanceTable" class="row-border">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Account Name</th>
                        <th>Debit</th>
                        <th>Credit</th>
                    </tr>
                    </thead>
                </table>

                <script type="text/javascript">
                    $(document).ready(function() {
                        $('#trialBalanceTable').dataTable({
                            "processing": true,
                            "ajax": {
                                url: "reportsFetchData.php",
                                data: {
                                    "reportID": 1,
                                    "startDate": "<?php echo $startDate ?>",
                                    "endDate": "<?php echo $endDate ?>"
                                }
                            },
                            "language": {
                                "emptyTable": "No data was found in the database.",	//Used if no SQL data was found 
                                "zeroRecords": "No data available in table."	//Used to display msg after filtering
                            },
                            "paging": false,    //Shows all rows without pagination
                            "order": [0, 'asc'],        
                            "columns": [
                                { data: 'accountID', width: '5%', orderable: false },
                                { data: 'faccount', orderable: false  },
                                { data: 'debit', orderable: false  },
                                { data: 'credit', orderable: false  },
                            ]
                        });  
                    });
                </script>
            </div>
	</body>
</html>