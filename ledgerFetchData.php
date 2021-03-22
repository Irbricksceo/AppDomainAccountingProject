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

$sql = "SELECT datecreated, debit, credit, transactionID FROM transactions WHERE accountID = $acct AND status = 1";
$result = mysqli_query($link, $sql);

while($row = mysqli_fetch_array($result)){
    
    $row['description'] = "-";

    $postReferenceLink = "<a href='entrydetails.php?u=".$row['transactionID']."'>View</a>";
    $row['postReference'] = $postReferenceLink;

    $data[] = $row;
}

$results = ["sEcho" => 1,
        	"iTotalRecords" => count($data),
        	"iTotalDisplayRecords" => count($data),
        	"aaData" => $data ];

echo json_encode($results);
?>