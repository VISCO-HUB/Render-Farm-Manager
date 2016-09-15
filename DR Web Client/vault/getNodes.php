<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
	
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
	
	IF(ISSET($_SERVER['PHP_AUTH_USER']) && IN_ARRAY(HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']), $ALLOWED_USERS))
	{
		$USER = HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']);
		
		mysqliGetNodes($USER, $DATA);
	}
	ELSE
	{
		ECHO '{"message": "RESTRICTED"}';			
	}
	
?>