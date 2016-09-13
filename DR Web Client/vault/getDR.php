<?php		
	INCLUDE 'functions.php';
	
	$QUERY = "SELECT * FROM dr;";
		
	ECHO mysqliGet($QUERY);
	
?>