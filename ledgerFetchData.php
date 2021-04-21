<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

//Get accountID from dataTables ajax data parameter
if(isset($_GET['accountID'])) {
	$acct = $_GET['accountID'];
} else {
	$acct = 000; //defaults ID to prevent breaking when accessed without a value.
}

$sql = "SELECT datecreated, description, debit, credit, transactionID FROM transactions WHERE accountID = $acct AND status = 1";
$result = mysqli_query($link, $sql);
$data = [];
while($row = mysqli_fetch_array($result)){
    
    //Convert SQL dateTime to more concise format (ex: Jan-18-2021)
    $datecreated = DateTime::createFromFormat("Y-m-d H:i:s", $row['datecreated']);
    $row['datecreated'] = date_format($datecreated, 'M-d-Y');

    $row['description'] = $row['description'];

    if ($row['debit'] == 0.00)
        $row['debit'] = "";
    if ($row['credit'] == 0.00)
        $row['credit'] = ""; 

    $postReferenceLink = "<a href='entrydetails.php?u=".$row['transactionID']."'>View</a>";
    $row['postReference'] = $postReferenceLink;

    $data[] = $row;
}

//Setup DataTables variables and attach $data
$results = ["draw" => 1,
        	"recordsTotal" => count($data),
        	"data" => $data ];

echo json_encode($results);
?>