<?php 
include 'accountscripts.php';

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$conn = new mysqli($DATABASE_HOST,$DATABASE_USER ,$DATABASE_PASS ,$DATABASE_NAME );
if ($conn ->connect_error) {
	// If there is an error with the connection, stop the script and display the error.
	die ('Failed to connect to MySQL: ' . $conn ->connect_error);
}
    $generatedUser = generateUsernameByName($_POST['fName'], $_POST['lName']); //this inst allowing me to continue through the php script
    $password= password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $fName = $_POST['fName'];
    $lName = $_POST['lName'];
    $address= $_POST['Address'];
    $city = $_POST['City'];
    $state= $_POST['State'];
    $zip= $_POST['Zip'];
    $DOB= $_POST['DOB'];
    $sq1= $_POST['SecurityQ1'];
    $sa1=$_POST['SecurityA1'];
    $sq2= $_POST['SecurityQ2'];
    $sa2=$_POST['SecurityA2'];
    $email= $_POST['Email'];
    
    $query1 = "INSERT INTO accounts (username, password, Fname, Lname, StreetAddress, City, State, Zip, DOB, SecurityQ1, SecurityA1, SecurityQ2,
    SecurityA2, Email)
    VALUES ('$generatedUser', '$password','$fName', '$lName','$address', '$city','$state','$zip','$DOB','$sq1','$sa1','$sq2','$sa2','$email')";
    $edit = mysqli_query($conn, $query1);
    if($edit)
    {
        if ($stmt = $conn->prepare('SELECT id FROM accounts WHERE username = ?')) {
            // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
            $stmt->bind_param('s', $generatedUser);
            $stmt->execute();
            // Store the result so we can check if the account exists in the database.
            $stmt->store_result();
        
            if ($stmt->num_rows > 0) {
                $stmt->bind_result($qry);
                $stmt->fetch();
            }}
        setPasswordExpire($qry);
        storePassword($qry);
        echo 'Registered successfully, please wait for administrator approval';
        exit;
    }
    else
    {
        echo mysqli_error();
    } 


  //  $stmnt= $conn->prepare('UPDATE accounts SET UserID= ? WHERE ID = '); // How to grab userID?
  //  $stmnt->bind_param('s',$generatedUser);
     
   
    //$query3="UPDATE accounts SET UserID=$generatedUser WHERE Fname= '$fName' AND Lname='$lName' ";
    //retrieves most recent record entered in accounts table
    //$query3= "SELECT * FROM Table ORDER BY ID DESC LIMIT 1";
    
     //gets the entire row from query3
     //$row= $query3 -> fetch_row();
    
     //call function and insert first column in $row array which is the ID value
     //$generatedUser = generateUsernameByID($row['id']);

    // $generatedUser = generateUsernameByName($fName,$lName);

     // $query4= "UPDATE accounts SET UserID= $generatedUser WHERE ID = $row[0]";

    

    /*

    if (mysqli_query($conn, $query1) && mysqli_query($conn, $query2))  {
     echo "New record created successfully !";
    } else {
     echo "Error: " . $sql . " " . mysqli_error($conn);
    }

    mysqli_close($conn);




  $mysqli ->close();
  */

?>




