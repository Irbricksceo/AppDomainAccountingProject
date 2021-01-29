<?php

function connectDB()
{
    // Change this to your connection info.
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'accountingprojectlogin';

    // Try and establish connection to DB
    $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    return $con;
}

//Password expiration logic
//Context: A password was changed by a user or admin from the editUser.php page
//Context2: An account was just created and activation email was sent to admin
//Parameter: Accepts an id to be used to search for account in accounts table
function setPasswordExpire($id)
{
    // Try to establish connection to DB
    $con = connectDB();

    // Check if the account with that username exists.
    if ($stmt = $con->prepare('SELECT * FROM accounts WHERE ID = ?')) {
	    // Bind parameters (s = string, i = int, b = blob, etc)
    	$stmt->bind_param('i', $id);
	    $stmt->execute();
	    $stmt->store_result();
	    // Store the result so we can check if the account exists in the database.
    	if ($stmt->num_rows > 0) {
	    	// Account exists
            // Prepare statement with new password expiration date
            if ($stmt = $con->prepare('UPDATE accounts SET PasswordExpire = DATE_ADD(NOW(), INTERVAL 6 MONTH) WHERE ID = ?')) {
                $stmt->bind_param('i', $id);
                $stmt->execute();

                echo ('Account password expiration date has been updated!');
            }
            else{
                // Problem with SQL statement
                echo 'Could not prepare statement!';
            }
        } 
        else {
            // Account not found
            echo ("Account was not found with supplied ID!");
        }
    }

    $con->close();
}

//Username generator (fName initial + lName + MM date created + YY date created)
//Context: An account was just created and activation email was sent to admin
//Paramter: Accepts an id to be used to search for account in accounts table
function generateUsername($id)
{
    // Try to establish connection to DB
    $con = connectDB();

    // Check if the account with that username exists.
    if ($stmt = $con->prepare('SELECT Fname, Lname, dateCreated FROM accounts WHERE ID = ?')) {
	    // Bind parameters (s = string, i = int, b = blob, etc)
    	$stmt->bind_param('i', $id);
	    $stmt->execute();
        $stmt->store_result();
	    // Store the result so we can check if the account exists in the database.
    	if ($stmt->num_rows > 0) {
	    	// Account exists
            // Pull necessary column values for account to generate username
            if ($stmt = $con->prepare('SELECT Fname, Lname, dateCreated FROM accounts WHERE ID = ?')) {
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $stmt->bind_result($Fname, $Lname, $dateCreated);
                $stmt->fetch();
                $stmt->close();
                
                //Build new username
                //Assign Fname initial to username
                $username = substr($Fname, 0, 1);
                //Concatenate Lname to username
                $username .= $Lname;
                //Concatenate MM date created to username
                $username .= substr($dateCreated, 5, 2);
                //Concatenate YY date created to username
                $username .= substr($dateCreated, 2, 2);

                // Update account with new username
                if($stmt = $con->prepare('UPDATE accounts SET username = ? WHERE ID = ?')) {
                    $stmt->bind_param("si", $username, $id);
                    $stmt->execute();

                    echo ("Account username has been updated: " . $username);
                }
                else{
                    // Problem with SQL statement
                    echo ('Could not prepare statement to update username!');
                }
            }
            else{
                // Problem with SQL statement
                echo 'Could not prepare statement to pull account values!';
            }
        } 
        else {
            // Account not found
            echo ("Account was not found with supplied ID!");
        } 
    }
    $con->close();
}


//Storing password entries into pastpassword table
function storePassword()
{

}
?>