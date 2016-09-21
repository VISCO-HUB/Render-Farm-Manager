<?php	
	ini_set('error_reporting', E_ALL);
	error_reporting(E_ALL);
	
	INCLUDE_ONCE 'config.php';
	INCLUDE_ONCE 'functions.php';

	session_set_cookie_params(9999999999999);

	$JSON = ARRAY();	
	SESSION_START();
	
		
	FUNCTION AUTH() {
		HEADER('WWW-Authenticate: Basic realm="Login:"');
		HEADER('HTTP/1.0 401 Unauthorized');
		$JSON['error'] = 'Please enter correct e-mail and password!';
		ECHO JSON_ENCODE($JSON);
		EXIT;
	}

	IF (!ISSET($_SESSION['logged'])) {
		$_SESSION['logged'] = false;
		AUTH();
	} 
	ELSE 
	{
		IF (ISSET($_SESSION['user']) && IN_ARRAY($_SESSION['user'], $ALLOWED_USERS)) {				
				$_SESSION['logged'] = true;
		}
		ELSE
		{
			$_SESSION['logged'] = false;		
		}
	}
				
		
	IF ($_SESSION['logged'] === false) {
		
		IF(ISSET($_SERVER['PHP_AUTH_USER']) && IN_ARRAY(HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']), $ALLOWED_USERS))
		{
			$_SESSION["logged"] = true;	
			
			$_SESSION["user"] = HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']);
			$PW = HTMLSPECIALCHARS($_SERVER['PHP_AUTH_PW']);
			
			$DATA = mysqliGetUser($_SESSION["user"]);

			IF(ISSET($DATA[0]->pwd))
			{				
				IF($DATA[0]->pwd != $PW)
				{
					$_SESSION['logged'] = false;	
				}	
			}
			ELSE
			{		
				mysqliSetUser($_SESSION['user'], $PW);
			}			
		}						
	}
	
	IF ($_SESSION["logged"] === false) {		
		$_SESSION['logged'] = false;
		SESSION_DESTROY();
	}
		
	
	$JSON['ip'] = $_SERVER['REMOTE_ADDR'];
	$JSON['user'] = $_SESSION['user'];	
	$JSON['logged'] = $_SESSION['logged'];	
			
	ECHO JSON_ENCODE($JSON);	
?>