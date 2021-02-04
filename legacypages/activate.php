<?php
// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
// First we check if the email exists...
if (isset($_GET['email'])) {
	if ($stmt = $con->prepare('SELECT * FROM accounts WHERE Email = ? AND Active = ?')) {
        $Active = 0;
        $stmt->bind_param('si', $_GET['email'], $Active);
		$stmt->execute();
		// Store the result so we can check if the account exists in the database.
		$stmt->store_result();
		if ($stmt->num_rows > 0) {
			// Account exists with the requested email and code.
			if ($stmt = $con->prepare('UPDATE accounts SET Active = ? WHERE Email = ?')) {
				// Set the active column to 1(active), this is how we can check if the user has activated their account.
				$Active = 1;
				$stmt->bind_param('is', $Active, $_GET['email']);
				$stmt->execute();
				echo 'The account is now activated! You can now <a href="index.html">login</a>!';
			}
		} else {
			echo 'The account is already activated or doesn\'t exist!';
		}
	}
}
?>