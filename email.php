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
        echo "Email successfully sent to: " .$to_email;
    } else {
        echo "Email could not be sent to: " .$to_email;
    }
}

//Helper function to be used for DB nightly script that checks for expired or close to expiring passwords
function sendPasswordReminderEmail($to_email)
{
    $headers = "From: server.acctpro@gmail.com";
    $subject = "Password Expiration Reminder";
    $body = "Your password for AccountingPro will expire within the next 3 days.  Please change your password now or contact an administrator for help.";

    if (mail($to_email, $subject, $body, $headers)) {
        echo "Email successfully sent to: " .$to_email;
    } else {
        echo "Email could not be sent to: " .$to_email;
    }
}

//Helper function to be used for DB nightly script that checks for expired or close to expiring passwords
function sendPasswordExpiredEmail($to_email)
{
    $headers = "From: server.acctpro@gmail.com";
    $subject = "Password Expired!";
    $body = "Your password for AccountingPro has expired!  Please click 'Forgot Password' on the login screen or contact an administrator for help.";

    if (mail($to_email, $subject, $body, $headers)) {
        echo "Email successfully sent to: " .$to_email;
    } else {
        echo "Email could not be sent to: " .$to_email;
    }
}

function sendActivationEmail($emailToBeActivated)
{
    //Question: Are we sending this activation email to the server email (to act as an admin) or sending to all emails with 
    //a userrole of 1(admin)?
}

function sendEmailFromAdmin()
{
    //Question: How is this differentiated from the sendEmailFromServer function?  With the current setup for the mail function, it will
    //only send emails from the provided email we set the mail server up with (server.acctpro@gmail.com).  Cannot send an email on behalf
    //of a totally different email unless we build out an SMTP server to manage emails for our domain.
    //
    //Possible solution(kinda jank tho): Just have a farewell message included in the body stating the message came from an admin.
    //Possible solution(building on top of the above suggesetion): Pass into function or pull from DB the admin credentials and leave 
    //
}
?>