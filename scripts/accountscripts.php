<?php

function connectDB()
{
    // Change this to your connection info.
    $DATABASE_HOST = 'localhost';
    $DATABASE_USER = 'root';
    $DATABASE_PASS = '';
    $DATABASE_NAME = 'accountingprojectlogin';

    // Try and establish connection to DB
    $con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);

    return $con;
}

//Script for adding to event log:
function logEvent ($) {

}


//script for checking if account is valid
?>