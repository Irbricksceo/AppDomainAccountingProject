<?php
// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit;
}
if(isset($_POST['Email'])) {
	$to_email = 'Irbricksceo@gmail.com';
	$body = 'Test Email';
	$subject = 'Test Email';
	$headers = "From: server.acctpro@gmail.com";

    if (mail($to_email, $subject, $body, $headers)) {
        echo "Email successfully sent";
    } else {
        echo "Email sending failed...";
    }
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

			<?php 
			if ($_SESSION['userrole'] == '1'):
				?><h1> Big Letters For Big People</h1><?php 
			elseif ($_SESSION['userrole'] == '2'):
				?><h2> Letters are smaller but you're still important</h2><?php 
			elseif ($_SESSION['userrole'] == '3'):
				?><h3> One day you'll get big letters too</h3><?php 
			else:
				?><h4> This should never show up</h4><?php 
			endif;
			?>

			
			<form action="" method="post">
				<input type="submit" value="Test Email" name="Email" >
			</form>

		</div>
	</body>
</html>