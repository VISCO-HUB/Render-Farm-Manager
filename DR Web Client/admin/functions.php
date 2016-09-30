<?php 

	FUNCTION mysqliSelect($MYSQLI, $QUERY){		
		$JSON = ARRAY();
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
				$JSON[] = $ROW;							
			}							
		}
	
		RETURN JSON_ENCODE($JSON);
	}

	FUNCTION adminDR($MYSQLI)
	{	
		$QUERY = "SELECT * FROM dr;";
		
		RETURN mysqliSelect($MYSQLI, $QUERY);
	}
	
	FUNCTION adminGetServices($MYSQLI)
	{				
		$QUERY = "SELECT * FROM services;";
		
		RETURN mysqliSelect($MYSQLI, $QUERY);
	}
	
	FUNCTION adminGetUsers($MYSQLI)
	{		
		$QUERY = "SELECT * FROM users;";
		
		RETURN mysqliSelect($MYSQLI, $QUERY);
	}
	
	/*
	FUNCTION getSrv($SERVICES)
	{
		$RUNNINGSRV = '';										
		FOREACH(EXPLODE(';', $SERVICES) AS $V)
		{					
			$P = EXPLODE('=', $V);						
			IF(STRCMP($P[1], '4') === 0) $RUNNINGSRV = $P[0];									
		}
		
		RETURN $RUNNINGSRV;
	}
	
	FUNCTION isIPExist($IP)
	{
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			RETURN FALSE;
		}
		
		$QUERY = "SELECT * FROM dr WHERE ip='" . $IP . "';";
		$RESULT = $MYSQLI->query($QUERY);		
		$COUNT = MYSQLI_NUM_ROWS($RESULT);
		
		$MYSQLI->CLOSE();
		
		RETURN $COUNT == 1;	
	}
	
	FUNCTION mysqliGetDR()
	{
		$JSON = ARRAY();
		
		$MYSQLI = mysqliConnect();
	
		IF($MYSQLI->connect_errno) {
			ECHO '{"message": "ERROR"}';
			RETURN FALSE;
		}
		
		$QUERY = "SELECT * FROM dr WHERE TO_SECONDS(NOW()) - TO_SECONDS(updated) < 10 ;";
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
								
				$ROW->services = getSrv($ROW->services);	
				$JSON[] = $ROW;							
			}							
		}
			
		$MYSQLI->CLOSE();		
		
		RETURN JSON_ENCODE($JSON);
	}
	
	FUNCTION mysqliGetServices()
	{
		$JSON = ARRAY();
		
		$MYSQLI = mysqliConnect();
	
		IF($MYSQLI->connect_errno) {
			ECHO '{"message": "ERROR"}';
			RETURN FALSE;
		}
		
		$QUERY = "SELECT * FROM services;";
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
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
		
		$QUERY = 'UPDATE dr SET user=null, job=null WHERE user="' . $USER . '";';
		$RESULT = $MYSQLI->query($QUERY);
		
		$COUNT = 0;
		$JOB = Strip($DATA->job);
		
		$LAST_USED = IMPLODE('|', $DATA->nodes);
		
		FOREACH ($DATA->nodes as $KEY => $VALUE) {
			$QUERY = 'UPDATE dr SET user="' . $USER . '", job="' . $JOB . '" WHERE ip="' . $VALUE . '" AND user IS NULL;';
			$RESULT = $MYSQLI->query($QUERY);
			
			IF($MYSQLI->affected_rows)
			{			
				$COUNT++;
			}
			
			$OUT = '{"message": "NODESRESERVED", "cnt": ' . $COUNT . '}';
		}
		
		IF($COUNT)
		{
			$QUERY = 'UPDATE users SET lastnodes="' . $LAST_USED. '" WHERE user="' . $USER . '";';
			$RESULT = $MYSQLI->query($QUERY);
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
		$USER = Strip($USER);
		
		$QUERY = 'UPDATE dr SET user=null, job=null WHERE user="' . $USER . '";';
		$RESULT = $MYSQLI->query($QUERY);
						
		$MYSQLI->CLOSE();
		
		ECHO '{"message": "NODESDROPPED"}' ;
	}

	FUNCTION mysqliGetLastNodes($USER)
	{
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO "ERROR";
			RETURN FALSE;
		}
		$USER = Strip($USER);
		
		$QUERY = 'SELECT * FROM users WHERE user="' . $USER . '";';
		$RESULT = $MYSQLI->query($QUERY);						
		$COUNT = MYSQLI_NUM_ROWS($RESULT);
		
		$LAST_USED = '';
		
		IF($COUNT == 1) {
			
			$ROW = $RESULT->fetch_object();			
			$LAST_USED = $ROW->lastnodes;
		}
						
		$MYSQLI->CLOSE();
		
		ECHO $LAST_USED;
	}
	
	FUNCTION mysqliUserNodes($USER)
	{
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO "ERROR";
			RETURN FALSE;
		}
		
		$OUT = "";		
		$QUERY = "SELECT * FROM dr WHERE user='" . $USER . "';";
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
				
				$S = getSrv($ROW->services);
				$S = PREG_REPLACE('/[a-z\s+]/i', '', $S);
				
				$OUT .= $ROW->ip . '-' . $S . ';';							
			}

			IF(EMPTY($OUT)) $OUT = 'NONODES';
		}
						
		$MYSQLI->CLOSE();
		
		ECHO $OUT ;
	}
	
	FUNCTION mysqliCoronaUserNodes($USER)
	{
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO "";
			RETURN FALSE;
		}
		
		$OUT = "";		
		$QUERY = "SELECT * FROM dr WHERE user='" . $USER . "';";
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
							
				$OUT .= $ROW->ip . ';';							
			}

			IF(EMPTY($OUT)) $OUT = 'NONODES';
		}
						
		$MYSQLI->CLOSE();
		
		ECHO $OUT ;
	}
	
	// EXE FUNCTIONS
	FUNCTION exeSetData($DATA)
	{
		IF(!isIPExist($DATA->ip)) 
		{			
			RETURN 'ERROR';
		}
		$IP = HTMLSPECIALCHARS($DATA->ip);
		$NAME = HTMLSPECIALCHARS($DATA->name);	
		$CPU = HTMLSPECIALCHARS($DATA->cpu);
		$SERVICE = HTMLSPECIALCHARS($DATA->service);
		$USER = HTMLSPECIALCHARS($DATA->user);
		
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO 'ERROR';
			RETURN FALSE;
		}
		
		
		$ATTACH = "";
		SWITCH ($USER) {
			CASE 'NO':
				$ATTACH = "";
			BREAK;
			CASE 'CLEAR':
				$ATTACH = "user=null";
			BREAK;
			DEFAULT:
				$ATTACH = "user='" . $USER . "'";
			BREAK;
		}
		
		$QUERY = "UPDATE dr SET cpu='" . $CPU . "', services='" . $SERVICE . "', name='" . $NAME . "' " . $ATTACH ." WHERE ip='" . $IP . "'";		
		$RESULT = $MYSQLI->query($QUERY);		

			
		$MYSQLI->CLOSE();

		RETURN 'OK';
	}
	
	FUNCTION exeDropNode($IP)
	{
		$MYSQLI = mysqliConnect();
				
		IF($MYSQLI->connect_errno) {
			ECHO "ERROR";
			RETURN FALSE;
		}
		
		$IP = HTMLSPECIALCHARS($IP);
				
		$QUERY = 'UPDATE dr SET user=null, job=null WHERE ip="' . $IP . '";';
		$RESULT = $MYSQLI->query($QUERY);
						
		$MYSQLI->CLOSE();
		
		ECHO 'OK' ;
	}
	
	FUNCTION exeGetUser($IP)
	{
		$MYSQLI = mysqliConnect();
				
		IF($MYSQLI->connect_errno) {
			ECHO "ERROR";
			RETURN FALSE;
		}
		
		$IP = HTMLSPECIALCHARS($IP);
		$USER = 'null';
		
		$QUERY = "SELECT * FROM dr WHERE ip='" . $IP . "';";
		$RESULT = $MYSQLI->query($QUERY);						
		$COUNT = MYSQLI_NUM_ROWS($RESULT);
		
		IF($COUNT == 1) {
			
			$ROW = $RESULT->fetch_object();			
			$USER = $ROW->user;
		}
		$MYSQLI->CLOSE();
		
		IF(COUNT($USER) == 0) RETURN 'null';
		RETURN $USER;
	}
	
	FUNCTION exeSetData1($USER, $SERVICE, $CPU, $NAME, $IP)
	{
		IF(!isIPExist($IP)) 
		{			
			RETURN 'ERROR';
		}
		
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO 'ERROR';
			RETURN FALSE;
		}
				
		$ATTACH = "";
		SWITCH ($USER) {
			CASE 'NONE':
				$ATTACH = "";
			BREAK;			
			DEFAULT:
				$ATTACH = "user='" . $USER . "'";
			BREAK;
		}
		
		$QUERY = "UPDATE dr SET cpu='" . $CPU . "', services='" . $SERVICE . "', name='" . $NAME . "' " . $ATTACH .", updated=NOW() WHERE ip='" . $IP . "'";		
		$RESULT = $MYSQLI->query($QUERY);		

			
		$MYSQLI->CLOSE();
		
		RETURN 'OK';
	}
	
	FUNCTION exeInsertData($DATA)
	{		
		$IP = HTMLSPECIALCHARS($DATA->ip);
		$NAME = HTMLSPECIALCHARS($DATA->name);	
				
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO 'ERROR';
			RETURN FALSE;
		}
				
		$QUERY = "INSERT IGNORE INTO dr(name,ip) VALUES('" . $NAME . "', '" . $IP . "');";		
		$RESULT = $MYSQLI->query($QUERY);		
			
		$MYSQLI->CLOSE();

		RETURN 'OK';
	}
	
	FUNCTION exeInsertData1($NAME, $IP)
	{							
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO 'ERROR';
			RETURN FALSE;
		}
				
		$QUERY = "INSERT IGNORE INTO dr(name,ip) VALUES('" . $NAME . "', '" . $IP . "');";		
		$RESULT = $MYSQLI->query($QUERY);		
			
		$MYSQLI->CLOSE();

		RETURN 'OK';
	}
	
	FUNCTION exeGetServices()
	{						
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO 'ERROR';
			RETURN FALSE;
		}
		$S = "";	
		$QUERY = "SELECT * FROM services;";
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
				$S .= $ROW->name . ';';							
			}							
		}
			
		$MYSQLI->CLOSE();

		RETURN $S;
	}	*/
?>