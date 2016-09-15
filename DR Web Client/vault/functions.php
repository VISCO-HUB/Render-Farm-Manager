<?php 
	
	
	// FUNCTIONS
	
	FUNCTION mysqliConnect()
	{
		$MYSQL_SERVER = $GLOBALS['MYSQL_SERVER'];
		$MYSQL_USER = $GLOBALS['MYSQL_USER'];
		$MYSQL_PWD = $GLOBALS['MYSQL_PWD'];
		$MYSQL_DB = $GLOBALS['MYSQL_DB'];
		
		
		RETURN NEW MYSQLI($MYSQL_SERVER, $MYSQL_USER, $MYSQL_PWD, $MYSQL_DB);	
	}
	
	FUNCTION getSrv($SERVICES)
	{
		$RUNNINGSRV = '';										
		FOREACH(EXPLODE(';', $SERVICES) AS $V)
		{					
			$P = EXPLODE('=', $V);						
			IF(STRCMP($P[1], '4') === 0) $RUNNINGSRV .= $P[0] . ' ';									
		}
		
		RETURN $RUNNINGSRV;
	}
	
	FUNCTION mysqliGetDR()
	{
		$JSON = ARRAY();
		
		$MYSQLI = mysqliConnect();
	
		IF($MYSQLI->connect_errno) {
			ECHO '{"message": "ERROR"}';
			RETURN FALSE;
		}
		
		$QUERY = "SELECT * FROM dr;";
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
								
				$ROW->services = getSrv($ROW->services);	
				$JSON[] = $ROW;							
			}							
		}
			
		$MYSQLI->CLOSE();		
		
		RETURN JSON_ENCODE($JSON);
	}
	
	FUNCTION mysqliGetUser($USER)
	{
		$OUT = ARRAY();
		
		$MYSQLI = mysqliConnect();
	
		IF($MYSQLI->connect_errno) {
			ECHO '{"message": "ERROR"}';
			RETURN FALSE;
		}
				
		$QUERY = 'SELECT * FROM users WHERE user="' . $USER . '" LIMIT 1;';
		
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
				$OUT[] = $ROW;			
			}	
		}
		
		$MYSQLI->CLOSE();		
		
		RETURN $OUT;
	}
	
	FUNCTION mysqliSetUser($USER, $PW)
	{
		$OUT = ARRAY();
		
		$MYSQLI = mysqliConnect();
	
		IF($MYSQLI->connect_errno) {
			ECHO '{"message": "ERROR"}';
			RETURN FALSE;
		}
				
		$QUERY = 'INSERT INTO users (user, pwd , ip) VALUES ("' . $USER . '", "' . $PW . '", "' . $_SERVER['REMOTE_ADDR'] . '");';
		
		$RESULT = $MYSQLI->query($QUERY);
				
		$MYSQLI->CLOSE();		
		
		RETURN $OUT;
	}
	
	FUNCTION mysqliGetNodes($USER, $DATA)
	{
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO '{"message": "ERROR"}';
			RETURN FALSE;
		}
		
		$OUT = '{"message": "NONODES"}';
		
		$QUERY = 'UPDATE dr SET user=null WHERE user="' . $USER . '";';
		$RESULT = $MYSQLI->query($QUERY);
		
		$COUNT = 0;
		
		FOREACH ($DATA as $KEY => $VALUE) {
			$QUERY = 'UPDATE dr SET user="' . $USER . '" WHERE ip="' . $VALUE . '" AND user IS NULL;';
			$RESULT = $MYSQLI->query($QUERY);
			
			IF($MYSQLI->affected_rows)
			{			
				$COUNT++;
			}
			
			$OUT = '{"message": "NODESRESERVED", "cnt": ' . $COUNT . '}';
		}
				
		$MYSQLI->CLOSE();
		
		ECHO $OUT ;
	}
	
	FUNCTION mysqliDropNodes($USER)
	{
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO "ERROR";
			RETURN FALSE;
		}
						
		$QUERY = 'UPDATE dr SET user=null WHERE user="' . $USER . '";';
		$RESULT = $MYSQLI->query($QUERY);
						
		$MYSQLI->CLOSE();
		
		ECHO '{"message": "NODESDROPPED"}' ;
	}
	
?>