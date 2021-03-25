<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

//Get batchID from dataTables ajax data parameter
if(isset($_GET['batchID'])) {
	$batchID = $_GET['batchID'];
} else {
	$batchID = 000; //defaults ID to prevent breaking when accessed without a value.
}

//Setup SQL query for data to be retrieved
$sql = "SELECT t.transactionID, t.accountID, fa.faccount, t.debit, t.credit, t.submitterID, datecreated FROM transactions t 
JOIN faccount fa ON fa.faccountID = t.accountID WHERE t.batchID = $batchID AND t.status = 0";
$result = mysqli_query($link, $sql);

//Declare array for SQL rows to be stored
$data = [];

//Fetch each row
while($row = mysqli_fetch_array($result)){

    // Clicking account name brings user to ledger for account name	
    $row['faccount'] = "<a href='ledger.php?u=".$row['accountID']."'>" . $row['faccount'] . "</a>";

    //Substitue blanks for credit/debit 0.00 values
    if ($row['debit'] == 0.00)
        $row['debit'] = "";
    if ($row['credit'] == 0.00)
        $row['credit'] = ""; 

    //Convert SQL dateTime to more concise format (ex: Jan-18-2021)
    $datecreated = DateTime::createFromFormat("Y-m-d H:i:s", $row['datecreated']);
    $row['datecreated'] = date_format($datecreated, 'M-d-Y');

    //Store each retrieved row into data array (2d array)
    $data[] = $row;
}

//Setup final array with variables for DataTables pagination and data to be displayed
$results = ["draw" => 1,                        //Not sure what this does, something for security
        	"recordsTotal" => count($data),     //Used for pagination
        	"data" => $data];                   //Used for attaching retrieved SQL data

//Return a JSON of the array above
echo json_encode($results);
?>