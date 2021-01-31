<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}


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
//Connect to the DB
$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
//Primes the query to pull current data
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
	$sqlupd = "UPDATE accounts SET Email = '$newEmail', Fname = '$newFname', Lname = '$newLname', StreetAddress = '$newStreet', City = '$newCity', State = '$newState', Zip = '$newZip', DOB = '$newDOB' WHERE id='$editu'";
	$edit = mysqli_query($link, $sqlupd);
	if($edit)
    {
        header("location:edituser.php?r=$return&u=$editu"); // reload page
        exit;
    }
    else
    {
        echo mysqli_error();
	}   	
}
//Separate Script To Fire For Admin Updates
if(isset($_POST['updateADMN'])) {
	$newRole = $_POST['role'];
	$newStatus = $_POST['status'];
	$sqlupd = "UPDATE accounts SET userrole = '$newRole', active = '$newStatus' WHERE id='$editu'";
	$edit = mysqli_query($link, $sqlupd);
	if($edit)
    {
        header("location:edituser.php?r=$return&u=$editu"); // reload page
        exit;
    }
    else
    {
        echo mysqli_error();
	} 
} 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Profile Page</title>
		<link href="css/style.css" rel="stylesheet" type="text/css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	
	<body class="loggedin">
		<nav class="navtop">
			<div>
            <h1>Accounting Pro</h1>
				<?php
					if ($return == 1):
						?><a href="Users2.php"></i>Back</a><?php 
					elseif ($return == 2):
						?><a href="profile.php"></i>Back</a><?php 
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
			<div>
				<h3> Personal Information </h3>
				<form action="" method="post">
					<input type="text" name="email" placeholder="Email" value="<?php echo $data['Email'];?>"><br>
					<input type="text" name="firstname" placeholder="First Name" value="<?php echo $data['Fname'];?>"><br>
					<input type="text" name="lastname" placeholder="Last Name" value="<?php echo $data['Lname'];?>"><br>
					<input type="text" name="street" placeholder="Street Address" value="<?php echo $data['StreetAddress'];?>"><br>
					<input type="text" name="city" placeholder="City" value="<?php echo $data['City'];?>"><br>
					<input type="text" name="state" placeholder="State" value="<?php echo $data['State'];?>"><br>
					<input type="text" name="zip" placeholder="Zip" value="<?php echo $data['Zip'];?>"><br>
					<input type="date" name="dob" value="<?php echo $newDate;?>"><br>
					<input type="submit" value="Update" name="update" >
				</form>
				<?php 
				if ($_SESSION['userrole'] == 1 && $editu != $_SESSION['id']) {
				?>
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
				<?php
				}
				?>
			</div>
		</div>
	</body>
</html>