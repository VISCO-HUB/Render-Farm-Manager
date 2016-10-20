<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
	
	SESSION_START();
	
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
	
	IF(ISSET($_SESSION['user']) && isUserAllow($_SESSION['user']) != -1)
	{
		$USER = HTMLSPECIALCHARS($_SESSION['user']);
		
		mysqliDropSelectedNodes($USER, $DATA);
	}
	ELSE
	{
		ECHO '{"message": "RESTRICTED"}';			
	}
	
?>