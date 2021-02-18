<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$conn = new mysqli($DATABASE_HOST,$DATABASE_USER ,$DATABASE_PASS ,$DATABASE_NAME );
if ($conn ->connect_error) {
	// If there is an error with the connection, stop the script and display the error.
	die ('Failed to connect to MySQL: ' . $conn ->connect_error);
}

$currentdate= date("2022-01-01 11:59:59");
$passwordExpire = DateTime::createFromFormat("Y-m-d H:i:s", $row["PasswordExpire"]);


$query= "SELECT username FROM accounts WHERE $passwordExpire == (2022-01-01 11:59:59)";
$result= $conn->query($query);

mysqli_fetch_array($result);

if($result = mysqli_query($conn, $query)){
        if(mysqli_num_rows($result) > 0){
            while($row = mysqli_fetch_array($result)){
                echo ($row['username']. "<br>");
            }

        }
    }



//echo($result);

//$conn-> close();

?>