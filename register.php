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

// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['username'], $_POST['password'], $_POST['email'])) {
	// Could not get the data that should have been sent.
	exit('Please complete the registration form!');
}

// Make sure the submitted registration values are not empty.
if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email'])) {
	// One or more values are empty.
	exit('Please complete the registration form');
}

// Check if entered email is valid
if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
	exit('Email is not valid!');
}

// Check for invalid characters in username
// **Unlikely to be used as email address will server as login ID
// Might be useful for something else
if (preg_match('/^[a-zA-Z0-9]+$/', $_POST['username']) == 0) {
    exit('Username is not valid!');
}

// Check for correct number of characters in password
if (strlen($_POST['password']) > 30 || strlen($_POST['password']) < 8) {
	exit('Password must be between 8 and 30 characters long!');
}

// We need to check if the account with that username exists.
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	$stmt->store_result();
	// Store the result so we can check if the account exists in the database.
	if ($stmt->num_rows > 0) {
		// Username already exists
		echo 'Username exists, please choose another!';
	} else {
        // Username doesnt exists, insert new account
        if ($stmt = $con->prepare('INSERT INTO accounts (username, password, email, userrole, Active) VALUES (?, ?, ?, ?, ?)')) {
	        // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            //Userrole set to 3(user) on default for registration
            $userrole = 3;
            //Active set to 0(inactive) on default for registration
            $Active = 0;
	        $stmt->bind_param('sssii', $_POST['username'], $password, $_POST['email'], $userrole, $Active);
            $stmt->execute();
            
            //Logic for sending account activation email to admin.
            $from    = 'noreply@accountingpro.com';
            $subject = 'New User Account Activation';
            $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
            // Update the activation variable below
            //**WILL REQUIRE CHANGES TO URL UPON PUSH TO LIVE**
            $activate_link = 'http://localhost/AccountingProject/activate.php?email=' . $_POST['email'];
            $message = '<p>Please click the following link to activate the account: <a href="' . $activate_link . '">' . $activate_link . '</a></p>';
            $adminEmail = 'barrettrose1@gmail.com';
            mail($adminEmail, $subject, $message, $headers);


            echo 'You have successfully registered!  Please wait for account activation before logging in.';
        } else {
	        // Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	        echo 'Could not prepare statement!';
        }
	}
	$stmt->close();
} else {
	// Something is wrong with the sql statement, check to make sure accounts table exists with all 3 fields.
	echo 'Could not prepare statement!';
}
$con->close();
?>