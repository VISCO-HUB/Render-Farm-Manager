<?php		
	SESSION_START();
	
	$_SESSION['logged'] = false;
	$_SESSION['user'] = '';
			
	$JSON['ip'] = $_SERVER['REMOTE_ADDR'];
	$JSON['user'] = '';	
	$JSON['logged'] = false;
			
	SESSION_DESTROY();
	SESSION_UNSET();

	$_SESSION = [];
	
	ECHO JSON_ENCODE($JSON);	
	EXIT;
?>