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

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Event Log</title>
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
		<link href="css/style.css" rel="stylesheet" type="text/css">
        <link rel="icon" href="images/favicon.ico">
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
				if ($_SESSION['userrole'] == '1') {
					?><h2>User Management</h2>	
					<a href="users2.php"><i class="fas fa-user-circle"></i>Users</a>
					<a href="adduser.php"><i class="fas fa-user-circle"></i>Add A User</a>
					<hr><?php 
				} else {
					?><h2>Transactions</h2>	
					<a href="addtransaction.php"><i class="fas fa-user-circle"></i>Create Batch</a>
					<a href="approvebatch.php"><i class="fas fa-user-circle"></i>Review Batch</a>
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
			<h2>Event Log</h2>
			<div class="tooltip">Hover For Help
  				<span class="tooltiptext">List of all entries in the account event log.</span>
			</div>
			
			<div>
				<hr>
				<form action="" method="post"> 
				<input type= "text" name="accountID" placeholder="Account ID">	
				<input type="submit" value="Filter By Account ID" name="submit" >
				</form>
            	<hr>
              <?php
				$query1 = "SELECT * FROM eventlog";

				//Filter button doesn't produce proper result atm
				if(isset($_POST['submit'])){
					$accountID = $_POST['accountID'];
					$query1 = $query1 . " WHERE faccountID = '$accountID' ";
					
					}

				if($result = mysqli_query($link, $query1)){
					if(mysqli_num_rows($result) > 0){
						echo "<table class='table table-bordered table-striped'>";
							echo "<thead>";
								echo "<tr>"; 
									echo "<th> Event ID</th>";
									echo "<th> User ID</th>";
									echo "<th> Date Changed</th>";
									echo "<th> Altered Account</th>";
									echo "<th> What Changed </th>";
									echo "<th> From</th>";
									echo "<th> To</th>";
								echo "</tr>";
							echo "</thead>";
						echo "<tbody>";
						while($row = mysqli_fetch_array($result)){
						echo "<tr>";
							echo "<td>" . $row['eventID'] . "</td>";
							echo "<td>" . $row['userID'] . "</td>";
							echo "<td>" . $row['datechanged'] . "</td>";
							echo "<td>" . $row['faccountID'] . "</td>";
							echo "<td>" . $row['changed'] . "</td>";
							echo "<td>" . $row['pastversion'] . "</td>";
							echo "<td>" . $row['currentversion'] . "</td>";

						echo"</tr>";
						}
						echo "</tbody>";                            
					echo "</table>";  
					}  else{ 
						echo "<p class='lead'><em>No records were found.</em></p>";
					}
				} 	
				?>  
            </div>
		</div>
	</body>
</html>

