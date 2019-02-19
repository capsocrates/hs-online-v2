<?php
//User has chosen to reveal an OM, display the OM and remove the session variable for that OM
include "goodstuff/functions.php";
sec_session_start();
if(isset($_SESSION['name'])){
	$text = htmlspecialchars($_POST['text']);
	
	include ("goodstuff/connections.php");
	
	if ($text == 1 OR $text == "1"){
		//$sessionVar = $_SESSION['om1'];
		
		$conn = db_connectQuery();
				if (!$conn)
				//Add game
			     echo "There was an internal error.  Please try again later.";
				  $query = sprintf("SELECT om1 from hs_oms WHERE username = '%s' and game = '%s' ORDER BY omtime DESC LIMIT 1",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
				//$query = "SELECT om1 from hs_oms WHERE gamename = ORDER BY slast DESC, splayers DESC";
						$result = mysql_query($query);
						$num_results = mysql_num_rows($result);

						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							$sessionVar =  $row["om1"];

						}
				//Now, delete the OM from the row

				 $query = sprintf("UPDATE hs_oms SET om1 = '' WHERE username = '%s' and game = '%s'",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);

		$omNumber = 1;
		//unset variable
		//unset($_SESSION['om1']);
	}
	if ($text == 2 OR $text == "2"){
		$conn = db_connectQuery();
				if (!$conn)
				//Add game
			     echo "There was an internal error.  Please try again later.";
				  $query = sprintf("SELECT om2 from hs_oms WHERE username = '%s' and game = '%s' ORDER BY omtime DESC LIMIT 1",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
				//$query = "SELECT om1 from hs_oms WHERE gamename = ORDER BY slast DESC, splayers DESC";
						$result = mysql_query($query);
						$num_results = mysql_num_rows($result);

						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							$sessionVar =  $row["om2"];

						}
						//Now, delete the OM from the row
				 $query = sprintf("UPDATE hs_oms SET om2 = '' WHERE username = '%s' and game = '%s'",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);

		$omNumber = 2;

	}
	if ($text == 3 OR $text == "3"){
		$conn = db_connectQuery();
				if (!$conn)
				//Add game
			     echo "There was an internal error.  Please try again later.";
				  $query = sprintf("SELECT om3 from hs_oms WHERE username = '%s' and game = '%s' ORDER BY omtime DESC LIMIT 1",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
				//$query = "SELECT om1 from hs_oms WHERE gamename = ORDER BY slast DESC, splayers DESC";
						$result = mysql_query($query);
						$num_results = mysql_num_rows($result);

						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							$sessionVar =  $row["om3"];

						}
						//Now, delete the OM from the row
				 $query = sprintf("UPDATE hs_oms SET om3 = '' WHERE username = '%s' and game = '%s'",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);

		$omNumber = 3;

	}
	if ($text == "X"){
		$conn = db_connectQuery();
				if (!$conn)
				//Add game
			     echo "There was an internal error.  Please try again later.";
				  $query = sprintf("SELECT omx from hs_oms WHERE username = '%s' and game = '%s' ORDER BY omtime DESC LIMIT 1",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
				//$query = "SELECT om1 from hs_oms WHERE gamename = ORDER BY slast DESC, splayers DESC";
						$result = mysql_query($query);
						$num_results = mysql_num_rows($result);

						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							$sessionVar =  $row["omx"];

						}
						//Now, delete the OM from the row
				 $query = sprintf("UPDATE hs_oms SET omx = '' WHERE username = '%s' and game = '%s'",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);

		$omNumber = "X";

	}
	if ($text == "XX"){
		$conn = db_connectQuery();
				if (!$conn)
				//Add game
			     echo "There was an internal error.  Please try again later.";
				  $query = sprintf("SELECT omxx from hs_oms WHERE username = '%s' and game = '%s' ORDER BY omtime DESC LIMIT 1",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);
						$num_results = mysql_num_rows($result);

						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							$sessionVar =  $row["omxx"];

						}
						//Now, delete the OM from the row
				 $query = sprintf("UPDATE hs_oms SET omxx = '' WHERE username = '%s' and game = '%s'",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);

		$omNumber = "XX";

	}

		
	if($sessionVar != "" AND $omNumber != ""){
		
		$gameName = addslashes(htmlspecialchars($_SESSION['gamename']));
		$userName = addslashes(htmlspecialchars($_SESSION['name']));
		//Activate time tracking, since you revealed an OM:
		//updateStartclock($gameName, $userName);
		//set OM placement var to false:
		countOmPlacement($gameName,0);
		
		//Set INIT and OM time-tracking vars to False
		numberedOmReveal($gameName);
		
		$fp = fopen($_SESSION['gamename'], 'a');
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i> ". $_SESSION['name'] ." revealed OM <strong>". $omNumber ."</strong> on <strong>". $sessionVar."</strong></i><br></div>");
		fclose($fp);
	}

	
	mysql_close($conn);
}

?>