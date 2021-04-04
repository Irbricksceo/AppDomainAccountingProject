<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

//Get transactionID from dataTables ajax data parameter
if(isset($_GET['transactionID'])) {
	$transactionID = $_GET['transactionID'];
} else {
	$transactionID = 000; //defaults ID to prevent breaking when accessed without a value.
}

$sql = "SELECT t.accountID, fa.faccount, t.debit, t.credit FROM transactions t JOIN faccount fa ON fa.faccountID = t.accountID WHERE t.transactionID = $transactionID";
$result = mysqli_query($link, $sql);

$data = [];

while($row = mysqli_fetch_array($result)){

    // Clicking account name brings user to ledger for account name	
    $row['faccount'] = "<a href='ledger.php?u=".$row['accountID']."'>" . $row['faccount'] . "</a>";

    if ($row['debit'] == 0.00)
        $row['debit'] = "";
    if ($row['credit'] == 0.00)
        $row['credit'] = ""; 

    $data[] = $row;
}

$results = ["draw" => 1,
        	"recordsTotal" => count($data),
        	"data" => $data ];

echo json_encode($results);
?>