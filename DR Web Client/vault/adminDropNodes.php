<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
				
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
	
	IF(!ISSET($DATA)) DIE('ERROR');			
	
	$ADMIN = isUserAllow($_SERVER['PHP_AUTH_USER']);
	
	IF(ISSET($_SERVER['PHP_AUTH_USER']) && $ADMIN == 1)
	{
		$USER = $DATA->user;
		
		mysqliDropNodes($USER);
	}
	ELSE
	{
		ECHO '{"message": "RESTRICTED"}';	
	}
	
?>