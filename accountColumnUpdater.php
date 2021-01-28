<?php
// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

//Password expiration logic



//Username creator (fName initial + lName + MM date created + YY date created)



//Storing password entries into pastpassword table





$con->close();
?>