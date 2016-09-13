<?php 
	
	
	// FUNCTIONS
	FUNCTION mysqliGet($QUERY)
	{
		$JSON = ARRAY();
		INCLUDE 'config.php';
	
		$MYSQLI = NEW MYSQLI($MYSQL_SERVER, $MYSQL_USER, $MYSQL_PWD, $MYSQL_DB);
	
		IF($MYSQLI->connect_errno) {
			RETURN ("ERROR");
		}
		
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
				$JSON[] = $ROW;			
			}							
		}

		$RESULT->CLOSE();	
		$MYSQLI->CLOSE();		
		
		RETURN JSON_ENCODE($JSON);
	}
	
	FUNCTION mysqliGetUser($USER)
	{
		$OUT = ARRAY();
		
		$MYSQL_SERVER = $GLOBALS['MYSQL_SERVER'];
		$MYSQL_USER = $GLOBALS['MYSQL_USER'];
		$MYSQL_PWD = $GLOBALS['MYSQL_PWD'];
		$MYSQL_DB = $GLOBALS['MYSQL_DB'];
		
		
		$MYSQLI = NEW MYSQLI($MYSQL_SERVER, $MYSQL_USER, $MYSQL_PWD, $MYSQL_DB);
	
		IF($MYSQLI->connect_errno) {
			RETURN ("ERROR");
		}
				
		$QUERY = 'SELECT * FROM users WHERE user="' . $USER . '" LIMIT 1;';
		
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
				$OUT[] = $ROW;			
			}	
		}

		//$RESULT->CLOSE();	
		$MYSQLI->CLOSE();		
		
		RETURN $OUT;
	}
	
	FUNCTION mysqliSetUser($USER, $PW)
	{
		$OUT = ARRAY();
		
		$MYSQL_SERVER = $GLOBALS['MYSQL_SERVER'];
		$MYSQL_USER = $GLOBALS['MYSQL_USER'];
		$MYSQL_PWD = $GLOBALS['MYSQL_PWD'];
		$MYSQL_DB = $GLOBALS['MYSQL_DB'];
		
		
		$MYSQLI = NEW MYSQLI($MYSQL_SERVER, $MYSQL_USER, $MYSQL_PWD, $MYSQL_DB);
	
		IF($MYSQLI->connect_errno) {
			RETURN ("ERROR");
		}
				
		$QUERY = 'INSERT INTO users (user, pwd , ip) VALUES ("' . $USER . '", "' . $PW . '", "' . $_SERVER['REMOTE_ADDR'] . '");';
		
		$RESULT = $MYSQLI->query($QUERY);
		
		//$RESULT->CLOSE();	
		$MYSQLI->CLOSE();		
		
		RETURN $OUT;
	}
	
	
?>