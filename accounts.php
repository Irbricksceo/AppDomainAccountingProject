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
		<title>Accounts</title>
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
			<a href="eventlog.php"><i class="fas fa-user-circle"></i>Event Log</a>
			</div>
		</nav>
		<div class="content">
			<h2>Chart Of Accounts</h2>
			<div>
              
				<?php
					// Display add account button only if userrole == 1(admin)
					if ($_SESSION['userrole'] == '1'):
						echo "<a href='addaccount.php'>Add Account</a>";
					endif;

					// Attempt select query execution
					$sql = "SELECT * FROM faccount";

                    if($result = mysqli_query($link, $sql)){
                        if(mysqli_num_rows($result) > 0){
                            echo "<table class='table table-bordered table-striped'>";
                                echo "<thead>";
                                    echo "<tr>";
                                        echo "<th>#</th>";
                                        echo "<th>Name</th>";
                                        echo "<th>Category</th>";
                                        echo "<th>Normal Side</th>";
                                        echo "<th>Balance</th>";
                                        echo "<th>Status</th>";
										echo "<th>Details</th>";
										
										// Logic to only display edit column if userrole == 1 (admin)
										if ($_SESSION['userrole'] == '1')
                                        	echo "<th>Edit</th>";

                                    echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                while($row = mysqli_fetch_array($result)){
									// Displaying each row from faccounts
                                    echo "<tr>";
                                        echo "<td>" . $row['faccountID'] . "</td>";

										// Clicking account name brings user to ledger for account name	
                                        echo "<td><a href='ledger.php?u=".$row['faccountID']."'>" . $row['faccount'] . "</a></td>";
										
										// Convert fcategory int code to string name
										switch ($row['fcategory']){
											case 1:
												echo "<td>" . "Asset" . "</td>";
												break;
											case 2: 
												echo "<td>" . "Liability" . "</td>";
												break;
											case 3:
												echo "<td>" . "Equity" . "</td>";
												break;
											case 4:
												echo "<td>" . "Revenue" . "</td>";
												break;
											case 5:
												echo "<td>" . "Expense" . "</td>";
												break;
										}

										// Convert normalside int to string name
										if ($row['normalside'] == 0)
											echo "<td>" . "Debit" . "</td>";
										else
											echo "<td>" . "Credit" . "</td>";

                                        echo "<td>" . $row['fbalance'] . "</td>";

										// Convert active int to string name
										if ($row['active'] == 0)
											echo "<td>" . "Deactivated" . "</td>";
										else
											echo "<td>" . "Active" . "</td>";

										// Provide link to view details for account
										echo "<td><a href='accountdetails.php?u=".$row['faccountID']."'>Details</a></td>";

										// Logic to only display edit column if userrole == 1 (admin)
										if ($_SESSION['userrole'] == '1')
                                        	echo "<td><a href='editaccount.php?r=1&u=".$row['faccountID']."'>Edit</a></td>";
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