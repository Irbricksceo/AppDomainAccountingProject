<?php

include 'email.php';

//**NOTICE TO DEVELOPERS** 
//For testing purposes, change this variable to false so emails do not send.
$sendEmail = true;

/*File context: All functions in this file are intended to be run intervally(nightly).  
Functions will include updates to the DB based upon certain criteria.
*/

//Helper function to connect to DB
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

//Purpose: Run through all accounts to check if account password has expired or is about to expire.
//Goal 1: If current time until passwordExpire is less than or equal to 3 days, send email reminder.
//Goal 2: If current time is after passwordExpire, set account active variable to 0 (inactive)
function checkExpiringPasswords()
{
    // Try to establish connection to DB
    $con = connectDB();

    //Set timezone.  To be used to find difference between now and passwordExpire
    date_default_timezone_set("America/New_York");

    //SQL query
    $sql = 'SELECT Email, PasswordExpire FROM accounts';
    
    if ($result = mysqli_query($con, $sql)) {
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                
                $currentDateTime = new DateTime();
                $passwordExpire = DateTime::createFromFormat("Y-m-d H:i:s", $row["PasswordExpire"]);
                $difference = $currentDateTime->diff($passwordExpire, false);

                //Password has expired 
                if ($currentDateTime > $passwordExpire){
                    //Set active to 0

                    if ($stmt = $con->prepare('UPDATE accounts SET Active = 0 WHERE Email = ?')) {
                        $stmt->bind_param('s', $row['Email']);
                        $stmt->execute();
        
                        echo ('Account has been disabled due to expired password!');
                    }
                    else{
                        // Problem with SQL statement
                        echo ('Could not prepare statement!');
                    }

                    //Send account disabled email
                    if($sendEmail)
                        sendPasswordExpiredEmail($row['Email']);
                }
                //Password is within 3 days of expiring
                if ($difference->format("%R%a") < 3 && $difference->format("%R%a") >= 0){
                    
                    //Send password expiration email reminder
                    if($sendEmail)
                        sendPasswordReminderEmail($row['Email']);
                }
            }
        }
        else{
            //No rows were returned after sql query
            echo ('No records were returned!');
        }
    }
    else{
        // Problem with SQL statement
        echo ('Error with SQL statement!');
    }
}


?>