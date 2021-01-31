
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
  
    $fName = $_POST['fName'];
    $lName = $_POST['lName'];
    $address= $_POST['Address'];
    $city = $_POST['City'];
    $state= $_POST['State'];
    $zip= $_POST['Zip'];
    $DOB= $_POST['DOB'];
    $email= $_POST['Email'];
    
    $sql = "INSERT INTO accounts (Fname,Lname,StreetAddress,City,userState,Zip,DOB,Email)
    VALUES ('$fName', '$lName','$address', '$city','$state','$zip','$DOB','$email')";
    if (mysqli_query($conn, $sql)) {
     echo "New record created successfully !";
    } else {
     echo "Error: " . $sql . " " . mysqli_error($conn);
    }

    mysqli_close($conn);




  $mysqli ->close();

?>



