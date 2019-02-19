<?php
/*
//Resume a paused game
If all of the games' active users click on the RESUME GAME, the gameclock will be reset.  
When an individual user clicks on the button, their corresponding row>paused tuple in hs_rolls will be 
set to 1.  If all active users have this set to 1, gameclock in hs_masterclock and
gametime in hs_rolls (for all users with the gamename) will be set to the current timestamp.
'Paused' tuple will also be reset for all players.

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
					 $query = sprintf("UPDATE hs_rolls SET paused = 1 WHERE gamename = '%s' and username = '%s' and tracktime = 1",
					 mysql_real_escape_string($gameName),
                     mysql_real_escape_string($userName));
					 $result = mysql_query($query);
					 //Now, check to see if all active users for the game have 'paused' set to 1.  If so, reset the gameclocks AND flag 'paused' to 0 for each user.
					    $query2 = sprintf("SELECT username from hs_rolls WHERE gamename = '%s' and tracktime = 1",
			            mysql_real_escape_string($gameName));
						$result2 = mysql_query($query2);
						$num_results2 = mysql_num_rows($result2);
						//variable to match the active users with $num_results, to check if all users have clicked on RESUME GAME button.
						$userCount = $num_results2;
						//var for the count where paused = 1
						$pausedUserCount = 0;

						for ($bb=0; $bb < $num_results2; $bb++)
						{
							$row2 = mysql_fetch_array($result2);
							//Now, let's check the individual users
							 $query3 = sprintf("SELECT username from hs_rolls WHERE gamename = '%s' and username = '%s' and paused = 1",
							 mysql_real_escape_string($gameName),
							 mysql_real_escape_string($row2["username"]));
							 $result3 = mysql_query($query3);
							 $num_results3 = mysql_num_rows($result3);
							 
							 for ($bbb=0; $bbb < $num_results3; $bbb++)
							 {
								//increment var pausedUserCount;
								$pausedUserCount++;
								
							 }
						}
						//If var pausedUserCount = userCount, then let's reset the time clocks!
						if($userCount != 0){
							if($userCount == $pausedUserCount) {
								//Now, update the player's total time:
								 $query4 = sprintf("UPDATE hs_rolls SET gametime = NOW() WHERE gamename = '%s' and tracktime = 1",
								 mysql_real_escape_string($gameName));
								 $result4 = mysql_query($query4);
								 
								 //Get the current timestamp:
								 $currentTimestamp = date("Y-m-d H:i:s");
								 
								 //And reset the gameclock to current timestamp:
								 $query5 = sprintf("UPDATE hs_masterclock SET gameclock = '%s' WHERE gamename = '%s'",
								 $currentTimestamp,
								 addslashes(htmlspecialchars($gameName)));                        
								 $result5 = mysql_query($query5);
								 
								 //Flag off the PAUSED var for all active users in the game:
								 $query6 = sprintf("UPDATE hs_rolls SET paused = 0 WHERE gamename = '%s' and tracktime = 1",
								 mysql_real_escape_string($gameName));
								 $result6 = mysql_query($query6);
								 
								 
								 //Finally, let everyone know:
								 $fp = fopen($gameName, 'a');
									fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i> <strong>The game has been resumed, and time clocks have been updated accordingly.</strong></i><br></div>");
								 fclose($fp);
							}
						}

					}

			mysql_close($conn);

?>