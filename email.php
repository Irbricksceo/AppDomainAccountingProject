<?php

/*
Current xampp setup will not send emails in its stock configuration.  You must modify a couple config files before 
being able to send any emails or use these functions.  The tutorial I followed to get email sending working can be found here:
https://meetanshi.com/blog/send-mail-from-localhost-xampp-using-gmail/

Server email credentials:
Email: server.acctpro@gmail.com
Password: Appdomain
*/

//A function I quickly put together to allow for an admin to send an email to anyone from the server
function sendEmailFromServer($to_email, $subject, $body)
{
    $headers = "From: server.acctpro@gmail.com";

    if (mail($to_email, $subject, $body, $headers)) {
        echo "Email successfully sent";
    } else {
        echo "Email sending failed...";
    }
}

//Helper function for when we will need to write a script to check the DB for upcoming password expiration
function sendPasswordReminderEmail($to_email)
{
    $headers = "From: server.acctpro@gmail.com";
    $subject = "Password Expiration Reminder";
    $body = "Your password for AccountingPro will expire within the next 3 days.  Please change your password now or contact an administrator for help.";

    if (mail($to_email, $subject, $body, $headers)) {
        echo "Email successfully sent";
    } else {
        echo "Email sending failed...";
    }
}
?>