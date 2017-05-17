<?php		
	INCLUDE '../config.php';
	INCLUDE '../functions.php';
	
	$NAME = Strip($_GET['name']);	
	$IP = Strip($_GET['ip']);	
	$RAM = Strip($_GET['ram']);	
	IF(!ISSET($RAM)) $RAM = 0;
	
	IF(!ISSET($NAME) || !ISSET($IP)) DIE('ERROR');
	
	ECHO exeInsertData1($NAME, $IP, $RAM);	
?>