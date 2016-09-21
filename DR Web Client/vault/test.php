<?php		
	INCLUDE 'config.php';
	INCLUDE 'functions.php';
	
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
	ECHO $DATA->msg;		
?>