<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$sql = "SELECT transactionID, batchID, status FROM transactions";
$result = mysqli_query($link, $sql);

while($row = mysqli_fetch_array($result)){

    // Convert fcategory int code to string name
    switch ($row['status']){
        case 0:
            $row['status'] = "Pending";
            break;
        case 1: 
            $row['status'] = "Approved";
            break;
        case 2:
            $row['status'] = "Rejected";
            break;
    }
    
    $data[] = $row;
}


$results = ["sEcho" => 1,
        	"iTotalRecords" => count($data),
        	"iTotalDisplayRecords" => count($data),
        	"aaData" => $data ];


echo json_encode($results);


?>