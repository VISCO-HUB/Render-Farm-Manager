<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
	
	SESSION_START();
	
	IF(ISSET($_SESSION['user']) && isUserAllow($_SESSION['user']) != -1)
	{
		$USER = HTMLSPECIALCHARS($_SESSION['user']);
		
		mysqliGetLastNodes($USER);
	}
	ELSE
	{
		ECHO '{"message": "RESTRICTED"}';	
	}
	
?>