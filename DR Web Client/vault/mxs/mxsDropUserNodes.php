<?php		
	INCLUDE '../config.php';
	INCLUDE '../functions.php';
	
	$USER = $_GET['user'];
			
	IF(ISSET($USER) && isUserAllow($USER) != -1)
	{
		$USER = HTMLSPECIALCHARS($USER);
		
		ECHO mysqliDropNodes($USER);		
	}
	ELSE
	{
		ECHO 'ERROR';	
	}
	
?>