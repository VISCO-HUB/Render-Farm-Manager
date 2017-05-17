<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
		
	SESSION_START();
		
	$USER = NULL;	
		
	IF(ISSET($_SESSION['user']) && isUserAllow($_SESSION['user']) != -1)
	{
		$USER = HTMLSPECIALCHARS($_SESSION['user']);
	}
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
		
	ECHO mysqliGetNodeInfo($USER, $DATA);
	
?>