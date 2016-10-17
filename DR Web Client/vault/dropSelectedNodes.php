<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
				
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
	
	IF(ISSET($_SERVER['PHP_AUTH_USER']) && isUserAllow($_SERVER['PHP_AUTH_USER']) != -1)
	{
		$USER = HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']);
		
		mysqliDropSelectedNodes($USER, $DATA);
	}
	ELSE
	{
		ECHO '{"message": "RESTRICTED"}';			
	}
	
?>