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

if(isset($_POST['Review'])) {
    $batch = $_POST['Batch'];
	header("location:approvebatch.php?b=$batch"); // reload page	
} 

if(isset($_POST['UpdateStatus'])) {
    $newstatus = $_POST['Action'];
	//TODO: ADD SCRIPT TO PROCESS BATCH WITH STATUS 1 OR 2
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
			</div>
		</nav>
		<div class="content">
			<h2>Batch Review</h2>
			<div>
            <?php
            if(!isset($_GET['b'])) {
                $sqlSelect="SELECT DISTINCT BatchID FROM transactions";
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
            echo "<p> Reviewing Batch " . $batch . ".</p>"

            //ADD TABLE TO VIEW BATCH DETAILS

            ?>
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