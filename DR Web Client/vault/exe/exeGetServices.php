<?php		
	INCLUDE '../config.php';
	INCLUDE '../functions.php';
	
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
	
	IF(!ISSET($DATA)) DIE('ERROR');
		
	ECHO exeGetServices($DATA);	
?>