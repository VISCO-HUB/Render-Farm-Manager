<?php	
	SESSION_START();
	SESSION_DESTROY();
	SESSION_DESTROY();
	SESSION_DESTROY();
		
	$JSON['ip'] = $_SERVER['REMOTE_ADDR'];
	$JSON['user'] = '';	
	$JSON['logged'] = false;	
			
	ECHO JSON_ENCODE($JSON);	
?>