<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
				
	IF(ISSET($_SERVER['PHP_AUTH_USER']) && isUserAllow($_SERVER['PHP_AUTH_USER']) != -1)
	{
		$USER = HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']);
		
		mysqliDropNodes($USER);
	}
	ELSE
	{
		ECHO '{"message": "RESTRICTED"}';	
	}
	
?>