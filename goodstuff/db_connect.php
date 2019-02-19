<?php

define("HOST", "localhost"); // The host you want to connect to.
define("USER", "hs-online-user"); // The database username.
define("PASSWORD", ""); // The database password. 
define("DATABASE", "hs_online_data"); // The database name.
 
$mysqli = new mysqli(HOST, USER, PASSWORD, DATABASE);
// If you are connecting via TCP/IP rather than a UNIX socket remember to add the port number as a parameter.

?>