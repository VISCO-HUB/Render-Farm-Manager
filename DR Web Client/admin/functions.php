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
		$USERS = ARRAY_UNIQUE($DATA->users);
		
		$ATTACH = '';
		
		SWITCH($NOTIFY) {
			CASE 1: $ATTACH = 'WHERE rights=0';
			BREAK;
			CASE 2: $ATTACH = 'WHERE rights=1';
			BREAK;
			CASE 3: {
				IF(COUNT($USERS)) {
					$TMP = [];
					FOREACH($USERS AS $U) IF(!EMPTY($U)) $TMP[] = 'user="' . $U . '"';
										
					IF(!COUNT($TMP)) RETURN $ERROR;

					$ATTACH = 'WHERE ' . IMPLODE('OR', $TMP);						
				}
			}
			BREAK;
			DEFAULT: RETURN $ERROR;
			BREAK;
		}
		
		
		$QUERY = "SELECT * FROM users " . $ATTACH . ";";
		
		$USERS = mysqliSelect($MYSQLI, $QUERY);
		
		$EMAILS = []; 
		
		FOREACH($USERS AS $USER) $EMAILS[] = $USER->user . "@visco.no";
			
		$HEADERS   = [];
		$HEADERS[] = "MIME-Version: 1.0";
		$HEADERS[] = "Content-type: text/plain; charset=iso-8859-1";
		$HEADERS[] = "From: RenderFarmManager@viscocg.com";
		$HEADERS[] = "Reply-To: " . IMPLODE(',', $EMAILS); 
		$HEADERS[] = "Subject: " . $SUBJECT;
		$HEADERS[] = "X-Mailer: PHP/" . PHPVERSION();
			
		IF(!COUNT($EMAILS)) RETURN $ERROR;
		
		$MESSAGE = WORDWRAP($CONTENT, 70, "\r\n");
		$SEND = MAIL(IMPLODE(',', $EMAILS), $SUBJECT , $MESSAGE, IMPLODE("\r\n", $HEADERS)); 
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

?>