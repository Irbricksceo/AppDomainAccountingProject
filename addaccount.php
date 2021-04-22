<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}

include 'scripts/userscripts.php'; 

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';
//Connect to the DB
$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

if ($_SESSION['userrole'] != 1) {
    header("location:home.php"); // Kick Non Admins backl to home
    exit;
}

//Fires update query when form is submitted
if(isset($_POST['Create'])) {
    //will actually run the sql
    $AcctName = $_POST['Name'];
    $Category = $_POST['Category'];
    $subCategory = $_POST['Subcategory'];
    $Comment = $_POST['Comment'];
    $Desc = $_POST['Description'];
    $startBal = $_POST['StartingBalance'];
    $createdBy = $_SESSION['id'];

    //find number of rows in current category, increments, and *10 it to get the acct number portion
    $query = "SELECT * FROM faccount WHERE fcategory = $Category"; 
    $result = mysqli_query($link, $query);  
    if ($result) 
    { 
        $row = mysqli_num_rows($result);
        $row ++;
        $row = $row*10;
    }
    //concat category with above number to create account ID number
    $acctID = $Category . $row;
    // Sets normal Side based on category
    if ($Category == 1 || $Category == 5) {
        $normalSide = 0;
    } else {
        $normalSide = 1;
    }

	//creates the qry to add an acct
	$sqlupd = "INSERT INTO `faccount` (`faccountID`, `faccount`, `fdescription`, `normalside`, `fcategory`, `fsubcategory`, `finitialbalance`, `debit`, `credit`, `fbalance`, `userID`, `comment`, `active`) 
    VALUES ('$acctID', '$AcctName', '$Desc', '$normalSide', '$Category', '$subCategory', $startBal, 0.00, 0.00, '$startBal', '$createdBy', '$Comment', '1')";

    $edit = mysqli_query($link, $sqlupd); //runs the qry
    if($edit) //entered if acct created successfully
    {
        header("location:accounts.php"); // return to users page
        exit;
    }
    else
    {
        echo "Could Not Add Account, SQL Returned Error";
    }
}
 
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Create New Account</title>
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
			<h2>Create New Account</h2>	
			<div class="tooltip">Hover For Help
  				<span class="tooltiptext">Make Sure All Fields Are Filled And Press Submit To Add Accounts.</span>
			</div>
			<div>
				<h3> Account Information </h3>
				<form action="" method="post">
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
					<input type="submit" value="Create" name="Create" >
				</form>
			</div>
		</div>
	</body>
</html>