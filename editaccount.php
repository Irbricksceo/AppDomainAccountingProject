<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

//-----------------

if ($_SESSION['userrole'] != 1) {
    header("location:home.php"); // Kick Non Admins backl to home
    exit;
}

include "scripts/accountscripts.php";

//Set a page variable based on if page was entered via profile or users page and parses for a person to be editing. Forces to default for non admins
if(isset($_GET['u'])&& $_SESSION['userrole'] == 1) {
	$editu = $_GET['u'];
} else {
	$editu = 0;
}

//--------------------
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
//-----------------
//Primes the query to pull user data based on the user being edited
$sql = "SELECT * FROM faccount WHERE faccountID='$editu'";

//Gets current user data
$qry = mysqli_query($link, $sql);
$data = mysqli_fetch_array($qry);

//Fires update query when form is submitted
if(isset($_POST['update'])) {
	$newfaccountID = $_POST['faccountID'];
	$newfaccount = $_POST['faccount'];
	$newfdescription = $_POST['fdescription'];
	$newnormalside = $_POST['normalside'];
	$newfcategory = $_POST['fcategory'];
	$newcomment = $_POST['comment'];



	//primes, then fires, the update query
	$sqlupd = "UPDATE faccount SET faccountID = '$newfaccountID', faccount = '$newfaccount', fdescription = '$newfdescription', normalside = '$newnormalside', fcategory ='$newfcategory',comment = '$newcomment' WHERE faccountID='$editu'";
	$edit = mysqli_query($link, $sqlupd);
	if($edit)
    {
		//The below chunk of code creates the event log for the change
		$sql2 = "SELECT * FROM faccount WHERE faccountID='$newfaccountID'";
		$qry2 = mysqli_query($link, $sql2);
		$data2 = mysqli_fetch_array($qry2); //First we create a new array containing the updated values
		
		$changed = "";
		$oldData = "";
		$newData = "";
		$user = $_SESSION['id'];

		//now we scan fields for changes. I feel like thsi should be loopable, but damn if I could figure out how. Each block adds any paramaters that changed
		if (strcmp($data['faccountID'] , $data2['faccountID']) != 0) {
			$changed = $changed . "ID" . "<br> ";
			$oldData = $oldData . $data['faccountID'] . "<br> ";
			$newData = $newData . $data2['faccountID'] . "<br> ";
		}

		if (strcmp($data['faccount'] , $data2['faccount']) != 0) {
			$changed = $changed . "Account Name" . "<br> ";
			$oldData = $oldData . $data['faccount'] . "<br> ";
			$newData = $newData . $data2['faccount'] . "<br> ";
		}

		if (strcmp($data['fdescription'] , $data2['fdescription']) != 0) {
			$changed = $changed . "Description" . "<br> ";
			$oldData = $oldData . $data['fdescription'] . "<br> ";
			$newData = $newData . $data2['fdescription'] . "<br> ";
		}

		if (strcmp($data['normalside'] , $data2['normalside']) != 0) {
			$changed = $changed . "normalside" . "<br> ";
			$oldData = $oldData . $data['normalside'] . "<br> ";
			$newData = $newData . $data2['normalside'] . "<br> ";
		}

		if (strcmp($data['fcategory'] , $data2['fcategory']) != 0) {
			$changed = $changed . "Category" . "<br> ";
			$oldData = $oldData . $data['fcategory'] . "<br> ";
			$newData = $newData . $data2['fcategory'] . "<br> ";
		}

		if (strcmp($data['comment'] , $data2['comment']) != 0) {
			$changed = $changed . "Comment" . "<br> ";
			$oldData = $oldData . $data['comment'] . "<br> ";
			$newData = $newData . $data2['comment'] . "<br> ";
		}
		//finally, we use the completed strings for the log
		$sqllog = "INSERT INTO `eventlog` (`userID`, `faccountID`, `pastversion`, `currentversion`, `changed`) 
		VALUES ('$user', '$newfaccountID', '$oldData', '$newData', '$changed')";
		$log = mysqli_query($link, $sqllog);
        //Finally, update the "account ID" for every entry in the log from the old ID to the new ID to maintain tracability
		$sqllog = "UPDATE eventlog SET faccountID = '$newfaccountID' WHERE faccountID='$editu'";
		$log = mysqli_query($link, $sqllog);

		header("location:accounts.php");
        exit;
    }
    else
    {
        echo mysqli_error($link);
	}   	
}
//Separate Script To Fire For Admin Updates
if(isset($_POST['updateADMN'])) {

	$primeMSG = false;
	$newStatus = $_POST['status'];
	if($data['active']!=1) {
		$primeMSG = true;
	}
	$sqlupd = "UPDATE accounts SET active = '$newStatus' WHERE faccountID='$editu'";
	$edit = mysqli_query($link, $sqlupd);
	if($edit)
    {
		header("location:accounts.php"); 
    }
    else
    {
        echo mysqli_error();
	} 
} 

//----------------
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Page Title</title>
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
			<h2>Edit Account</h2>
			<div>
            <!----->
			<?php
			   echo "<h3> Editing Account " . $data['faccount'] . "</h3>"
			   ?>
			</div>	
			<div class = "float-container"> 
				<div class = "float-child"> 
					<h3> Account Information </h3>
					<form action="" method="post">
						<?php echo "ID:" ?><br>
						<input type="text" name="faccountID" placeholder="faccountID" value="<?php echo $data['faccountID'];?>"><br>
						<?php echo "Name:" ?><br>
						<input type="text" name="faccount" placeholder="faccount" value="<?php echo $data['faccount'];?>"><br>
						<?php echo "Description:" ?><br>
						<input type="text" name="fdescription" placeholder="Description" value="<?php echo $data['fdescription'];?>"><br>
						<label for="Category">Category:</label><br>
						<select name="fcategory" id="fcategory">
                        	<option value="1">Assets</option>
                        	<option value="2">Liabilities</option>
                        	<option value="3">Equity</option>
                        	<option value="4">Revenues</option>
                        	<option value="5">Expenses</option>
							</select> <br>
						<?php echo "Normal Side:" ?><br>
						<input type="text" name="normalside" placeholder="Normal Side" value="<?php echo $data['normalside'];?>"><br>
						<?php echo "Comment:" ?><br>
						<input type="text" name="comment" placeholder="Comment" value="<?php echo $data['comment'];?>"><br>
						<input type="submit" value="Update" name="update" >
					</form>
				</div>

				<?php 
				if ($_SESSION['userrole'] == 1 && $editu != $_SESSION['id']) {
				?>
				<div class = "float-child"> 
				<h3> Administrative Functions </h3>
						<form action="" method="post">
							<h4> Status </h4>
							<input type="radio" name="status" value = 1 <?php if($data['active']==1) { echo "checked";} if (isset($_POST['status']) && $_POST['status'] ==  '1'): ?>checked='checked'<?php endif; ?>>Active<br>
							<input type="radio" name="status" value = 0 <?php if($data['active']!=1) { echo "checked";} if (isset($_POST['status']) && $_POST['status'] ==  '0'): ?>checked='checked'<?php endif; ?>>Disabled<br>
							</br>
							<input type="submit" value="Update Status" name="updateADMN" >
						</form> 
					<hr>
				</div> 
				<?php
				}
				?>
			<!----->     
            </div>
		</div>
	</body>
</html>