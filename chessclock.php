<?php
//Record chess clock for player.
include "goodstuff/functions.php";
sec_session_start();
//Grab post var and sanitize it
$text = trim(htmlspecialchars($_POST['text']));
if(isset($_SESSION['name']) AND $text != ""){
	include ("goodstuff/connections.php");
/*	
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
		mysql_close($conn);
		$omNumber = 1;
		//unset variable
		//unset($_SESSION['om1']);
	}

*/
		
//Write clock status to game chat.
		$fp = fopen($_SESSION['gamename'], 'a');
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i>". $_SESSION['name'] ." started the clock at ".$text.".</i><br></div>");
		fclose($fp);

}

?>