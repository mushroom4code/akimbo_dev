<?php
	$to      = 'rodionova@enterego.ru';
	$subject = 'the subject';
	$message = 'hello';
	$headers = 'From: robot@akimbo-moda.ru' . "\r\n" .
    	'Reply-To: robot@akimbo-moda.ru' . "\r\n" .
    	'X-Mailer: PHP/' . phpversion();

	if(mail($to, $subject, $message, $headers))
		echo 'SUCCESS';
	else
		echo '!!!FAIL!!!'
?>