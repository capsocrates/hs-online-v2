<?php
//Frames page for Order Marker management
include ("goodstuff/connections.php");
include "goodstuff/functions.php";
sec_session_start();
	//If user's IP is in the blacklist, they can't login!
	if (in_array ($_SERVER['REMOTE_ADDR'], $deny)) {
	   exit();
	}	
if(isset($_SESSION['name'])){
	if(isset($_POST['addom'])){
		if(($_POST['om1'] != "") && ($_POST['om2'] != "") && ($_POST['om3'] != "") && ($_POST['omx'] != "")){
			//Generate Random numbers to append to the end of the OMs.
			//These will be used for abilities that remove OMs.		
			$addRand = stripslashes(htmlspecialchars($_POST['addRand']));
			 if ($addRand == "Yes") {
				$om1 = getRand(trim(stripslashes(htmlspecialchars(removeApost($_POST['om1'])))));
				$om2 = getRand(trim(stripslashes(htmlspecialchars(removeApost($_POST['om2'])))));
				$om3 = getRand(trim(stripslashes(htmlspecialchars(removeApost($_POST['om3'])))));
				$omx = getRand(trim(stripslashes(htmlspecialchars(removeApost($_POST['omx'])))));
				//XX Marker, for C3G abilities (ie Loki) and other uses:
				if ($_POST['omxx'] != "") {
					$omxx = getRand(trim(stripslashes(htmlspecialchars(removeApost($_POST['omxx'])))));
				}
			 } else {
				$om1 = trim(stripslashes(htmlspecialchars(removeApost($_POST['om1']))));
				$om2 = trim(stripslashes(htmlspecialchars(removeApost($_POST['om2']))));
				$om3 = trim(stripslashes(htmlspecialchars(removeApost($_POST['om3']))));
				$omx = trim(stripslashes(htmlspecialchars(removeApost($_POST['omx']))));
				//XX Marker, for C3G abilities (ie Loki) and other uses:
				if ($_POST['omxx'] != "") {
					$omxx = trim(stripslashes(htmlspecialchars(removeApost($_POST['omxx']))));
				}
			 }
			
			//Write OMs to DB
			$gameName = htmlspecialchars($_SESSION['gamename']);
			$userName = trim(addslashes(htmlspecialchars($_SESSION['name'])));
			//if $omxx is null, set it to an empty value, mainly to avoid any potential data-writing errors
			if (!isset($omxx)) { $omxx = ""; }
			$conn = db_connectQuery();
					if (!$conn)
					//Add OMs
					 echo "There was an internal error.  Please try again later.";
					 $query = "select game from hs_oms where game = '".$gameName."' and username = '".$userName."'";
					 $result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					
					if (mysql_num_rows($result)<1) {//Add gamename, playername, OMs to table
						$query = "insert into hs_oms(username, om1, om2, om3, omx, omxx, game, omtime) values
								('".$userName."', '".$om1."', '".$om2."', '".$om3."', '".$omx."', '".$omxx."', '".$gameName."', NOW())";
						$result = mysql_query($query);
					} else {
						//update the existing row to reflect the new OMs
						$result = mysql_query("UPDATE hs_oms SET om1 = '".$om1."', om2 = '".$om2."', om3 = '".$om3."', omx = '".$omx."', omxx = '".$omxx."', omtime = NOW() WHERE game = '".$gameName."' AND username = '".$userName."'");
						//or die(mysql_error());  
					}
			mysql_close($conn);
			//Write variables to array

			$omArray[] = $om1;
			$omArray[] = $om2;
			$omArray[] = $om3;
			$omArray[] = $omx;
			//Only display OM XX if the var has been set.
			if ($_POST['omxx'] != "") {
				$omArray[] = $omxx;
			}
			//Shuffle the OM placement
			shuffle($omArray);

			//Tell log that you have set all of your Order Markers:
			$fp = fopen($_SESSION['gamename'], 'a');
			fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i>User ". $_SESSION['name'] ." has placed all 4 Order Markers:</i><br></div>");

			$arrayTotal = "";
			foreach($omArray as $value){

			    $arrayTotal = $arrayTotal. "1 OM on ".$value."<br />";

			}

			fwrite($fp, "<div class='msgln'><i>$arrayTotal </i><br></div>");
			fclose($fp);
		} else {
			if($_POST['om1'] == "") {
			echo '<span class="error"><strong>Please add a card for Order Marker 1<strong></span><br />';
			}
			if($_POST['om2'] == "") {
			echo '<span class="error"><strong>Please add a card for Order Marker 2<strong></span><br />';
			}
			if($_POST['om3'] == "") {
			echo '<span class="error"><strong>Please add a card for Order Marker 3<strong></span><br />';
			}
			if($_POST['omx'] == "") {
			echo '<span class="error"><strong>Please add a card for Order Marker X<strong></span><br />';
			}
		}
	}

	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>HeroSkype Application</title>
	<link type="text/css" rel="stylesheet" href="style.css" />
	</head>
	<body>
	<form action="omframe.php" method="post">
			<p>Add the name of your card for each Order Marker:</p>
			<label for="name">OM 1:</label>
			<input type="text" name="om1" id="om1" maxlength="40" /><br />
			<label for="name">OM 2:</label>
			<input type="text" name="om2" id="om2" maxlength="40"/ /><br />
			<label for="name">OM 3:</label>
			<input type="text" name="om3" id="om3" maxlength="40"/ /><br />
			<label for="name">OM X:</label>
			<input type="text" name="omx" id="omx" maxlength="40"/ /><br />
			<label for="name"><strong>OPTIONAL OM (for C3G, customs, etc):</strong><br />OM XX:</label>
			<input type="text" name="omxx" id="omxx" maxlength="40"/ /><br />
			<input type="checkbox" id="addRand" name="addRand" value="Yes" /> Add random characters (for OM-removing abilities)<br />

			<input type="submit" name="addom" id="addom" value="Submit your Order Markers" />
		</form>
		<?php 
		//Display Order markers, cross out OMs as they are revealed
				$conn = db_connectQuery();
				if (!$conn)
				//Add game
			     echo "There was an internal error.  Please try again later.";
				  $query = sprintf("SELECT om1, om2, om3, omx, omxx from hs_oms WHERE username = '%s' and game = '%s' ORDER BY omtime DESC LIMIT 1",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);
						$num_results = mysql_num_rows($result);

						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							//$sessionVar =  $row["om1"];


								if(isset($row['om1']) && $row['om1'] != ""){
								?>
								<div id="om1set">OM 1: <?php echo $row['om1']; ?></div> 
								<div id="om1-reveal"><button type="button" onclick="revealom('1')">Reveal OM 1</button></div><br />
								<?php
								}
								if(isset($row['om2']) && $row['om2'] != ""){
								?>
								<div id="om2set">OM 2: <?php echo $row['om2']; ?></div> 
								<div id="om2-reveal"><button type="button" onclick="revealom('2')">Reveal OM 2</button></div><br />
								<?php
								}
								if(isset($row['om3']) && $row['om3'] != ""){
								?>
								<div id="om3set">OM 3: <?php echo $row['om3']; ?></div> 
								<div id="om3-reveal"><button type="button" onclick="revealom('3')">Reveal OM 3</button></div><br />
								<?php
								}
								if(isset($row['omx']) && $row['omx'] != ""){
								?>
								<div id="om0set">OM X: <?php echo $row['omx']; ?></div>	
								<div id="om0-reveal"><button type="button" onclick="revealom('X')">Reveal OM X</button></div><br />
								<?php
								}
								if(isset($row['omxx']) && $row['omxx'] != ""){
								?>
								<div id="om00set">OM XX: <?php echo $row['omxx']; ?></div>	
								<div id="om00-reveal"><button type="button" onclick="revealom('XX')">Reveal OM XX</button></div><br />
								<?php
								}
						}
		mysql_close($conn);
	?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
	<script type="text/javascript">
		function revealom(omname)
		{
		$.post("revealom-DB.php", {text: omname});	
		//strike out revealed OM
			if (omname == '1') {
					document.getElementById('om1-reveal').style.display = 'none';
					var strikeLine = document.getElementById("om1set").innerHTML;
					document.getElementById("om1set").innerHTML = strikeLine.strike();
			}
			if (omname == '2') {
					document.getElementById('om2-reveal').style.display = 'none';
					var strikeLine = document.getElementById("om2set").innerHTML;
					document.getElementById("om2set").innerHTML = strikeLine.strike();
			}
			if (omname == '3') {
					document.getElementById('om3-reveal').style.display = 'none';
					var strikeLine = document.getElementById("om3set").innerHTML;
					document.getElementById("om3set").innerHTML = strikeLine.strike();
			}
			if (omname == 'X') {
					document.getElementById('om0-reveal').style.display = 'none';
					var strikeLine = document.getElementById("om0set").innerHTML;
					document.getElementById("om0set").innerHTML = strikeLine.strike();
			}
			if (omname == 'XX') {
					document.getElementById('om00-reveal').style.display = 'none';
					var strikeLine = document.getElementById("om00set").innerHTML;
					document.getElementById("om00set").innerHTML = strikeLine.strike();
			}
		}		
	</script>
	</body>
	</html>
<?php
}
		?>