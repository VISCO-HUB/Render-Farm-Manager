<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
				
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
	
	IF(!ISSET($DATA)) DIE('ERROR');			
				
	IF(ISSET($_SERVER['PHP_AUTH_USER']) && 
	IN_ARRAY(HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']), $ALLOWED_USERS) &&
	IN_ARRAY(HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']), $SUPER_USERS))
	{
		$USER = $DATA->user;
		
		mysqliDropNodes($USER);
	}
	ELSE
	{
		ECHO '{"message": "RESTRICTED"}';	
	}
	
?>