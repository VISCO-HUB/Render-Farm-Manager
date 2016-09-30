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

	$RIGHTS = -1;
	
	IF (!ISSET($_SESSION['logged'])) {
		$_SESSION['logged'] = false;
		AUTH();
	} 
	ELSE 
	{
		$USER = isUserAllow($_SESSION['user']);
		
		IF (ISSET($_SESSION['user']) && $USER !== -1) {				
				$_SESSION['logged'] = true;
				$RIGHTS = $USER->rights;
		}
		ELSE
		{
			$_SESSION['logged'] = false;		
		}
	}
			
	IF ($_SESSION['logged'] === false) {
				
		$USER = isUserAllow($_SERVER['PHP_AUTH_USER']);
		
		IF(ISSET($_SERVER['PHP_AUTH_USER']) && $USER !== -1)
		{
			$_SESSION["logged"] = true;	
			$RIGHTS = $USER->rights;
						
			$_SESSION["user"] = HTMLSPECIALCHARS($_SERVER['PHP_AUTH_USER']);
			$PW = HTMLSPECIALCHARS($_SERVER['PHP_AUTH_PW']);
			
			IF(!EMPTY($USER->pwd))
			{				
				IF($USER->pwd != MD5($PW))
				{
					$_SESSION['logged'] = false;	
				}	
			}
			ELSE
			{		
				mysqliSetUser($_SESSION['user'], MD5($PW));
			}			
		}						
	}
	
	IF ($_SESSION['logged'] === false) {		
		$_SESSION['logged'] = false;
		SESSION_DESTROY();
	}
		
	
	$JSON['ip'] = $_SERVER['REMOTE_ADDR'];
	$JSON['user'] = $_SESSION['user'];	
	$JSON['logged'] = $_SESSION['logged'];	
	$JSON['admin'] = $RIGHTS == 1;	
			
	ECHO JSON_ENCODE($JSON);	
?>