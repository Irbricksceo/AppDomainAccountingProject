<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

if ($_SESSION['userrole'] != 1) {
    header("location:home.php"); // Kick Non Admins backl to home
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
		<title>Users Page</title>
		<link href="css/style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
        <link rel="icon" href="images/favicon.ico">
    	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.js"></script>
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
			<h2>Users</h2>
            <div class="tooltip">Hover For Help
  				<span class="tooltiptext">This page lists all users and allows administrators to edit/add users.</span>
			</div>
			<div>
                <a href="adduser.php"></i>Add User</a>
                <hr>
                <div class = "filters">
                <form action="" method="post">
                    <input type="submit" value="Show Only Active Users" name="filterroleactive" >
                </form>
                
                <form action="" method="post">
                    <input type="submit" value="Show Users With Expired Passwords" name="filterroleexpired" >
                </form>
                <hr>
                </div>

				<?php
                    // Attempt select query execution
                    $sql = "SELECT * FROM accounts";
                    if (isset($_POST['filterroleexpired'])) {
                        $date = date('Y-m-d H:i:s');
                        $sql = $sql . " WHERE PasswordExpire < '$date'";
                    }
                    if (isset($_POST['filterroleactive'])) {
                        $sql = $sql . " WHERE active = 1";
                    }
                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>#</th>";
                                        echo "<th>Username</th>";
                                        echo "<th>Role</th>";
                                        echo "<th>Name</th>";
                                        echo "<th>Email</th>";
                                        echo "<th>Created</th>";
                                        echo "<th>Password Expires</th>";
                                        echo "<th>Status</th>";
                                        echo "<th>Edit</th>";
                                        echo"<th>Send Message</th>";
                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
                                    echo "<tr>";
                                        echo "<td>" . $row['ID'] . "</td>";
                                        echo "<td>" . $row['username'] . "</td>";
                                        echo "<td>" . $row['userrole'] . "</td>";
                                        echo "<td>" . $row['Fname'] . " " . $row['Lname'] ."</td>";
                                        echo "<td>" . $row['Email'] . "</td>";
                                        echo "<td>" . $row['DateCreated'] . "</td>";
                                        echo "<td>" . $row['PasswordExpire'] . "</td>";
                                        echo "<td>" . ($row['Active'] == 1 ? "Active" : "Inactive") . "</td>";
                                        echo "<td><a href='edituser.php?r=1&u=".$row['ID']."'>Edit</a></td>";
                                        echo "<td><a href='emailuser.php?u=".$row['ID']."'>MSG</a></td>";
                                        echo "</tr>";
                                }
                                echo "</tbody>";                            
                            echo "</table>";
                            // Free result set
                            mysqli_free_result($result);
                        } else{
                            echo "<p class='lead'><em>No records were found.</em></p>";
                        }
                    } else{
                        echo "ERROR: Could not able to execute $sql. " . mysqli_error($link);
                    }
 
                    // Close connection
                    mysqli_close($link);
                    ?>
            </div>
		</div>
	</body>
</html>