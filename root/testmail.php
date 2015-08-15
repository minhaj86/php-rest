<?php

$to  = "rubaiat.csedu.10@gmail.com"; 
$subject = "Greetings from qLog";
$message = "<html><body>Hi Rubaiat<br />Congratulations to your qLog Service. Below is your credential:<br />Username: rubaiat<br />Password: saikat</body></html>";
$headers  = 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
$headers .= 'From: QLOG <noreply@snowbean.net>' . "\r\n";

mail($to,$subject,$message,$headers);
?>
