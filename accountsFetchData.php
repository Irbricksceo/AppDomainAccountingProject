<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$sql = "SELECT faccountID, faccount, fcategory, normalside, fbalance, active FROM faccount";
$result = mysqli_query($link, $sql);

while($row = mysqli_fetch_array($result)){
    
    // Clicking account name brings user to ledger for account name	
    $row['faccount'] = "<a href='ledger.php?u=".$row['faccountID']."'>" . $row['faccount'] . "</a>";

    // Convert fcategory int code to string name
    switch ($row['fcategory']){
        case 1:
            $row['fcategory'] = "Asset";
            break;
        case 2: 
            $row['fcategory'] = "Liability";
            break;
        case 3:
            $row['fcategory'] = "Equity";
            break;
        case 4:
            $row['fcategory'] = "Revenue";
            break;
        case 5:
            $row['fcategory'] = "Expense";
            break;
    }

    // Convert normalside int to string name
    if ($row['normalside'] == 0)
        $row['normalside'] = "Debit";
    else
        $row['normalside'] = "Credit";

    $row['active'];
    // Convert active int to string name
    if ($row['active'] == 0)
        $row['active'] = "Deactivated";
    else
        $row['active'] = "Active";

    // DataTables uses two key:value pairs for a specified column, one for array index with number, one for array index with string    
    $acctDetailsLink = "<a href='accountdetails.php?u=".$row['faccountID']."'>Details</a>";
    $row[6] = $acctDetailsLink;
    $row['details'] = $acctDetailsLink;

    $editAcctLink = "<a href='editaccount.php?u=".$row['faccountID']."'>Edit</a>";
    $row[7] = $editAcctLink;
    $row['edit'] = $editAcctLink;
    
    $data[] = $row;
}

$results = ["draw" => 1,
        	"recordsTotal" => count($data),
        	"data" => $data ];

echo json_encode($results);
?>