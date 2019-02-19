<?php
include "goodstuff/functions.php";
sec_session_start();
if(isset($_SESSION['name'])){

$Roll = htmlspecialchars($_POST['roll']); 
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
	
	$fp = fopen($_SESSION['gamename'], 'a');
	fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i>User ". $_SESSION['name'] ." rolled <strong style=\"color: #1217d1;\">". $shield ." shields</strong> and <strong>".$blank." blanks</strong> with <strong>". $Roll ." total dice.</strong></i><br></div>");
	fclose($fp);
	
				//Write dice rolls to DB
			$gameName = htmlspecialchars($_SESSION['gamename']);
			$userName = trim(mysql_real_escape_string(htmlspecialchars($_SESSION['name'])));
			include ("goodstuff/connections.php");
			$conn = db_connectQuery();
					if (!$conn)
					//Add game
					 echo "There was an internal error.  Please try again later.";
					 $query = "select gamename from hs_rolls where gamename = '".$gameName."'";
					 $result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					
					if (mysql_num_rows($result)<1) {//Add gamename to table
						$query = "insert into hs_rolls(gamename, username, dskulls, dshields, dblanks, gametime) values
								('".$gameName."', '".$userName."', '".$skull."', '".$shield."', '".$blank."', NOW())";
						$result = mysql_query($query);
					} else {
						//update the existing row to reflect the new totals
						$result = mysql_query("UPDATE hs_rolls SET dskulls = dskulls + '".$skull."', dshields = dshields + '".$shield."', dblanks = dblanks + '".$blank."', gametime = NOW() WHERE gamename = '".$gameName."' AND username = '".$userName."'");
						//or die(mysql_error());  
					}
			mysql_close($conn);
}

?>