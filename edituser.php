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


include "scripts/email.php";
include "scripts/accountscripts.php";

//Set a page variable based on if page was entered via profile or users page and parses for a person to be editing. Forces to default for non admins
if(isset($_GET['u'])&& $_SESSION['userrole'] == 1) {
	$editu = $_GET['u'];
} else {
	$editu = $_SESSION['id'];
}

if(isset($_GET['r'])&& $_SESSION['userrole'] == 1) {
	$return = $_GET['r'];
} else {
	$return = 2;
}



$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';
$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
//Primes the query to pull user data based on the user being edited
$sql = "SELECT * FROM accounts WHERE id='$editu'";

//Gets current user data
$qry = mysqli_query($link, $sql);
$data = mysqli_fetch_array($qry);
$newDate = date("Y-m-d", strtotime($data['DOB']));

//Fires update query when form is submitted
if(isset($_POST['update'])) {
	$newEmail = $_POST['email'];
	$newFname = $_POST['firstname'];
	$newLname = $_POST['lastname'];
	$newStreet = $_POST['street'];
	$newCity = $_POST['city'];
	$newState = $_POST['state'];
	$newZip = $_POST['zip'];
	$newDOB = $_POST['dob'];

	//primes, then fires, the update query
	$sqlupd = "UPDATE accounts SET Email = '$newEmail', Fname = '$newFname', Lname = '$newLname', StreetAddress = '$newStreet', City = '$newCity', State = '$newState', Zip = '$newZip', DOB = '$newDOB' WHERE id='$editu'";
	$edit = mysqli_query($link, $sqlupd);
	if($edit)
    {
        header("location:edituser.php?r=$return&u=$editu"); // reload page to refresh the fields, preserves the URL parameters
        exit;
    }
    else
    {
        echo mysqli_error();
	}   	
}
//Separate Script To Fire For Admin Updates
if(isset($_POST['updateADMN'])) {

	$primeMSG = false;
	$newRole = $_POST['role'];
	$newStatus = $_POST['status'];
	if($data['active']!=1) {
		$primeMSG = true;
	}
	$sqlupd = "UPDATE accounts SET userrole = '$newRole', active = '$newStatus' WHERE id='$editu'";
	$edit = mysqli_query($link, $sqlupd);
	if($edit)
    {
		if ($primeMSG == true && $newStatus == 1) {
			$to_email = $data['Email'];
			$body = 'An Administrator Has Activated Your Accounting Pro Account, Sign In Today!';
			$subject = 'Accounting Pro Account Activated';
			sendEmailFromServer($to_email, $subject, $body);
		}
		header("location:edituser.php?r=$return&u=$editu"); // reload page	
    }
    else
    {
        echo mysqli_error();
	} 
} 
//script for updating suspension windows
if(isset($_POST['updateSuspension'])) {
	$susStart = $_POST['start'];
	$susEnd = $_POST['end'];
	setSuspensionDates($editu, $susStart, $susEnd);
	header("location:edituser.php?r=$return&u=$editu"); // reload page	
} 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
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
					if ($return == 1):
						?><a href="users2.php"></i>Back</a><?php 
					elseif ($return == 2):
						?><a href="profile.php"></i>Back</a><?php 
					endif;
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
			</div>
		</nav>
		<div class="content">
			<h2>Editing User</h2>
			<div>
			   <?php
			   echo "<h3> Editing User " . $data['username'] . "</h3>"
			   ?>
			</div>	
			<div class = "float-container"> 
				<div class = "float-child"> 
					<h3> Personal Information </h3>
					<form action="" method="post">
						<?php echo "Email:" ?><br>
						<input type="text" name="email" placeholder="Email" value="<?php echo $data['Email'];?>"><br>
						<?php echo "First Name:" ?><br>
						<input type="text" name="firstname" placeholder="First Name" value="<?php echo $data['Fname'];?>"><br>
						<?php echo "Last Name:" ?><br>
						<input type="text" name="lastname" placeholder="Last Name" value="<?php echo $data['Lname'];?>"><br>
						<?php echo "Street Address:" ?><br>
						<input type="text" name="street" placeholder="Street Address" value="<?php echo $data['StreetAddress'];?>"><br>
						<?php echo "City:" ?><br>
						<input type="text" name="city" placeholder="City" value="<?php echo $data['City'];?>"><br>
						<?php echo "State:" ?><br>
						<input type="text" name="state" placeholder="State" value="<?php echo $data['State'];?>"><br>
						<?php echo "Zip Code:" ?><br>
						<input type="text" name="zip" placeholder="Zip" value="<?php echo $data['Zip'];?>"><br>
						<?php echo "Date of Birth:" ?><br>
						<input type="date" name="dob" value="<?php echo $newDate;?>"><br>
						<?php echo "Security Question 1:" ?><br>
						<input type="text" name="SecurityQ1" placeholder="SecurityQ1" value="<?php echo $data['SecurityQ1'];?>"><br>
						<?php echo "Security Answer 1:" ?><br>
						<input type="text" name="SecurityA1" placeholder="SecurityA1" value="<?php echo $data['SecurityA1'];?>"><br>
						<?php echo "Security Question 2:" ?><br>
						<input type="text" name="SecurityQ2" placeholder="SecurityQ2" value="<?php echo $data['SecurityQ2'];?>"><br>
						<?php echo "Security Answer 2:" ?><br>
						<input type="text" name="SecurityA2" placeholder="SecurityA2" value="<?php echo $data['SecurityA2'];?>"><br>
						<br>
						<input type="submit" value="Update" name="update" >
					</form>
				</div>

				<?php 
				if ($_SESSION['userrole'] == 1 && $editu != $_SESSION['id']) {
				?>
				<div class = "float-child"> 
				<h3> Administrative Functions </h3>
						<form action="" method="post">
							<h4> Role </h4>
							<input type="radio" name="role" value = 1 <?php if($data['userrole']==1) { echo "checked";} if (isset($_POST['role']) && $_POST['role'] ==  '1'): ?>checked='checked'<?php endif; ?>>Administrator<br>
							<input type="radio" name="role" value = 2 <?php if($data['userrole']==2) { echo "checked";} if (isset($_POST['role']) && $_POST['role'] ==  '2'): ?>checked='checked'<?php endif; ?>>Manager<br>
							<input type="radio" name="role" value = 3 <?php if($data['userrole']==3) { echo "checked";} if (isset($_POST['role']) && $_POST['role'] ==  '3'): ?>checked='checked'<?php endif; ?>>User<br>
							</br>
							<h4> Status </h4>
							<input type="radio" name="status" value = 1 <?php if($data['Active']==1) { echo "checked";} if (isset($_POST['status']) && $_POST['status'] ==  '1'): ?>checked='checked'<?php endif; ?>>Active<br>
							<input type="radio" name="status" value = 0 <?php if($data['Active']!=1) { echo "checked";} if (isset($_POST['status']) && $_POST['status'] ==  '0'): ?>checked='checked'<?php endif; ?>>Disabled<br>
							</br>
							<input type="submit" value="Update Role/Status" name="updateADMN" >
						</form> 
					<hr>
						<h3> Set Suspension Window </h3>
						<form action="" method="post">
							<input type="datetime-local" name='start'> <br>
							<input type="datetime-local" name='end'> <br>
							<input type="submit" value="Confirm" name="updateSuspension" >
						</form>
				</div> 
				<?php
				}
				?>
			</div>
		</div>
	</body>
</html>