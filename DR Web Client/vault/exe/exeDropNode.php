<?php		
	INCLUDE '../config.php';
	INCLUDE '../functions.php';
	
	$IP = $_GET['ip'];
			
	ECHO exeDropNode($IP);		
		
?>