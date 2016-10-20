<?php
	
	
	$ldap = ldap_connect("192.168.1.18");
	$username="v.lukyanenko1@visco.no";
	$password="";
	if($bind = ldap_bind($ldap, $username,$password ))
	{
		echo "logged in";
	}
	else
	{
		echo "fail";
	}
	
	echo "done";
?>