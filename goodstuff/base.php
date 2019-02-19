<?php
	// Set the error reporting level
	error_reporting(E_ALL);
	ini_set("display_errors", 1);

	// Start a PHP session
	if(!isset($_SESSION))
	{
		session_start();
	} 

	define('DB_HOST', 'localhost');
	define('DB_USER', '');
	define('DB_PASS', '');
	define('DB_NAME', ''');
	
    // HTML Whitelist
    define('WHITELIST', '<b><i><strong><em><a>');
	
	$hostname_um = "localhost";
	$database_um = "";
	$username_um = "";
	$password_um = "";
	$um = mysql_pconnect($hostname_um, $username_um, $password_um) or trigger_error("There was an error.  Please try again, or contact support.");

	if ( !isset($_SESSION['token']) || time()-$_SESSION['token_time']>=300 )
	{
		$_SESSION['token'] = md5(uniqid(rand(), TRUE));
		$_SESSION['token_time'] = time();
	}
	
	// Create a database object
	try {
		$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
		$db = new PDO($dsn, DB_USER, DB_PASS);

	} catch (PDOException $e) {
		echo 'Connection failed: ' . $e->getMessage();
		exit;
	}
	
?>