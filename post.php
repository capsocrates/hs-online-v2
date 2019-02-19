<?php
include "goodstuff/functions.php";
sec_session_start();
	//If user's IP is in the blacklist, they can't login!
	if (in_array ($_SERVER['REMOTE_ADDR'], $deny)) {
	   exit();
	}	
//session_start();
if(isset($_SESSION['name'])){
	$text = $_POST['text'];
	$gameName = htmlspecialchars($_SESSION['gamename']);
	$userName = trim(addslashes(htmlspecialchars($_SESSION['name'])));
	//Go ahead and make a DB connection, as it is used in most of the options below.  Close it out at the end of the script
	include ("goodstuff/connections.php");
		//Write roll to roll snapshot table
		$conn = db_connectQuery();
		if (!$conn)
		//Add game
			echo "There was an internal error.  Please try again later.";
	$fp = fopen($gameName, 'a');
	//Make an initiative roll, if the corresponding selection is chosen
	if (stripslashes(htmlspecialchars($text)) == "Roll for initiative") {
		$roll =  mt_rand(1, 20);
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i><strong> ". $_SESSION['name'] ."</strong> rolled <strong>". $roll ."</strong> for initiative</i><br></div>");
			
		$query2 = "insert into hs_rolls_itemized(gamename, username, action, d20, gametime) values
								('".$gameName."', '".$userName."', '3', '".$roll."', NOW())";
		$result = mysql_query($query2);
		
		addPlayerTime($gameName, $userName);
		
		//Update countInitRoll var in hs_masterclock; set it to true.
		countInitRoll($gameName,1);
	} elseif (stripslashes(htmlspecialchars($text)) == "Roll for trap") {
		$roll =  mt_rand(1, 20);
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i><strong> ". $_SESSION['name'] ."</strong> rolled a <strong>". $roll ."</strong> for the trap</i><br></div>");
			
		$query2 = "insert into hs_rolls_itemized(gamename, username, action, d20, gametime) values
								('".$gameName."', '".$userName."', '0', '".$roll."', NOW())";
		$result = mysql_query($query2);
		
		addPlayerTime($gameName, $userName);
	
	} elseif (stripslashes(htmlspecialchars($text)) == "Wannok") {
	
		$wroll =  mt_rand(1, 20);
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i><strong> ". $_SESSION['name'] ."</strong> rolled <strong>". $wroll ."</strong> for Wannok</i><br></div>");
		
		addPlayerTime($gameName, $userName);
	
	} elseif (stripslashes(htmlspecialchars($text)) == "Done") {
	
		//Mark turn/action has DONE, which resets the clock to the current timestamp
		addPlayerTime($gameName, $userName);
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i><strong> ". $_SESSION['name'] ." is done with their turn/action.</strong></i><br></div>");
		
	} elseif (stripslashes(htmlspecialchars($text)) == "Opponent-Done") {
	
		//Mark turn/action has DONE, which resets the clock to the current timestamp
		addPlayerTime($gameName, $userName);
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i><strong>Please press <b style=\"color: #ff0000;\">DONE</b> after your turn/action.</strong></i><br></div>");
		
	} elseif (stripslashes(htmlspecialchars($text)) == "Opponent rolls for leaving engagement") {
	
		//Stop your own timeclock;
		addPlayerTime($gameName, $userName);
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i><strong> ". $_SESSION['name'] ."</strong> says to please roll for leaving engagement.</i><br></div>");
	
	} elseif (stripslashes(htmlspecialchars($text)) == "Opponent rolls for engagement strike") {
	
		//Stop your own timeclock;
		addPlayerTime($gameName, $userName);
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i><strong> ". $_SESSION['name'] ."</strong> says to please roll for engagement strike.</i><br></div>");
		
	} else {
	
		fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
	
	}
	fclose($fp);
	mysql_close($conn);
}
?>