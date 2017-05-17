<?php		
	INCLUDE '../config.php';
	INCLUDE '../functions.php';
	
	$USER = Strip($_GET['user']);
	$SERVICE = Strip($_GET['service']);	
	$CPU = Strip($_GET['cpu']);	
	$NAME = Strip($_GET['name']);	
	$IP = Strip($_GET['ip']);	
	$RAM = Strip($_GET['ram']);	
	$ARAM = Strip($_GET['aram']);	
	$CPUDATA = Strip($_GET['cpudata']);	
	$MAX3D = Strip($_GET['3dsmax']);	
	$CPUNUMBER = Strip($_GET['cpunumber']);	
	IF(!ISSET($_GET['ram'])) $RAM = 0;
	IF(!ISSET($_GET['3dsmax'])) $MAX3D = 0;
	IF(!ISSET($_GET['aram'])) $ARAM = 0;
	IF(!ISSET($_GET['cpudata'])) $CPUDATA = '';
	IF(!ISSET($_GET['cpunumber'])) $CPUNUMBER = 1;
	
	IF(!ISSET($USER) || !ISSET($SERVICE) || !ISSET($CPU) || !ISSET($NAME) || !ISSET($IP)) DIE('ERROR');
		
	ECHO exeSetData1($USER, $SERVICE, $CPU, $NAME, $IP, $RAM, $ARAM, $CPUDATA, $MAX3D, $CPUNUMBER);	
?>