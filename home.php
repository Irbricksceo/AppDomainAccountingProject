<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="css/style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">

	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<img src="images/logo.png" width="60" alt="Logo">
				<h1>Accounting Pro</h1>
				<?php
					if ($_SESSION['userrole'] == '1'):
						?><a href="users2.php"><i class="fas fa-user-circle"></i>Users</a><?php 
					endif;
				?>
				<a href="home.php"><i class="fas fa-user-circle"></i>Home</a>
				<a href="profile.php"><i class="fas fa-user-circle"></i>Profile</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
				<h4> Logged In As: <?=$_SESSION['name']?> </h4>
			</div>
		</nav>
		<div class="content">
			<h2>Home Page</h2>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>

			<p>Welcome to Accounting Pro, your online tool for managing accounts. We're still under construction.</p>

			<?php 
			if ($_SESSION['userrole'] == '1'):
				?><h3> As an administrator, you currently have access to tools for managing users on the platform. Try it out in the users tab!</h3><?php 
			elseif ($_SESSION['userrole'] == '2'):
				?><h3> As a manager, you do not currently have much functionality, but its coming soon!</h3><?php 
			elseif ($_SESSION['userrole'] == '3'):
				?><h3> As a normal user, you do not currently have much functionality, but its coming soon!</h3><?php 
			else:
				?><h3> Unable to retrieve role, please logout and login again. If this problem persists, please contact your administrator.</h3><?php 
			endif;
			?>
		</div>
	</body>
</html>