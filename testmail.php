<?php
include_once("config.php");
echo randomFix(8);exit;
$data=mymail("","mragankshekhar@ms2fun.com","test com","test com");
print_r($data);




/*$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: '.MAIL_SENDER_NAME.'<'.MAIL_SENDER_EMAIL.'>' . "\r\n";*/

?>