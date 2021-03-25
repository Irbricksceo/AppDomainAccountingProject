<?php

$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = '';
$DATABASE_NAME = 'accountingprojectlogin';

$link = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if (mysqli_connect_errno()) {
    exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
//Script for adding to event log:
function logEvent($acct, $changed, $oldData, $newData, $user) {
	$sqlupd = "INSERT INTO `eventlog` (`userID`, `faccountID`, `pastversion`, `currentversion`, `changed`) 
    VALUES ('$user', '$acct', '$oldData', '$newData', '$changed)";
    $edit = mysqli_query($link, $sqlupd);
}

//script for checking if account is valid
?>