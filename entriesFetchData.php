<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$sql = "SELECT * FROM transactions";
$result = mysqli_query($link, $sql);

while($row = mysqli_fetch_array($result)){
    $data[] = $row;
}

//Go through each row in data array
$length = count($data);
for ($i=0; $i<$length; $i++)
{
    //Store transactionID to be used to check for other rows with matching transactionID
    $ID = $data[$i]['transactionID'];

    //ASSUMPTION: All rows sharing the same transactionID will have identical values for the variables below
    //ACTION: Pull these values stored in the first row with a unique transactionID
    //Variables that are identical for all rows under a unique transactionID
    $transactionID = $data[$i]['transactionID'];
    $batchID = $data[$i]['batchID'];
    $status = $data[$i]['status'];
    $submitterID = $data[$i]['submitterID'];
    $approverID = $data[$i]['approverID'];
    $datecreated = $data[$i]['datecreated'];
    $dateassessed = $data[$i]['dateassessed'];
    $details = "<a href='entrydetails.php?u=".$transactionID."'>Details</a>";

    //Variables that need to be aggregated from rows with matching transactionID
    $accountsAffected = "";
    $amountMoved = 0.00;

    //Convert status int value to string value
    switch ($status){
        case 0:
            $status = "Pending";
            break;
        case 1: 
            $status = "Approved";
            break;
        case 2:
            $status = "Rejected";
            break;
        case 3:
            $status = "In-Progress";
    }



    //Find rows with matching transactionID to aggregate accountsAffected and amountMoved
    while ($i < $length)
    {
        if ($ID == $data[$i]['transactionID'])
        {
            //Pull appproriate data from row to form new aggregated variable
            $accountsAffected .= strval($data[$i]['accountID']) . ", ";
            $amountMoved += $data[$i]['debit'];     //Since debit=credit in transaction, either could be used

            //Update index variable to look at next row
            //Updating index also serves to set the for loop to next unique transactionID
            $i++;
        }
        else
            break;   
    }

    //Build new row by appending key:value pairs where key is variable name
    $newRow['transactionID'] = $transactionID;
    $newRow['batchID'] = $batchID;
    $newRow['accountsAffected'] = substr($accountsAffected, 0, -2);     //Substr to remove last appended comma
    $newRow['amountMoved'] = $amountMoved;
    $newRow['status'] = $status;
    $newRow['submitterID'] = $submitterID;
    $newRow['approverID'] = $approverID;
    $newRow['datecreated'] = $datecreated;
    $newRow['dateassessed'] = $dateassessed;
    $newRow['details'] = $details;

    //Add new row to new data array
    $newData[] = $newRow;


}

$results = ["sEcho" => 1,
        	"iTotalRecords" => count($data),
        	"iTotalDisplayRecords" => count($data),
        	"aaData" => $newData ];


echo json_encode($results);


?>