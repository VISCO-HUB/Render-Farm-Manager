<?php		
	INCLUDE '../config.php';
	INCLUDE '../functions.php';
	
	$USER = Strip($_GET['user']);
	$SERVICE = Strip($_GET['service']);	
	$CPU = Strip($_GET['cpu']);	
	$NAME = Strip($_GET['name']);	
	$IP = Strip($_GET['ip']);	
	
	IF(!ISSET($USER) || !ISSET($SERVICE) || !ISSET($CPU) || !ISSET($NAME) || !ISSET($IP)) DIE('ERROR');
		
	ECHO exeSetData1($USER, $SERVICE, $CPU, $NAME, $IP);	
?>