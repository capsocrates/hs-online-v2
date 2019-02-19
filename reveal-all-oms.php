<?php
//Reveal all OM locations for the user, and roll for initiative.  This only shows the 'cards' that the OMs are on, not the actual OMs.
include "goodstuff/functions.php";
sec_session_start();
if(isset($_SESSION['name'])){

	include ("goodstuff/connections.php");
	
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		//$sessionVar = $_SESSION['om1'];
		
		//Vars to use with time-tracking functions.  Can be used for other stuff, too!
		$gameName = htmlspecialchars($_SESSION['gamename']);
		$userName = trim(addslashes(htmlspecialchars($_SESSION['name'])));
		
		$conn = db_connectQuery();
				if (!$conn)
				
			     echo "There was an internal error.  Please try again later.";
				 $fp = fopen($_SESSION['gamename'], 'a');
				 
				 
				  $query = sprintf("SELECT om1, om2, om3, omx, omxx from hs_oms WHERE username = '%s' and game = '%s' ORDER BY omtime DESC LIMIT 1",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);
						$num_results = mysql_num_rows($result);
						//Var to determine if all 4 required OMs are set.  If the value is 4, then reveal the OMs.
						$varAllset = 0;

						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							//$sessionVar =  $row["om1"];
							//Display button to reveal all OMs.
							
								if(isset($row['om1']) && $row['om1'] != ""){
									$omArray[] = $row['om1'];
									$varAllset++;
								}
								if(isset($row['om2']) && $row['om2'] != ""){
									$omArray[] = $row['om2'];
									$varAllset++;
								}
								if(isset($row['om3']) && $row['om3'] != ""){
									$omArray[] = $row['om3'];
									$varAllset++;
								}
								if(isset($row['omx']) && $row['omx'] != ""){
									$omArray[] = $row['omx'];
									$varAllset++;
								}
								if(isset($row['omxx']) && $row['omxx'] != ""){
									$omArray[] = $row['omxx'];
								}	

									//Shuffle the OM placement
									shuffle($omArray);
									$arrayTotal = "";
									foreach($omArray as $value){

										$arrayTotal = $arrayTotal. "1 OM on ".$value."<br />";

									}

						}
						
						//varAllset MUST = 4, to signify that tthe 4 required OMs are set.
						if ($varAllset == 4) {
							//Display OM locations in chat log.
							fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i>User ". $_SESSION['name'] ." reveals Order Markers:</i><br></div>");
							fwrite($fp, "<div class='msgln'><i>$arrayTotal </i><br></div>");
							
							//Roll for initiative
							$roll =  mt_rand(1, 20);
							fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i><strong> ". $_SESSION['name'] ."</strong> rolled <strong>". $roll ."</strong> for initiative</i><br></div>");
							
							//Call trackInitTime and update time as required.  Remember:  the first person
							//to roll for init/reveal OMs does not incur a time penalty; everyone else does!
							trackInitTime($gameName, $userName);
							
							//Update countInitRoll var in hs_masterclock; set it to true.
							countInitRoll($gameName,1);
							
							//Since we are revealing OMs, it is assumed that the player is an active player.  So, let's set their corresponding
							//tracking var in hs_rolls to 1.
							setActivePlayer($gameName, $userName);
							
							//If player is the first to set OMs, the other players need to be timed on their OM placement time.
							//updateStartclock($gameName, $userName);
							
					
							//Activate time tracking, since you are setting OMs.
							/////////////addPlayerTime($gameName, $userName);
								
							$query2 = "insert into hs_rolls_itemized(gamename, username, action, d20, gametime) values
													('".$gameName."', '".$userName."', '3', '".$roll."', NOW())";
							$result = mysql_query($query2);
							fclose($fp);
							mysql_close($conn);
						}
	}		  
}

?>