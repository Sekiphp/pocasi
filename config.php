<?php
	$server = "localhost";
	$user = "";        
	$pass = ""; 
	$base = "";
  
	# get persistent conection
	$conn = mysql_pConnect($server, $user, $pass) or die("kill");
	$select = mysql_select_db($base, $conn);
	$use = mysql_query("USE `$base`");
	$set = mysql_query("SET NAMES 'utf8'");
  
  //phpinfo();