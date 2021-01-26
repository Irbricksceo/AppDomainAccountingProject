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

$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
$result = $con->query("SELECT * FROM accounts");
$users = array();
while($data = $result->fetch_assoc()){
   $users[] = $data;
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
                <h1>Website Title</h1>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>Profile Page</h2>
			<div>
                <p>Users:</p>
                
				<table>
					<tr>
						<td>Username:</td>
                        <td>Role</td>
                        <td>Password</td>
                        <td>Email</td>
                    </tr>
                    <?php
                    for ($row = 0; $row <sizeof($users); $row++) {
                        echo"<tr>";
                            for ($col = 0; $col < 4; $col++) {
                            echo"<td>".$users[$row][$col]."</td>";
                        echo "</tr>\n";
                        }
                    }
                    ?>               
				</table>
			</div>
		</div>
	</body>
</html>