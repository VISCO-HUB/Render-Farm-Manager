<?php 

	///////////////////////////////////////////////////////
	// MYSQLI BASIC FUNCTIONS
	///////////////////////////////////////////////////////

	// SELECT
	FUNCTION mysqliSelect($MYSQLI, $QUERY){		
		$JSON = ARRAY();
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
				$JSON[] = $ROW;							
			}							
		}
	
		RETURN $JSON;
	}
	
	// INSERT
	FUNCTION mysqliInsert($MYSQLI, $TABLE, $DATA)
	{	
		$COLS = [];
		$VALUES = [];
		FOREACH($DATA AS $KEY => $VALUE)
		{
			$COLS[] = $KEY;						
			$VALUES[] = $VALUE == null ? 'NULL' : "'" .$VALUE . "'";
		}
			
		$QUERY = "INSERT IGNORE INTO " . $TABLE . "(" . IMPLODE(',', $COLS) . ") VALUES(" . IMPLODE(',', $VALUES) . ");";				
		$RESULT = $MYSQLI->query($QUERY);		
			
		$MYSQLI->CLOSE();

		RETURN $RESULT;
	}

	// DELETE
	FUNCTION mysqliDelete($MYSQLI, $TABLE, $DATA, $PARAM)
	{	
		$VALUES = [];
		FOREACH($DATA AS $VALUE)
		{
			$VALUES[] = $PARAM . "='" .$VALUE . "'";
		}
			
		$QUERY = "DELETE FROM " . $TABLE . " WHERE " . IMPLODE(' OR ', $VALUES) . ";";						
		$RESULT = $MYSQLI->query($QUERY);		
			
		$MYSQLI->CLOSE();

		RETURN $RESULT;
	}
	
	// UPDATE
	FUNCTION mysqliUpdate($MYSQLI, $TABLE, $SET, $WHERE)
	{	
		$V = [];
		FOREACH($SET AS $VALUE)
		{				
			$KEY = ARRAY_KEYS($VALUE)[0];
			
			$V[] = $KEY . "=" . "'" .$VALUE[$KEY] . "'";
		}
		
		$W = [];
		FOREACH($WHERE AS $VALUE)
		{				
			$KEY = ARRAY_KEYS($VALUE)[0];
			
			$W[] = $KEY . "=" . "'" .$VALUE[$KEY] . "'";
		}
			
		$QUERY = "UPDATE " . $TABLE . " SET " . IMPLODE(',', $V) . " WHERE " . IMPLODE(' OR ', $W) . ";";						
		
		$RESULT = $MYSQLI->query($QUERY);		
			
		$MYSQLI->CLOSE();

		RETURN $RESULT;
	}
	
	///////////////////////////////////////////////////////
	// GLOBAL
	///////////////////////////////////////////////////////
	
	FUNCTION adminGlobal($MYSQLI)
	{	
		$QUERY = "SELECT * FROM global;";
		
		RETURN JSON_ENCODE(mysqliSelect($MYSQLI, $QUERY));
	}
	
	FUNCTION adminGlobalChangeParam($MYSQLI, $DATA)
	{
		$ERROR = '{"message": "ADMINGLOBALBAD"}';
		$SUCCESS = '{"message": "ADMINGLOBALOK"}';
		
		IF(!ISSET($DATA->name) OR !ISSET($DATA->value)) RETURN $ERROR;
		
		$V = Strip($DATA->value);
		$N = Strip($DATA->name);
				
		$WHERE = [];
		$SET = [];
								
		$SET[] = ['value' => $V];
		$WHERE[] = ['name' => $N];
						
		$RESULT = mysqliUpdate($MYSQLI, 'global', $SET, $WHERE);
		
		IF(!$RESULT) RETURN $ERROR;
		RETURN $SUCCESS;
	}
	
	FUNCTION adminSendEmail($MYSQLI, $DATA)
	{
		$ERROR = '{"message": "EMAILERROR"}';
		$SUCCESS = '{"message": "EMAILOK"}';
		
		IF(!ISSET($DATA->content) || $DATA->content == '' || !ISSET($DATA->subject) || !ISSET($DATA->notify)) RETURN $ERROR;
		$SUBJECT = $DATA->subject;
		$CONTENT = $DATA->content;
		$NOTIFY = $DATA->notify;
		
		$ATTACH = '';
		IF($NOTIFY == 1) $ATTACH = 'WHERE rights=0';
		IF($NOTIFY == 2) $ATTACH = 'WHERE rights=1';
		
		$QUERY = "SELECT * FROM users " . $ATTACH . ";";
		
		$USERS = mysqliSelect($MYSQLI, $QUERY);
		
		$U = []; 
		
		FOREACH($USERS AS $USER) $U[] = $USER->user . "@visco.no";
			
		$HEADERS   = [];
		$HEADERS[] = "MIME-Version: 1.0";
		$HEADERS[] = "Content-type: text/plain; charset=iso-8859-1";
		$HEADERS[] = "From: RenderFarmManager@viscocg.com";
		$HEADERS[] = "Reply-To: " . IMPLODE(',', $U); 
		$HEADERS[] = "Subject: " . $SUBJECT;
		$HEADERS[] = "X-Mailer: PHP/" . PHPVERSION();
		
		$MESSAGE = WORDWRAP($CONTENT, 70, "\r\n");
		$SEND = MAIL(IMPLODE(',', $U), $SUBJECT , $MESSAGE, IMPLODE("\r\n", $HEADERS)); 
		//$SEND = MAIL('v.lukyanenko@visco.no' , $MESSAGE, IMPLODE("\r\n", $HEADERS)); 
		//$SEND = MAIL('lukuanenko@gmail.com', 'oi' ,'ny je'); 
		IF(!$SEND) RETURN $ERROR;
		RETURN $SUCCESS;
	}
	
	///////////////////////////////////////////////////////
	// NODES
	///////////////////////////////////////////////////////
	
	FUNCTION adminDR($MYSQLI)
	{	
		$QUERY = "SELECT * FROM dr;";
		
		RETURN JSON_ENCODE(mysqliSelect($MYSQLI, $QUERY));
	}
	
	FUNCTION adminNodesDisable($MYSQLI, $DATA)
	{
		$ERROR = '{"message": "ADMINSTATUSBAD"}'; 
		$SUCCESS = '{"message": "ADMINSTATUSOK"}';
		
		IF(!ISSET($DATA->ip) OR !ISSET($DATA->status)) RETURN $ERROR;
			
		$STATUS = 0;		
		
		IF($DATA->status == true) $STATUS = 1;
				
		$WHERE =[];
		$SET = [];
		
		FOREACH($DATA->ip AS $VALUE){
						
			$SET[] = ['status' => $STATUS];
			$WHERE[] = ['ip' => Strip($VALUE)];
		}
				
		$RESULT = mysqliUpdate($MYSQLI, 'dr', $SET, $WHERE);
		
		IF(!$RESULT) RETURN $ERROR;
		RETURN $SUCCESS;
	}
	
	FUNCTION adminNodeDelete($MYSQLI, $DATA)
	{		
		$ERROR = '{"message": "ADMINDELETEBAD"}';
		$SUCCESS = '{"message": "ADMINDELETEOK"}';
		
		IF(!ISSET($DATA->ip)) RETURN $ERROR;
		$DR =[];
		
		FOREACH($DATA->ip AS $VALUE)
		{
			$DR[] = Strip($VALUE);			
		}
		
		$RESULT = mysqliDelete($MYSQLI, 'dr', $DR, 'ip');
		
		IF(!$RESULT) RETURN $ERROR;
		RETURN $SUCCESS;
	}
	
	FUNCTION adminSendCmd($DATA)
	{
		$ERROR = '{"message": "NOTSEND"}';
		$SUCCESS = '{"message": "SEND"}';
		
		$IP = $DATA->ip;
		$CMD = $DATA->cmd;
		$PORT = $GLOBALS['PORT'];
		
		$TCP = 'tcp://' . $IP;
		
		$SOCKET = FSOCKOPEN($TCP,$PORT,$ERRNO, $ERRSTR, 1);

		IF($SOCKET)
		{
			FPUTS($SOCKET, $CMD);
			ECHO FGETS($SOCKET, 255);
			FCLOSE($SOCKET);
			
			RETURN $SUCCESS;
		}
		
		RETURN $ERROR;
	}
	
	///////////////////////////////////////////////////////
	// USERS
	///////////////////////////////////////////////////////
	
	FUNCTION adminGetUsers($MYSQLI)
	{		
		$QUERY = "SELECT * FROM users;";
		
		RETURN JSON_ENCODE(mysqliSelect($MYSQLI, $QUERY));
	}
	
	FUNCTION adminAddUser($MYSQLI, $DATA)
	{		
		$ERROR = '{"message": "ADMINUSERNOTADDED"}';
		$SUCCESS = '{"message": "ADMINUSERADDED"}';
		
		IF(!ISSET($DATA->user)) RETURN $ERROR;
		$USER = Strip($DATA->user);
		
		$VALUES = ['user' => $USER, 'pwd' => ''];			
		$RESULT = mysqliInsert($MYSQLI, 'users', $VALUES);
		
		IF(!$RESULT) RETURN $ERROR;
		RETURN $SUCCESS;
	}
	
	FUNCTION adminDeleteUsers($MYSQLI, $DATA)
	{		
		$ERROR = '{"message": "ADMINUSERNOTDELETED"}';
		$SUCCESS = '{"message": "ADMINUSERDELETED"}';
		
		IF(!ISSET($DATA->users)) RETURN $ERROR;
		$USERS =[];
		
		FOREACH($DATA->users AS $VALUE)
		{
			$USERS[] = Strip($VALUE);			
		}
					
		$RESULT = mysqliDelete($MYSQLI, 'users', $USERS, 'user');
		
		IF(!$RESULT) RETURN $ERROR;
		RETURN $SUCCESS;
	}
	
	FUNCTION adminChangeAccess($MYSQLI, $DATA)
	{
		$ERROR = '{"message": "ADMINACCESSNOTCHANGED"}';
		$SUCCESS = '{"message": "ADMINACCESSCHANGED"}';
		
		IF(!ISSET($DATA->users) OR !ISSET($DATA->access)) RETURN $ERROR;
				
		$ACCESS = 0;		
		
		IF($DATA->access == '#admin') $ACCESS = 1;
				
		$WHERE =[];
		$SET = [];
		
		FOREACH($DATA->users AS $VALUE){
						
			$SET[] = ['rights' => $ACCESS];
			$WHERE[] = ['user' => Strip($VALUE)];
		}
				
		$RESULT = mysqliUpdate($MYSQLI, 'users', $SET, $WHERE);
		
		IF(!$RESULT) RETURN $ERROR;
		RETURN $SUCCESS;		
	}
		
	
	FUNCTION adminChangePassword($MYSQLI, $DATA)
	{
		$ERROR = '{"message": "ADMINNOCHANGEPASSWORD"}';
		$SUCCESS = '{"message": "ADMINCHANGEPASSWORD"}';
		
		IF(!ISSET($DATA->users) OR !ISSET($DATA->pwd)) RETURN $ERROR;
		
		$WHERE =[];
		$SET = [];
		
		FOREACH($DATA->users AS $VALUE){
						
			$SET[] = ['pwd' => $DATA->pwd == '' ? '' : MD5($DATA->pwd)];
			$WHERE[] = ['user' => Strip($VALUE)];
		}
		
		$RESULT = mysqliUpdate($MYSQLI, 'users', $SET, $WHERE);
		
		IF(!$RESULT) RETURN $ERROR;
		RETURN $SUCCESS;
	}
	
	FUNCTION adminServiceDisable($MYSQLI, $DATA)
	{
		$ERROR = '{"message": "ADMINSTATUSBAD"}'; 
		$SUCCESS = '{"message": "ADMINSTATUSOK"}';
		
		IF(!ISSET($DATA->names) OR !ISSET($DATA->status)) RETURN $ERROR;
			
		$STATUS = 0;		
		
		IF($DATA->status == true) $STATUS = 1;
				
		$WHERE =[];
		$SET = [];
		
		FOREACH($DATA->names AS $VALUE){
						
			$SET[] = ['status' => $STATUS];
			$WHERE[] = ['name' => Strip($VALUE)];
		}
				
		$RESULT = mysqliUpdate($MYSQLI, 'services', $SET, $WHERE);
		
		IF(!$RESULT) RETURN $ERROR;
		RETURN $SUCCESS;
	}
	
	
	///////////////////////////////////////////////////////
	// SERVICES
	///////////////////////////////////////////////////////
	
	FUNCTION adminGetServices($MYSQLI)
	{				
		$QUERY = "SELECT * FROM services;";
		
		RETURN JSON_ENCODE(mysqliSelect($MYSQLI, $QUERY));
	}
	
	FUNCTION adminServiceAdd($MYSQLI, $DATA)
	{		
		$ERROR = '{"message": "ADMINSERVICENOTADDED"}';
		$SUCCESS = '{"message": "ADMINSERVICEADDED"}';
		
		IF(!ISSET($DATA->name)) RETURN $ERROR;
		$SERVICE = Strip($DATA->name);
		
		$VALUES = ['name' => $SERVICE, 'status' => '0'];			
		$RESULT = mysqliInsert($MYSQLI, 'services', $VALUES);
		
		IF(!$RESULT) RETURN $ERROR;
		RETURN $SUCCESS;
	}
	
	FUNCTION adminServiceDelete($MYSQLI, $DATA)
	{		
		$ERROR = '{"message": "ADMINDELETEBAD"}';
		$SUCCESS = '{"message": "ADMINDELETEOK"}';
		
		IF(!ISSET($DATA->names)) RETURN $ERROR;
		$SERVICES =[];
		
		FOREACH($DATA->names AS $VALUE)
		{
			$SERVICES[] = Strip($VALUE);			
		}
		
		$RESULT = mysqliDelete($MYSQLI, 'services', $SERVICES, 'name');
		
		IF(!$RESULT) RETURN $ERROR;
		RETURN $SUCCESS;
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