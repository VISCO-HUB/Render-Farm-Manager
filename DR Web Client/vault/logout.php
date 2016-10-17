<?php		
	SESSION_START();
	
	$_SESSION['logged'] = false;
	$_SESSION['user'] = '';
		
	$JSON['ip'] = $_SERVER['REMOTE_ADDR'];
	$JSON['user'] = '';	
	$JSON['logged'] = false;
	
	SESSION_UNSET();
	SESSION_UNSET();
	SESSION_UNSET();
	SESSION_DESTROY();
	SESSION_DESTROY();
	SESSION_DESTROY();
			
	ECHO JSON_ENCODE($JSON);	
?>