<?php
/*
//Handle Time tracking functions
Utilizes columns in hs_rolls: tracktime = 0 or 1; only process time functions if it is set to 1.  That way, users who are not playing won't be time tracked.
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
					 $query = "select gamename from hs_rolls where gamename = '".$gameName."' AND username = '".$userName."' AND tracktime = 1";
					 $result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					
					if (mysql_num_rows($result)<1) {//Add gamename to table
						//Do nothing
						
					} else {
					//Update the totalclock value by grabbing the startclock value (current unix timestamp), comparing it to the current time value, and then noting the difference.  Then, add the difference to the totalclock column.

						//Select the startclock value//
						  $query = sprintf("SELECT startclock, totalclock,  from hs_rolls WHERE username = '%s' and game = '%s'",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);
						$num_results = mysql_num_rows($result);

						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							$startClock =  $row["startclock"];
							$totalClock =  $row["totalclock"];

						}
						//Get the current timestamp:
						$currentTimestamp = time();
						//get the diff between the currentTimeStamp and the startClock
						$passedTime = $currentTimestamp - $startClock;
						//$passedTime = $currentTimestamp->diff($startClock);
						//Now, add this value to totalClock:
						$newTotal = $passedTime + $totalClock;
						
						//$result = mysql_query("UPDATE hs_rolls SET startclock = '".$currentTimestamp."', gametime = NOW() WHERE gamename = '".$gameName."' AND username = '".$userName."'");
						 $query = sprintf("UPDATE hs_rolls SET startclock = '%s', gametime = NOW(), totalclock = '%s' WHERE gamename = '%s' and username = '%s'",
			             addslashes(htmlspecialchars($passedTime)),
			             addslashes(htmlspecialchars($newTotal)),
						 mysql_real_escape_string($gameName),
                         mysql_real_escape_string($userName));
						 $result = mysql_query($query);

						$fp = fopen($_SESSION['gamename'], 'a');
						fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i> <strong>". $_SESSION['name'] ." is done with their turn/action.</strong></i><br></div>");
						fclose($fp);
					}

			mysql_close($conn);
	
	
	
	/*include ("goodstuff/connections.php");
		//Write roll to roll snapshot table
		$conn = db_connectQuery();
		if (!$conn)
		//Add game
			echo "There was an internal error.  Please try again later.";
			
		$query2 = "insert into hs_rolls_itemized(gamename, username, action, d20, gametime) values
								('".$gameName."', '".$userName."', '0', '".$text."', NOW())";
		$result = mysql_query($query2);
		mysql_close($conn);
	*/
}

?>
