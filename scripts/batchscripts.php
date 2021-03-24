<?php

function processBatch ($link, $bid, $status) {
    /*function needs to do 2 things: 
    -Update status of batch
    -Update Balances of relavent accounts
    */
    //update status
    //TODO: ADD Date assesed and approver updater to line below
    $aid = $_SESSION['id'];
    $currentDate = date("Y-m-d");
	$sqlupd = "UPDATE transactions SET status = $status, approverID = $aid, dateassessed = $currentDate WHERE batchID = $bid";
     if ($edit = mysqli_query($link, $sqlupd)) {
        if ($status == 1) {
            $sqlupd2 = "SELECT * FROM transactions WHERE batchID = $bid";
            $result = mysqli_query($link, $sqlupd2);
            while($row = mysqli_fetch_array($result)){
                $rowcredit = $row['credit'];
                $rowdebit = $row['debit'];                               
                $rowid = $row['accountID'];
                $acct = substr($rowid, 0, 1);
                if ($acct == 1 || $acct == 5)  {
                    $sqlupd3 = "UPDATE faccount SET fbalance = fbalance - $rowcredit + $rowdebit, debit = debit + $rowdebit, credit = credit + $rowcredit WHERE faccountID = $rowid";
                } else {
                    $sqlupd3 = "UPDATE faccount SET fbalance = fbalance + $rowcredit - $rowdebit, debit = debit + $rowdebit, credit = credit + $rowcredit WHERE faccountID = $rowid";
                }
                if ($result2 = mysqli_query($link, $sqlupd3)) {
                    echo "Update Made";
                } else {
                    exit('Failed to connect to MySQL 1: ' . mysqli_connect_error());
                }
            }
        }
     } else {
        exit('Failed to connect to MySQL 2: ' . mysqli_connect_error());
     }
}
?>