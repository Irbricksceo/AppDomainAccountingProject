<?php

function processBatch ($link, $bid, $status) {
    /*function needs to do 2 things: 
    -Update status of batch
    -Update Balances of relavent accounts
    */
    //update status
    //TODO: ADD Date assesed and approver updater to line below
    $aid = $SESSION_['id'];
	$sqlupd = "UPDATE transactions SET status = $status, approverID = $aid, dateassessed = DATE_ADD(NOW() WHERE batchID = $bid";
     if ($edit = mysqli_query($link, $sqlupd)) {
        if ($status == 1) {
            $sqlupd2 = "SELECT * FROM transactions WHERE batchID = $bid";
            $result = mysqli_query($link, $sqlupd2);
            while($row = mysqli_fetch_array($result)){
                $acct = //make this the first digit of the account
                $rowcredit = $row['credit'];
                $rowdebit = $row['debit'];                               
                $rowid = $row['accountID'];
                if ($acct == 0)  {//If its a debit acccount
                    $sqlupd3 = "UPDATE faccount SET fbalance = fbalance + $rowcredit - $rowdebit, debit = debit + $rowdebit, credit = credit + $rowcredit WHERE faccountID = $rowid";
                } else {
                    $sqlupd3 = "UPDATE faccount SET fbalance = fbalance + $rowcredit - $rowdebit, debit = debit + $rowdebit, credit = credit + $rowcredit WHERE faccountID = $rowid";
                }
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