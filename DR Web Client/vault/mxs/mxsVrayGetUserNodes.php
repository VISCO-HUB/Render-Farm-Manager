<?php		
	INCLUDE '../config.php';
	INCLUDE '../functions.php';
	
	$USER = $_GET['user'];
	
	IF(ISSET($USER) && isUserAllow($USER) != -1)
	{
		$USER = HTMLSPECIALCHARS($USER);
		
		// Set true because "problem client disconnected"
		ECHO mysqliUserNodes($USER, TRUE);		
	}
	ELSE
	{
		ECHO 'ERROR';	
	}
	
?>