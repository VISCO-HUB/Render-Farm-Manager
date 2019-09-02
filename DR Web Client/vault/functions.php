<?php 
	
	FUNCTION GET_BROWSER_NAME()
	{
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
		elseif (strpos($user_agent, 'Edge')) return 'IE';
		elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
		elseif (strpos($user_agent, 'Safari')) return 'Safari';
		elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
		elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'IE';
		
		return 'MXS';
	}
	
	// WEB FUNCTIONS
	FUNCTION Strip($S)
	{
		$S = STR_REPLACE('"', "", $S);
		$S = HTMLSPECIALCHARS($S);
		$S = PREG_QUOTE($S);
		RETURN $S;
	}
	
	FUNCTION IMPLODE_KEY($GLUE = '', $PIECES = ARRAY())
	{
		$KEYS = ARRAY_KEYS($PIECES);
		RETURN IMPLODE($GLUE, $KEYS);
	}
	
	FUNCTION IMPLODE_DATA($GLUE = '', $PIECES = ARRAY()){
		$KEYS = [];
		FOREACH($PIECES AS $K) $KEYS[] = "'" . $K . "'";
		RETURN IMPLODE($GLUE, $KEYS);
	}
	
	FUNCTION IMPLODE_VAR($GLUE = '', $PIECES = ARRAY()){
		$KEYS = [];
		FOREACH($PIECES AS $K => $V) $KEYS[] = $K . "='" . $V . "'";
		RETURN IMPLODE($GLUE, $KEYS);
	}
	
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
			IF(STRCMP($P[1], '4') === 0) $RUNNINGSRV = $P[0];									
		}
		
		RETURN $RUNNINGSRV;
	}
	
	FUNCTION getInstalledSrv($SERVICES)
	{
		$OUT = [];										
		FOREACH(EXPLODE(';', $SERVICES) AS $V)
		{					
			$P = EXPLODE('=', $V);						
			IF(STRCMP($P[1], 'notfound') !== 0 AND $P[0]) $OUT[] = $P[0];									
		}
		
		RETURN IMPLODE(', ', $OUT);
	}
	
	FUNCTION isUserAllow($USER)
	{
		$USER = Strip($USER);
		
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			RETURN FALSE;
		}
		
		$QUERY = "SELECT * FROM users WHERE user='" . $USER . "';";
		$RESULT = $MYSQLI->query($QUERY);		
		$COUNT = MYSQLI_NUM_ROWS($RESULT);		
		$MYSQLI->CLOSE();
		
		IF($COUNT != 1) RETURN -1;
		
		$ROW = $RESULT->fetch_object();					
		RETURN $ROW;
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
	
	FUNCTION toFloat($V) {
		$V = STR_REPLACE(',', '.', $V);	
		$V = ROUND(FLOATVAL($V), 1);
		IF(!STRPOS($V, '.')) $V .= '.0';
			
		RETURN $V;
	}
	
	FUNCTION getRAM($RAM, $ARAM) {
		RETURN $RAM ? (toFloat(toFloat($RAM) - toFloat($ARAM))) . '/' . toFloat($RAM) . ' GB' : 'Unknown';
	}
	
	FUNCTION getRamUsage($RAM, $ARAM) {
		IF(!$RAM) RETURN -1;
		RETURN ROUND((toFloat($RAM) - toFloat($ARAM)) / toFloat($RAM) * 100.0, 2);
	}
	
	FUNCTION mysqliGetNodeInfo($USER, $DATA) {
		IF(!ISSET($DATA->ip)) RETURN '{}';
		$IP = Strip($DATA->ip);
		
		$MYSQLI = mysqliConnect();
		
		$QUERY = "SELECT *  FROM dr WHERE ip='" . $IP . "';";
		$RESULT = $MYSQLI->query($QUERY);
		$RAW = $RESULT->fetch_object();
		$RAW->installedsrv = getInstalledSrv($RAW->services);
		$RAW ->services = getSrv($RAW->services);
		$RAW->ramusage = getRamUsage($RAW->ram, $RAW->aram);
		$RAW->ram = getRAM($RAW->ram, $RAW->aram);	
		$RAW->cpu = $RAW->cpu > 97 ? 100 : $RAW->cpu;		
		
		
		RETURN JSON_ENCODE($RAW);
	}
	
	FUNCTION formatByPeriod($PERIOD1, $PERIOD2){
			
		$PERIOD = ABS((STRTOTIME($PERIOD1) - STRTOTIME($PERIOD2))/ 3600);
		
		IF($PERIOD >  (365 * 24)) {
			RETURN 'Y';
		}
		ELSE IF($PERIOD >  (32 * 24)) {
			RETURN 'M';
		} /*ELSE IF ($PERIOD > (7 * 24)){
			RETURN 'W (Y)';
		}*/
		
		RETURN 'd (D)';
	}
	
	FUNCTION getRenderTime($DATA, $MYSQLI) {
		$RENDER_TIME = ARRAY();
		$CURRENT_DATE = DATE('Y-m-d', TIME());
		$PERIOD = $CURRENT_DATE;
		$OFFICE_FILTER = Strip($DATA->office);
		
		$ATTACH_OFFICE = '';
		
		SWITCH($OFFICE_FILTER) {
			CASE 'All': {}
			BREAK;
			CASE 'Unsorted': {
				$ATTACH_OFFICE = " AND (NULLIF(`office`, '') IS NULL)";
			}
			BREAK;
			DEFAULT: {
				$ATTACH_OFFICE = " AND office='" . $OFFICE_FILTER . "'";
			}
		}
		
				
		$JSON = ARRAY();
		$FORMAT = 'Y-m-d';
		$EMPLOYMENT = ARRAY();
			
		SWITCH($DATA->period) {
			CASE 'Custom': {
				IF(!ISSET($DATA->dt->from)) $DATA->dt->from = $CURRENT_DATE;
				IF(!ISSET($DATA->dt->to)) $DATA->dt->to = DATE('Y-m-d', STRTOTIME($CURRENT_DATE .' -1 months'));
				$DATA->dt->from = DATE('Y-m-d', STRTOTIME($DATA->dt->from));
				$DATA->dt->to = DATE('Y-m-d', STRTOTIME($DATA->dt->to));
				
				$QUERY = "SELECT *  FROM statistic_nodeusage WHERE `date` between date('" . $DATA->dt->from . "') AND date('" . $DATA->dt->to . "')" . $ATTACH_OFFICE . ";";
				$RESULT = $MYSQLI->query($QUERY);
											
				$FORMAT = formatByPeriod($DATA->dt->from, $DATA->dt->to);
			}
			BREAK;
			CASE 'Month': {
				$PERIOD = STRTOTIME($CURRENT_DATE.' -1 months');
				$QUERY = "SELECT *  FROM statistic_nodeusage WHERE updated>=" . $PERIOD . $ATTACH_OFFICE . ";";
				$FORMAT = 'W (Y)';
			}
			BREAK;
			CASE 'Year': {
				$PERIOD = STRTOTIME($CURRENT_DATE .' -1 years');
				$QUERY = "SELECT *  FROM statistic_nodeusage WHERE updated>=" . $PERIOD . $ATTACH_OFFICE . ";";
				$FORMAT = 'M';
			}
			BREAK;
			DEFAULT: {
				$PERIOD = STRTOTIME($CURRENT_DATE .' -1 weeks');
				$QUERY = "SELECT *  FROM statistic_nodeusage WHERE updated>=" . $PERIOD . $ATTACH_OFFICE . ";";
				$RESULT = $MYSQLI->query($QUERY);
				$FORMAT = 'd (D)';
			}
			BREAK;
		}
			
		$RESULT = $MYSQLI->query($QUERY);
		$EMPL = ARRAY();
		$EMPL_CNT = ARRAY();
		$EMPL_NODE_CNT = ARRAY();
		
		$TOTAL_POWER_REND = 0;
		$TOTAL_POWER_IDLE = 0;
		$NODES_CNT = 0;
		
		WHILE($ROW = $RESULT->fetch_object()){
			
			$TOTAL_POWER_REND += $ROW->rendkw;
			$TOTAL_POWER_IDLE += $ROW->idlekw;
			$NODES_CNT++;
			
			$EMPL[$ROW->name] += $ROW->totaltime;
			$EMPL_CNT[$ROW->date] = $ROW->name;
			$EMPL_NODE_CNT[$ROW->name]++;
						
			$KEY = DATE($FORMAT, STRTOTIME($ROW->date));			
			IF($ROW->updated) $RENDER_TIME[$KEY] += $ROW->totaltime;		
		}
			
		FOREACH($RENDER_TIME AS $K=>$V) {
			$JSON['data'][] = ROUND($V / 3600);
			$JSON['label'][] = $K;
		}
		
		$REND_BY_PERIOD = 0;
		$IDLE_BY_PERIOD = 0;
		$REND_BY_PERIOD_P = 0;
		$IDLE_BY_PERIOD_P = 0;
		
		$REND_BY_PERIOD = ROUND(ARRAY_SUM($EMPL) / 3600);
		$IDLE_BY_PERIOD = (COUNT($EMPL_CNT) * COUNT($EMPL) * 24) - $REND_BY_PERIOD;
		
		$TOTAL_REND_PERIOD = ($REND_BY_PERIOD + $IDLE_BY_PERIOD);
		IF($TOTAL_REND_PERIOD) {
			$REND_BY_PERIOD_P = ROUND($REND_BY_PERIOD / $TOTAL_REND_PERIOD  * 100.0);
			$IDLE_BY_PERIOD_P = ROUND(100.0 - $REND_BY_PERIOD_P);
		}
		
		
		$JSON['empl']['data'] = ARRAY();		
		$JSON['empl']['label'] = ARRAY();
				
		$JSON['power']['data'] = ARRAY();
		$JSON['power']['label'] = ARRAY();
		
		$JSON['bynode']['data'] = ARRAY();
		$JSON['bynode']['label'] = ARRAY();
		
		$JSON['empl']['data'][] = $REND_BY_PERIOD;
		$JSON['empl']['data'][] = $IDLE_BY_PERIOD;
		$JSON['empl']['label'][] = 'Render Time (' . $REND_BY_PERIOD_P . '%)';
		$JSON['empl']['label'][] = 'Idle Time (' . $IDLE_BY_PERIOD_P . '%)';
				
		$JSON['power']['data'][] = $NODES_CNT ? ROUND(($REND_BY_PERIOD * ($TOTAL_POWER_REND / $NODES_CNT)) / 1000, 1) : 0;
		$JSON['power']['data'][] = $NODES_CNT ? ROUND(($IDLE_BY_PERIOD * ($TOTAL_POWER_IDLE / $NODES_CNT)) / 1000, 1) : 0;
		$JSON['power']['label'][] = 'Rendering';
		$JSON['power']['label'][] = 'Idle';
		
		
		ASORT($EMPL, SORT_NUMERIC);
		$EMPL = ARRAY_REVERSE($EMPL, TRUE);
		
		FOREACH($EMPL AS $K=>$V) {
			$REND = ROUND($V / 3600);
			$D = $EMPL_NODE_CNT[$K];
			
			$IDLE = (24 * $D) - $REND;
			
			$TMP['rend'] = $REND;
			$TMP['idle'] = $IDLE;
			$TMP['rend_p'] = ROUND($REND / ($REND + $IDLE) * 100.0);
			$TMP['idle_p'] = 100 - $TMP['rend_p'];
			$TMP['name'] = $K;
			
			$JSON['bynode'][] = $TMP;
		}
			
		
		
		IF(!COUNT($RENDER_TIME)) {
			$JSON['data'][] = 0;
			$JSON['label'][] = 'No Data';
		}
		
		$JSON['from'] = $DATA->dt->from;
		$JSON['to'] = $DATA->dt->to;
		
		RETURN $JSON;
	}
		
	FUNCTION mysqliStatistic($USER, $DATA)
	{				
		$CPU_DETECT = 45;
		
		$JSON = ARRAY();
		$DR = ARRAY();
		$MYSQLI = mysqliConnect();
		$USER = Strip($USER);
		
		$NOW_REND = 0;
		$OFFICES = ARRAY();
		$GROUPS = ARRAY();
		$USERS = ARRAY();
		$TOPUSER = ARRAY();
		$REND_BY_USER = ARRAY();
		$REND_BY_OFFICE = ARRAY();
		$NODES_BY_OFFICE = ARRAY();
		$EFFICIENCY['used'] = 0;
		$EFFICIENCY['unused'] = 0;
		$EFFICIENCY['total'] = 0;
		$EFFICIENCY['percent'] = 0;
						
		$JSON['rendertime'] = getRenderTime($DATA, $MYSQLI);
				
		$QUERY = "SELECT *  FROM dr WHERE status=0 AND (TO_SECONDS(NOW()) - TO_SECONDS(updated) < " . $CPU_DETECT  . ");";
		$RESULT = $MYSQLI->query($QUERY);
		WHILE($ROW = $RESULT->fetch_object()){
			$DR[] = $ROW;
			$U = Strip($ROW->user);
			IF($U) $REND_BY_USER[$U]++; ELSE IF($ROW->cpu > $CPU_DETECT) $REND_BY_USER['Backburner']++; ELSE $REND_BY_USER['Free']++;		
			IF($U == $USER) {
				$EFFICIENCY['total']++;
				IF($ROW->cpu > $CPU_DETECT) $EFFICIENCY['used']++; ELSE $EFFICIENCY['unused']++;
			}
			
			IF($ROW->cpu > $CPU_DETECT) $NOW_REND++;
			IF($ROW->office) $OFFICES[] = $ROW->office;
			IF($ROW->group) $GROUPS[] = $ROW->group;
			IF($U) $USERS[] = $U;
			IF($U) $TOPUSER[$ROW->user]++;
			IF($ROW->office) {
				$NODES_BY_OFFICE[$ROW->office]++;
				IF($ROW->cpu > $CPU_DETECT) $REND_BY_OFFICE[$ROW->office]++; ELSE IF (!$REND_BY_OFFICE[$ROW->office]) $REND_BY_OFFICE[$ROW->office] = 0;
			}
			ELSE  
			{	
				$NODES_BY_OFFICE['Unsorted']++;
				IF($ROW->cpu > $CPU_DETECT) $REND_BY_OFFICE['Unsorted']++;
			}
		}
				
		ASORT($TOPUSER, SORT_NUMERIC);
		$TOP = ARRAY_KEYS($TOPUSER);
				
		$OFFICES = ARRAY_UNIQUE($OFFICES);
		$GROUPS = ARRAY_UNIQUE($GROUPS);
		$USERS = ARRAY_UNIQUE($USERS);
			
		ASORT($REND_BY_USER, SORT_NUMERIC);
		$REND_BY_USER = ARRAY_REVERSE($REND_BY_USER, TRUE);
			
		FOREACH($REND_BY_USER AS $K => $V) {
			$U = ROUND($V / COUNT($DR) * 100.0, 1);
			
			$JSON['renderbyuser']['data'][] =  $V;
			$JSON['renderbyuser']['label'][] = STRIPSLASHES($K) . ' (' . $U . '%)';
		}
		
		FOREACH($NODES_BY_OFFICE AS $K => $V) {
			$JSON['nodebyoffice']['data'][] =  $V;
			$JSON['nodebyoffice']['label'][] = $K;
		}
		
		ASORT($REND_BY_OFFICE, SORT_NUMERIC);
		$REND_BY_OFFICE = ARRAY_REVERSE($REND_BY_OFFICE, TRUE);
		
		FOREACH($REND_BY_OFFICE AS $K => $V) {			
			$JSON['rendbyoffice']['data'][] =  $V;
			$JSON['rendbyoffice']['label'][] = $K;
		}
		
		IF(!COUNT($REND_BY_OFFICE)) {
			$JSON['rendbyoffice']['data'][] =  0;
			$JSON['rendbyoffice']['label'][] = 'None';
		}
		
		
		// GET OFFICES
		$JSON['offices'] = ARRAY();
		
		$QUERY = "SELECT * FROM `offices` WHERE status=0;";
		$RESULT = $MYSQLI->query($QUERY);
				
		WHILE($ROW = $RESULT->fetch_object()){
			$JSON['offices'][] = $ROW->name;
		}
		
		// GET TIME FOR LATEST 12 MONTHS
		
		$DR_CNT = COUNT($DR) ? COUNT($DR) : 1;
		$JSON['totalcnt'] = COUNT($DR);		
		$JSON['farmidle'] = $JSON['totalcnt'] - $NOW_REND;
		$JSON['farmusage'] = ROUND($NOW_REND / $DR_CNT * 100.0, 0) ;
		$JSON['farmunused'] = ROUND($JSON['farmidle'] / $DR_CNT * 100.0, 0) ;
		$JSON['farmrender'] = $NOW_REND;
		$JSON['usedoffices'] = COUNT($OFFICES);
		$JSON['usedgroups'] = COUNT($GROUPS);
		$JSON['usersrend'] = COUNT($USERS);
		$JSON['topuser'] = END($TOP) ? END($TOP) : 'N/A';
		$JSON['topusernodes'] = END($TOPUSER) ? END($TOPUSER) : 0;
		$JSON['youused'] = $REND_BY_USER[$USER] ? $REND_BY_USER[$USER] : 0;
		
		$EFFICIENCY['percent'] = $EFFICIENCY['total'] > 0 ? ROUND($EFFICIENCY['used'] / $EFFICIENCY['total'] * 100.0, 0) : 0;
		$JSON['efficiency'] = $EFFICIENCY;
				
		RETURN JSON_ENCODE($JSON);
	}
	
	FUNCTION mysqliGetDR($USER, $DATA)
	{	
		$JSON = ARRAY();
		
		$USER = Strip($USER);
		$GROUP = NULL;
		
		$MYSQLI = mysqliConnect();
	
		IF($MYSQLI->connect_errno) {
			ECHO '{"message": "ERROR"}';
			RETURN FALSE;
		}
		
		
		$QUERY = "SELECT * FROM users WHERE `user`='" . $USER . "' ;";
		
		$RESULT = $MYSQLI->query($QUERY);
		IF($RESULT) {
			$U = $RESULT->fetch_object();
			$GROUP = $U->group ? $U->group : NULL;
		}
		
		$OFFICE = Strip($DATA->office);
		$ATTACH_OFFICE = ISSET($DATA->office) ? " AND `office`='" . $OFFICE . "'" : '';
		IF($OFFICE == 'All') $ATTACH_OFFICE = '';
		IF($OFFICE == 'Unsorted') $ATTACH_OFFICE =  " AND (`office` IS NULL OR `office`='')";
		
		$ATTACH_GROUP = $GROUP ? " OR `group`='" . $GROUP . "'" : "";
				
		$QUERY = "SELECT name,status,user,ip,services,cpu,ram,aram,`desc`  FROM dr WHERE (TO_SECONDS(NOW()) - TO_SECONDS(updated) < 13) AND ((NULLIF(`group`, '') IS NULL) " . $ATTACH_GROUP . ") " . $ATTACH_OFFICE . ";";
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
								
				$ROW->services = getSrv($ROW->services);
				$RAM = getRAM($ROW->ram, $ROW->aram);
				
				UNSET($ROW->ram);
				UNSET($ROW->aram);
				$ROW->ram = $RAM;
				$ROW->cpu = $ROW->cpu > 97 ? 100 : $ROW->cpu;
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
	
	FUNCTION mysqliGetOffices()
	{		
		$OFFICES = [];
		
		$MYSQLI = mysqliConnect();
	
		IF($MYSQLI->connect_errno) {
			ECHO '{"message": "ERROR"}';
			RETURN FALSE;
		}
		
		$QUERY = "SELECT * FROM `offices`;";
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
				$OFFICES[] = $ROW;							
			}							
		}
						
		$MYSQLI->CLOSE();		
		
		RETURN JSON_ENCODE($OFFICES);
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
				
		$QUERY = 'UPDATE users SET pwd="' . $PW . '", ip="' . $_SERVER['REMOTE_ADDR'] . '" WHERE user="' . $USER . '";';
		$RESULT = $MYSQLI->query($QUERY);
				
		$MYSQLI->CLOSE();		
		
		RETURN $OUT;
	}
	
	FUNCTION mysqliGetNodes($USER, $DATA, $CLEAR = FALSE)
	{
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO '{"message": "ERROR"}';
			RETURN FALSE;
		}
		
		$OUT = '{"message": "NONODES"}';
		
		IF($CLEAR){
			$QUERY = 'UPDATE dr SET user=null, job=null WHERE user="' . $USER . '";';
			$RESULT = $MYSQLI->query($QUERY);
		}
		
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
	
	FUNCTION mysqliDropSelectedNodes($USER, $DATA)
	{
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO "ERROR";
			RETURN FALSE;
		}
		$USER = Strip($USER);
				
		$NODES = [];
		FOREACH ($DATA->nodes AS $VALUE) {
			$NODES[] = 'ip="' . $VALUE . '"';
		}
		
		$QUERY = 'UPDATE dr SET user=null, job=null WHERE user="' . $USER . '" AND (' . IMPLODE(' OR ', $NODES) . ');';
		$RESULT = $MYSQLI->query($QUERY);
		$COUNT = $QUERY;
		$COUNT = $MYSQLI->affected_rows;
		
		$MYSQLI->CLOSE();
		
		ECHO '{"message": "NODESSELDROPPED", "cnt": ' . $COUNT . '}';
	}
	
	FUNCTION kickSelectedNodes($DATA)
	{
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO "ERROR";
			RETURN FALSE;
		}
						
		$NODES = [];
		FOREACH ($DATA->nodes AS $VALUE) {
			$NODES[] = 'ip="' . $VALUE . '"';
		}
		
		$QUERY = 'UPDATE dr SET user=null, job=null WHERE (' . IMPLODE(' OR ', $NODES) . ');';		
		$RESULT = $MYSQLI->query($QUERY);
		$COUNT = $QUERY;
		$COUNT = $MYSQLI->affected_rows;
		
		$MYSQLI->CLOSE();
		
		ECHO '{"message": "NODESELKICK", "cnt": ' . $COUNT . '}';
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
	
	FUNCTION mysqliUserNodes($USER, $USE_IP = FALSE)
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
				
				$VAL = $USE_IP ? $ROW->ip : $ROW->name;
				$OUT .= $VAL . '|' . $S . ';';							
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
	
	FUNCTION mysqliGetGlobalStatus()
	{
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO "";
			RETURN FALSE;
		}
		
		$OUT = "OFFLINE";		
		$QUERY = "SELECT * FROM global WHERE name='status' LIMIT 1;";
		
		$RESULT = $MYSQLI->query($QUERY);						
						
		$ROW = $RESULT->fetch_object();	
		
		IF($ROW->value == 1){		
			$OUT = "ONLINE";
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
			//return 'OK'	;
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
	
	FUNCTION exeGetGlobal($IP)
	{
		$MYSQLI = mysqliConnect();
				
		IF($MYSQLI->connect_errno) {
			ECHO "ERROR";
			RETURN FALSE;
		}
		
		$IP = HTMLSPECIALCHARS($IP);
		
		$OUT = "";
		$GLOBAL = [];
		
		$QUERY = "SELECT * FROM global;";
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
				
				$GLOBAL[$ROW->name] = $ROW->value;
			}
		}
		
		$QUERY = "SELECT * FROM dr WHERE ip='" . $IP . "' LIMIT 1;";
		$RESULT = $MYSQLI->query($QUERY);
		$ROW = $RESULT->fetch_object();			
				
		RETURN $GLOBAL['status'] . '|' . $GLOBAL['idle'] . '|' . $ROW->status . '|' . $ROW->srvautostart;
	}
	
	FUNCTION exeSetData1($USER, $SERVICE, $CPU, $NAME, $IP, $RAM, $ARAM, $CPUDATA, $MAX3D, $CPUNUMBER)
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
				$ATTACH = " user='" . $USER . "'";
			BREAK;
		}
		
		$QUERY = "UPDATE dr SET cpunumber='" . $CPUNUMBER . "', cpu='" . $CPU . "', ram='" . $RAM . "', aram='" . $ARAM . "', cpudata='" . $CPUDATA . "', services='" . $SERVICE . "', max3d='" . $MAX3D . "', name='" . $NAME . "' " . $ATTACH .", updated=NOW() WHERE ip='" . $IP . "'";		
		
		$RESULT = $MYSQLI->query($QUERY);

		setStatistic($MYSQLI, $USER, $CPU, $NAME, $IP, $RAM, $ARAM); 
			
		$MYSQLI->CLOSE();
		
		RETURN 'OK';
	}
	
	FUNCTION setStatistic($MYSQLI, $USER, $CPU, $NAME, $IP, $RAM, $ARAM)
	{
		$POWER_REND_KW = $GLOBALS['POWER_REND_KW'];
		$POWER_IDLE_KW = $GLOBALS['POWER_IDLE_KW'];
		
		$QUERY = "SELECT * FROM dr WHERE ip='" . $IP . "';";
		$RESULT = $MYSQLI->query($QUERY);
		$NODE = $RESULT->fetch_object();
		IF(!$NODE) RETURN FALSE;
				
		$TIME = TIME();
				
		$DATA['name'] =  $NODE->name;
		$DATA['date'] =  DATE('Y-m-d');
		$DATA['ip'] =  $NODE->ip;
		$DATA['cpu'] =  $NODE->cpu;
		$DATA['updated'] =  $TIME;
		$DATA['office'] =  $NODE->office;
		$DATA['rendkw'] =  $NODE->rendkw ? $NODE->rendkw : $POWER_REND_KW;
		$DATA['idlekw'] =  $NODE->idlekw ? $NODE->idlekw : $POWER_IDLE_KW;
		
		$QUERY = "SELECT * FROM statistic_nodeusage WHERE ip='" . $IP . "' AND date='" . $DATA['date'] . "'";
		$RESULT = $MYSQLI->query($QUERY);
			
		$WHERE['ip'] = $IP;
		$WHERE['month'] = $DATA['month'];
		$WHERE['year'] = $DATA['year'];
			
		IF($ROW = $RESULT->fetch_object()) {
			$TOTALTIME = $ROW->totaltime;
			IF($ROW->cpu > 45 AND $CPU > 45) $TOTALTIME = $TOTALTIME + ($TIME - $ROW->updated);
			
			$QUERY = "UPDATE statistic_nodeusage SET " . IMPLODE_VAR(',', $DATA) . ",totaltime='" . $TOTALTIME . "' WHERE id='" . $ROW->id . "'";		
			$RESULT = $MYSQLI->query($QUERY);						
		} ELSE {
			$QUERY = "INSERT INTO statistic_nodeusage (" . IMPLODE_KEY(',', $DATA) . ") VALUES(" . IMPLODE_DATA(',', $DATA) . ");";		
			$RESULT = $MYSQLI->query($QUERY);	
		}		
	}
	
	FUNCTION getStatistic() {
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO 'ERROR';
			RETURN FALSE;
		}
		
		$M = DATE("m");
		$Y = DATE("Y");
				
		$QUERY = "SELECT * FROM statistic_nodeusage WHERE year='" . $YEAR . "'";
		$RESULT = $MYSQLI->query($QUERY);

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
				
		$QUERY = "INSERT IGNORE INTO dr (name,ip) VALUES('" . $NAME . "', '" . $IP . "');";		
		$RESULT = $MYSQLI->query($QUERY);

		$QUERY = "UPDATE dr SET name='" . $NAME . "', ip='" . $IP . "' WHERE name='" . $NAME . "';";		
		$RESULT = $MYSQLI->query($QUERY);			
			
		$MYSQLI->CLOSE();

		RETURN 'OK';
	}
	
	FUNCTION exeGetServices()
	{						
		$MYSQLI = mysqliConnect();
		
		IF($MYSQLI->connect_errno) {
			ECHO '';
			RETURN FALSE;
		}
		$S = "";	
		$QUERY = "SELECT * FROM services;";
		$A = [];
		
		IF ($RESULT = $MYSQLI->query($QUERY)) {
			
			WHILE($ROW = $RESULT->fetch_object()) {
				$A[] = $ROW->name;						
			}							
		}
			
		$S = IMPLODE(';', $A);
			
		$MYSQLI->CLOSE();

		RETURN $S;
	}	
?>