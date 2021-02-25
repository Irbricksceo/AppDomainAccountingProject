<?php

//Script for adding to event log:
function logEvent($acct, $changed, $oldData, $newData, $user) {
	$sqlupd = "INSERT INTO `eventlog` (`userID`, `faccountID`, `pastversion`, `currentversion`, `changed`) 
    VALUES ('$user', '$acct', '$oldData', '$newData', '$changed)";
    $edit = mysqli_query($link, $sqlupd);
}


//script for checking if account is valid
?>