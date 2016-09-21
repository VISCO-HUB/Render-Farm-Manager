<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
				
	IF(ISSET($_SERVER['PHP_AUTH_USER']) && IN_ARRAY(HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']), $ALLOWED_USERS))
	{
		$USER = HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']);
		
		mysqliDropNodes($USER);
	}
	ELSE
	{
		ECHO '{"message": "RESTRICTED"}';	
	}
	
?>