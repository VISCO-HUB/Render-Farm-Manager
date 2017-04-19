<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
	
	SESSION_START();
	
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
	
	IF(!ISSET($DATA)) DIE('ERROR');			
	
	$ADMIN = isUserAllow($_SESSION['user']);
	
	IF(ISSET($_SESSION['user']) && $ADMIN == 1)
	{	
		kickSelectedNodes($DATA);
	}
	ELSE
	{
		ECHO '{"message": "RESTRICTED"}';	
	}
	
?>