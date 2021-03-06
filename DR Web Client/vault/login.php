﻿<?php	
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

	$BROWSER = GET_BROWSER_NAME();
	
	IF(ISSET($_GET['browser'])) 
	{	
		$_SESSION['browser'] = $_GET['browser'];
	}
	
	IF(ISSET($_SESSION['browser'])) 
	{	
		$BROWSER = $_SESSION['browser'];
	}
	
	
	
	$RIGHTS = -1;
	// TRUST USER!
	IF(ISSET($_GET['trustuser'])){		
		$_SESSION['user'] = $_GET['trustuser'];
		$_SESSION['trustuser'] = true;
		$_SESSION['logged'] = true;
	}

	IF (!ISSET($_SESSION['logged'])) {
		$_SESSION['logged'] = false;
		IF($BROWSER != 'MXS') 
		{		
			AUTH();
		}
	} 
	ELSE 
	{				
		IF (ISSET($_SESSION['user'])) {				
				$USER = isUserAllow($_SESSION['user']);
				IF($USER !== -1)
				{
					$_SESSION['logged'] = true;
					$RIGHTS = $USER->rights;
				}
				ELSE
				{
					$_SESSION['logged'] = false;
				}
		}
		ELSE
		{
			$_SESSION['logged'] = false;		
		}
	}
			
	IF ($_SESSION['logged'] === false && ISSET($_SERVER['PHP_AUTH_USER'])) {
				
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
	$JSON['user'] = ISSET($_SESSION['user']) ? $_SESSION['user'] : 'null';	
	$JSON['logged'] = $_SESSION['logged'];	
	$JSON['admin'] = $RIGHTS == 1;	
	$JSON['browser'] = $BROWSER;
			
	ECHO JSON_ENCODE($JSON);	
?>