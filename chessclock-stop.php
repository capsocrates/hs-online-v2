<?php
//Record chess clock for player.
include "goodstuff/functions.php";
sec_session_start();
//Grab post var and sanitize it
$text = trim(htmlspecialchars($_POST['text']));
if(isset($_SESSION['name']) AND $text != ""){
	include ("goodstuff/connections.php");
			
		$conn = db_connectQuery();
				if (!$conn)
				echo "There was an internal error.  Please try again later.";
				////Update Chess clock value

				 $query = sprintf("UPDATE hs_rolls SET chessclock = '%s' WHERE username = '%s' and gamename = '%s'",
						 addslashes(htmlspecialchars($text)),
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);
		mysql_close($conn);
		//unset variable
		//unset($_SESSION['om1']);
		
//Write clock status to game chat.
		$fp = fopen($_SESSION['gamename'], 'a');
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i>". $_SESSION['name'] ." stopped the clock at ".$text.".</i><br></div>");
		fclose($fp);

}

?>