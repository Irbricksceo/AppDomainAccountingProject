<?php
session_start();
// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('Please fill both the username and password fields!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $con->prepare('SELECT id, password, userrole, active FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password, $userrole, $status);
        $stmt->fetch();
        // Account exists, now we verify the password.
        // Note: remember to use password_hash in your registration file to store the hashed passwords.
        if (password_verify($_POST['password'], $password)) {
            //checks for inactive users
            if ($status != 1) {
                echo 'This account is currenly disabled, please contact your administrator.';
            } else {
                if ($stmt = $con->prepare('UPDATE accounts SET attempts = 0 WHERE username = ?')) {
                    $stmt->bind_param('s', $_POST['username']);
                    $stmt->execute();
                    // Store the result so we can check if the account exists in the database.
                    $stmt->store_result();
                // Verification success! User has logged-in!
                // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
                session_regenerate_id();
                $_SESSION['loggedin'] = TRUE;
                $_SESSION['name'] = $_POST['username'];
                $_SESSION['id'] = $id;
                $_SESSION['userrole'] = $userrole;
                header('Location: home.php');
            }}
        } else {
            if ($stmt2 = $con->prepare('UPDATE accounts SET attempts = attempts + 1 WHERE username = ?')) {
                $stmt2->bind_param('s', $_POST['username']);
                $stmt2->execute();
            
                    if ($stmt3 = $con->prepare('SELECT attempts FROM accounts WHERE username = ?')) {
                        $stmt3->bind_param('s', $_POST['username']);
                        $stmt3->execute();
                        $stmt3->store_result();
                        if ($stmt3->num_rows > 0) {
                            $stmt3->bind_result($attempts);
                            $stmt3->fetch();
                            
                            if ($attempts == 3) {
                                $stmt4 = $con->prepare('UPDATE accounts SET active = 0, attempts = 0 WHERE username = ?');
                                $stmt4->bind_param('s', $_POST['username']);
                                $stmt4->execute();
                                echo 'Too Many Wrong Attempts, Account Disabled. Please Contact Your Administrator ';
                                
                            }

                }}}

            // Incorrect password
            echo 'Incorrect username and/or password! ';
        }
    } else {
        // Incorrect username
        echo 'Incorrect username and/or password! ';
    }

	$stmt->close();
}
?>