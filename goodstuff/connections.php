<?php

 function db_connectQuery()
  {
  $result = mysql_connect("localhost", "hs-online-user", "");
  if (!$result)
      return false;
  if (!mysql_select_db("hs_online_data"))
      return false;

  return $result;
  }
define("HOST", "localhost"); // The host you want to connect to.
define("USER", "hs-online-user"); // The database username.
define("PASSWORD", ""); // The database password. 
define("DATABASE", "hs_online_data"); // The database name.
  $conn = new mysqli(HOST, USER, PASSWORD, DATABASE);
?>