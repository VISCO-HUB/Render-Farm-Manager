<?php		
	INCLUDE '../vault/config.php';
	INCLUDE '../vault/functions.php';
	INCLUDE 'functions.php';
	SESSION_START();
		
	$ADMIN = isUserAllow($_SESSION['user']);
	$QUERY = $_GET['query'];
	$DATA = JSON_DECODE(FILE_GET_CONTENTS('php://input'));
		
	IF(ISSET($_SESSION['user']) && $ADMIN == 1 && ISSET($_GET['query']))
	{
		$MYSQLI = mysqliConnect();
	
		IF($MYSQLI->connect_errno) {
			DIE('{"message": "ERROR"}');
		}
		
		SWITCH($QUERY){
			
			CASE 'getDR': ECHO adminDR($MYSQLI);
			BREAK;
			
			CASE 'getServices': ECHO adminGetServices($MYSQLI);
			BREAK;
			
			CASE 'getUsers': ECHO adminGetUsers($MYSQLI);
			BREAK;
			
			CASE 'addUser': ECHO adminAddUser($MYSQLI, $DATA);
			BREAK;
			
			CASE 'deleteUsers': ECHO adminDeleteUsers($MYSQLI, $DATA);
			BREAK;
			
			CASE 'changeAccess': ECHO adminChangeAccess($MYSQLI, $DATA);
			BREAK;
			
			CASE 'changePassword': ECHO adminChangePassword($MYSQLI, $DATA);
			BREAK;
			
			CASE 'serviceAdd': ECHO adminServiceAdd($MYSQLI, $DATA);
			BREAK;
			
			CASE 'serviceDelete': ECHO adminServiceDelete($MYSQLI, $DATA);
			BREAK;
			
			CASE 'serviceDisable': ECHO adminServiceDisable($MYSQLI, $DATA);
			BREAK;
			
			CASE 'getGlobal': ECHO adminGlobal($MYSQLI);
			BREAK;
			
			CASE 'globalChangeParam': ECHO adminGlobalChangeParam($MYSQLI, $DATA);
			BREAK;
			
			CASE 'sendEmail': ECHO adminSendEmail($MYSQLI, $DATA);
			BREAK;
			
			CASE 'nodesDisable': ECHO adminNodesDisable($MYSQLI, $DATA);
			BREAK;
			
			CASE 'nodeDelete': ECHO adminNodeDelete($MYSQLI, $DATA);
			BREAK;
			
			CASE 'sendCmd': ECHO adminSendCmd($DATA);
			BREAK;
		}

		$MYSQLI->CLOSE();			
	}
	ELSE
	{
		DIE('{"message": "RESTRICTED"}');	
	}
?>