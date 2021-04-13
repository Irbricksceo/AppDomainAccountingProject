<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}

$reportType = $_GET['reportID'];
$startDate = $_GET['startDate'];
$endDate = $_GET['endDate'];

//To be used to store rows from SQL query response
$data = [];

switch ($reportType) {
    //Trial Balance
    case 1: 

        $sql = "SELECT faccountID, faccount, normalside, fbalance FROM faccount ORDER BY faccountID ASC";
        $result = mysqli_query($link, $sql);

        $totalDebit = 0;
        $totalCredit = 0;
        
        while($row = mysqli_fetch_array($result)){
            
            //Assigning balance to debit side
            if ($row['normalside'] == 0) {

                //Add balance to totalDebit
                $totalDebit += $row['fbalance'];

                //Add parentheses if balance is negative
                if ($row['fbalance'] < 0) {
                    $row['debit'] = "(" . number_format(abs($row['fbalance']), 2) . ")";
                    $row['credit'] = "";
                }
                //Balance is positive
                else {
                    $row['debit'] = number_format($row['fbalance'], 2);
                    $row['credit'] = "";
                }    
            }
            //Assigning balance to credit side
            else {

                //Add balance to totalCredit
                $totalCredit += $row['fbalance'];

                //Add parentheses if balance is negative
                if ($row['fbalance'] < 0) {
                    $row['credit'] = "(" . number_format(abs($row['fbalance']), 2) . ")";
                    $row['debit'] = "";
                }
                //Balance is positive
                else {
                    $row['credit'] = number_format($row['fbalance'], 2);
                    $row['debit'] = "";
                }   
            }
            
            //Add row into data array
            $data[] = $row;
        }

        //Create new row for total debits and credits
        $newRow['faccountID'] = "";
        $newRow['faccount'] = "";
        $newRow['debit'] = '<b>' . number_format($totalDebit, 2) . '</b>';
        $newRow['credit'] = '<b>' . number_format($totalCredit, 2) .  '</b>';

        //Add new row to data
        $data[] = $newRow;

        break;

    //Income Statement
    case 2:

        $sql = "";
        $result = mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($result)){
            //Write conversions here
            
            //Add row into data array
            $data[] = $row;
        }

        //Create new row for aggregated data/totals


        //Add new row to data


        break;

    //Balance Sheet
    case 3:
        
        $sql = "";
        $result = mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($result)){
            //Write conversions here
            
            //Add row into data array
            $data[] = $row;
        }

        //Create new row for aggregated data/totals

        
        //Add new row to data


        break;

    //Retained Earnings
    case 4:

        $sql = "";
        $result = mysqli_query($link, $sql);

        while($row = mysqli_fetch_array($result)){
            //Write conversions here
            
            //Add row into data array
            $data[] = $row;
        }

        //Create new row for aggregated data/totals

        
        //Add new row to data


        break;
}

$results = ["draw" => 1,
        	"recordsTotal" => count($data),
        	"data" => $data ];

echo json_encode($results);
?>