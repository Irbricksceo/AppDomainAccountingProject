<?php
        
function logEvent($link, $acct, $changed, $oldData, $newData, $user) {
	$sqllog = "INSERT INTO `eventlog` (`userID`, `faccountID`, `pastversion`, `currentversion`, `changed`) 
    VALUES ('$user', '$acct', '$oldData', '$newData', '$changed')";
    if ($log = mysqli_query($link, $sqllog)) {
        echo "Inserted";
    } else {
        echo "failure to insert" . $acct . $changed . $oldData . $newData . $user;
    }
}

function processBatch ($link, $bid, $status) {

    $aid = $_SESSION['id'];
    $currentDate = date("Y-m-d H:i:s");
	$sqlupd = "UPDATE transactions SET status = $status, approverID = $aid, dateassessed = SYSDATE() WHERE batchID = $bid";
     if ($edit = mysqli_query($link, $sqlupd)) {
        if ($status == 1) {
            $sqlupd2 = "SELECT * FROM transactions WHERE batchID = $bid";
            $result = mysqli_query($link, $sqlupd2);
            while($row = mysqli_fetch_array($result)){
                $rowcredit = $row['credit'];
                $rowdebit = $row['debit'];                               
                $rowid = $row['accountID'];
                $acct = substr($rowid, 0, 1);

                $sqlupd4 = "SELECT * FROM faccount WHERE faccountID = $rowid";
                $result2 = mysqli_query($link, $sqlupd4);
                $row2 = mysqli_fetch_array($result2);

                $oldbal = $row2['fbalance'];
                $oldcred = $row2['credit'];
                $olddeb = $row2['debit'];

                if ($acct == 1 || $acct == 5)  {
                    $sqlupd3 = "UPDATE faccount SET fbalance = fbalance - $rowcredit + $rowdebit, debit = debit + $rowdebit, credit = credit + $rowcredit WHERE faccountID = $rowid";
                } else {
                    $sqlupd3 = "UPDATE faccount SET fbalance = fbalance + $rowcredit - $rowdebit, debit = debit + $rowdebit, credit = credit + $rowcredit WHERE faccountID = $rowid";
                }
                if ($result2 = mysqli_query($link, $sqlupd3)) {
                    //log all changes to event log

                    $sqlupd5 = "SELECT * FROM faccount WHERE faccountID = $rowid";
                    $result3 = mysqli_query($link, $sqlupd5);
                    $row3 = mysqli_fetch_array($result3);
    
                    $newbal = $row3['fbalance'];
                    $newcred = $row3['credit'];
                    $newdeb = $row3['debit'];

                    logEvent($link, $rowid, "Balance", $oldbal, $newbal, $aid);
                    if ($rowdebit > 0) {
                        logEvent($link, $rowid, "Debit", $olddeb, $newdeb, $aid);
                    }
                    if ($rowcredit > 0) {
                        logEvent($link, $rowid, "Credit", $oldcred, $newcred, $aid);
                    }
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