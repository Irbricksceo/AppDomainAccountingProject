<?php

function processBatch ($link, $bid, $status) {
    /*function needs to do 2 things: 
    -Update status of batch
    -Update Balances of relavent accounts
    */
    //update status
	$sqlupd = "UPDATE transactions SET status = $status WHERE batchID = $bid";
     if ($edit = mysqli_query($link, $sqlupd)) {
        if ($status == 1) {
            $sqlupd2 = "SELECT * FROM transactions WHERE batchID = $bid";
            $result = mysqli_query($link, $sqlupd2);
            while($row = mysqli_fetch_array($result)){
                $rowcredit = $row['credit'];
                $rowdebit = $row['debit'];                               
                $rowid = $row['accountID'];
                echo "entering loop for $rowid";
                $sqlupd3 = "UPDATE faccount SET fbalance = fbalance + $rowcredit - $rowdebit, debit = debit + $rowdebit, credit = credit + $rowcredit WHERE faccountID = $rowid";
                if ($result2 = mysqli_query($link, $sqlupd3)) {
                    echo "Update Made";
                } else {
                    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
                }
            }
        }
     } else {
        exit('Failed to connect to MySQL: ' . mysqli_connect_error());
     }
}
?>