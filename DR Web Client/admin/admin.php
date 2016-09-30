<?php		
	INCLUDE '../vault/config.php';
	INCLUDE '../vault/functions.php';
	INCLUDE 'functions.php';
	SESSION_START();
		
	$ADMIN = isUserAllow($_SESSION['user']);
	$QUERY = $_GET['query'];
		
	IF(ISSET($_SESSION['user']) && $ADMIN == 1 && ISSET($_GET['query']))
	{
		$MYSQLI = mysqliConnect();
	
		IF($MYSQLI->connect_errno) {
			DIE('{"message": "ERROR"}');
		}
		
		SWITCH($QUERY){
			
			CASE 'getDR': ECHO adminDR($MYSQLI);
			BREAK;
			
			CASE 'getServices': ECHO adminGetServices($MYSQLI);
			BREAK;
			
			CASE 'getUsers': ECHO adminGetUsers($MYSQLI);
			BREAK;
			
		}

		$MYSQLI->CLOSE();			
	}
	ELSE
	{
		DIE('{"message": "RESTRICTED"}');	
	}
?>