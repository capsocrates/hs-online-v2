<?php
include "goodstuff/functions.php";
sec_session_start();
//session_start();
if(isset($_SESSION['name'])){
	//$text = htmlspecialchars($_POST['text']);
	$gameName = htmlspecialchars($_SESSION['gamename']);
	$userName = trim(addslashes(htmlspecialchars($_SESSION['name'])));
	//random number 
	$text =  mt_rand(1, 20);
	$fp = fopen($_SESSION['gamename'], 'a');
	fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i> <strong>". $_SESSION['name'] ."</strong> rolled <strong>". $text ."</strong> on the d20</i><br></div>");
	fclose($fp);
	
	include ("goodstuff/connections.php");
		//Write roll to roll snapshot table
		$conn = db_connectQuery();
		if (!$conn)
		//Add game
			echo "There was an internal error.  Please try again later.";
			
		$query2 = "insert into hs_rolls_itemized(gamename, username, action, d20, gametime) values
								('".$gameName."', '".$userName."', '0', '".$text."', NOW())";
		$result = mysql_query($query2);
		//Update countInitRoll var in hs_masterclock; set it to true.
		countInitRoll($gameName,1);
		//Update the player time to include the time it took to roll the dice.
		addPlayerTime($gameName, $userName);
		mysql_close($conn);
	
}

?>
