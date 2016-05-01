<?php

/****
*	A small helper class to help with some features
****/

function sendMail($content)
{
    $to = 'nitzanb@spotoption.com';
    $subject = 'wechat test';
    $headers = 'From: nitzan@twodiv.com' . "\r\n" .
    'Reply-To: nitzan@twodiv.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();
    $message  =   $content;
    mail($to, $subject, $message, $headers);
}

