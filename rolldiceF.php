<?php
include "goodstuff/functions.php";
sec_session_start();
	//If user's IP is in the blacklist, they can't login!
	if (in_array ($_SERVER['REMOTE_ADDR'], $deny)) {
	   exit();
	}	
if(isset($_SESSION['name'])){

$Roll = htmlspecialchars($_POST['roll']); 
$valDice = stripslashes(htmlspecialchars($_POST['val']));
$skull = 0;
$shield = 0;
$blank = 0;
for ($i = 0; $i < $Roll; $i++) {
	$text =  mt_rand(1, 6);
	if($text < 4){
		$skull++;
	} elseif($text == 6){
		$blank++;
	} else {
		$shield++;
	}
 }
 //Dislay symbols if Valkyrie dice are selected
 if ($valDice == 1) {
	$valVar = "and <strong>". $blank ." symbols</strong>";
 } else {
	$valVar = "";
 }
	
	$fp = fopen($_SESSION['gamename'], 'a');
	fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i><strong> ". $_SESSION['name'] ."</strong> rolled <strong style=\"color: #ff0000\">". $skull ." skulls</strong> ".$valVar." with <strong>". $Roll ." total</strong> dice</i><br></div>");
	fclose($fp);
	
				//Write dice rolls to DB
			$gameName = htmlspecialchars($_SESSION['gamename']);
			$userName = trim(addslashes(htmlspecialchars($_SESSION['name'])));
			include ("goodstuff/connections.php");
			$conn = db_connectQuery();
					if (!$conn)
					//Add game
					 echo "There was an internal error.  Please try again later.";
					 $query = "select gamename from hs_rolls where gamename = '".$gameName."' AND username = '".$userName."'";
					 $result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					
					if (mysql_num_rows($result)<1) {//Add gamename to table
						$query = "insert into hs_rolls(gamename, username, skulls, shields, blanks, gametime) values
								('".$gameName."', '".$userName."', '".$skull."', '".$shield."', '".$blank."', NOW())";
						$result = mysql_query($query);
					} else {
						//update the existing row to reflect the new totals
						$result = mysql_query("UPDATE hs_rolls SET skulls = skulls + '".$skull."', shields = shields + '".$shield."', blanks = blanks + '".$blank."', gametime = NOW() WHERE gamename = '".$gameName."' AND username = '".$userName."'");
						//or die(mysql_error());  
					}
					//Insert into the individual stats table
					$query2 = "insert into hs_rolls_itemized(gamename, username, action, skulls, blanks, totals, gametime) values
								('".$gameName."', '".$userName."', '1', '".$skull."', '".$blank."', '".$Roll."', NOW())";
					$result = mysql_query($query2);
			mysql_close($conn);
}

?>