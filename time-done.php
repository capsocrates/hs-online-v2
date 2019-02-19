<?php
/*
//Handle Time tracking functions
Utilizes columns in hs_rolls: tracktime = 0 or 1; only process time functions if it is set to 1.  That way, users who are not playing won't be time tracked.
Added FUN WITH SOUNDWARP at line 25; FUN WITH DYSOLE at line 25
*/
include "goodstuff/functions.php";
sec_session_start();

if(isset($_SESSION['name'])){

	$gameName = htmlspecialchars($_SESSION['gamename']);
	$userName = trim(addslashes(htmlspecialchars($_SESSION['name'])));
	//Check hs_rolls>tracktime: if set to 1, then process time functionality.  Otherwise, do nothing (non-playing user).
	
	include ("goodstuff/connections.php");
			$conn = db_connectQuery();
					if (!$conn)
					//Add game
					 echo "There was an internal error.  Please try again later.";
					 addPlayerTime($gameName, $userName);
						
						$fp = fopen($_SESSION['gamename'], 'a');

						if ($userName == "Dysole") {
							$doneVar = "punned";
						//} elseif ($userName == "Foudzing") {
						//	$doneVar = "done <i style=\"color:#ff0000;\">(He pressed DONE! Remind him to do it next time)</i>";
						//} elseif ($userName == "foudzing") {
						//	$doneVar = "done <i style=\"color:#ff0000;\">(He pressed DONE! Remind him to do it next time)</i>";
						} else {
							$doneVar = "done";
						}
						fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i> <strong>The turn/action of ". $_SESSION['name'] ." is ". $doneVar .".</strong></i><br></div>");
						fclose($fp);
					}

			mysql_close($conn);

?>
