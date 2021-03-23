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
if(isset($_GET['transactionID'])) {
	$transactionID = $_GET['transactionID'];
} else {
	$transactionID = 000; //defaults ID to prevent breaking when accessed without a value.
}

$transactionID = 100;

$sql = "SELECT t.accountID, fa.faccount, t.debit, t.credit FROM transactions t JOIN faccount fa ON fa.faccountID = t.accountID WHERE t.transactionID = $transactionID AND t.status = 1";
$result = mysqli_query($link, $sql);

while($row = mysqli_fetch_array($result)){

    if ($row['debit'] == 0.00)
        $row['debit'] = "";
    if ($row['credit'] == 0.00)
        $row['credit'] = ""; 

    $data[] = $row;
}

$results = ["sEcho" => 1,
        	"iTotalRecords" => count($data),
        	"iTotalDisplayRecords" => count($data),
        	"aaData" => $data ];

echo json_encode($results);
?>