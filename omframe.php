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
				//Set addRand sesson to binary True; this will keep the checkbox checked until it is manually unchecked
				//arc = AddRandomChars
				$_SESSION['arc'] = "1";
				//XX Marker, for C3G abilities (ie Loki) and other uses:
				if ($_POST['omxx'] != "") {
					$omxx = getRand(trim(stripslashes(htmlspecialchars(removeApost($_POST['omxx'])))));
				}
			 } else {
				$om1 = trim(stripslashes(htmlspecialchars(removeApost($_POST['om1']))));
				$om2 = trim(stripslashes(htmlspecialchars(removeApost($_POST['om2']))));
				$om3 = trim(stripslashes(htmlspecialchars(removeApost($_POST['om3']))));
				$omx = trim(stripslashes(htmlspecialchars(removeApost($_POST['omx']))));
				//Set addRand session to N)
				$_SESSION['arc'] = "0";
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
					 $query = sprintf("select game from hs_oms where game = '%s' and username = '%s'",
			            $gameName,
                        $userName);
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
					//If there is no corresponding entry for hs_rolls, make one now (which means that the dice roll trigger has not yet been fired):
					$query = sprintf("select gamename from hs_rolls where username = '%s' and gamename = '%s'",
			             $userName,
                         $gameName);
					 $result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					
					if (mysql_num_rows($result)<1) {//Add gamename to table
						$query2 = "insert into hs_rolls(gamename, username, gametime) values
								('".$gameName."', '".$userName."', NOW())";
						$result2 = mysql_query($query2);
					}
					//TIME STUFF*************************************
					//Since we have set OMs, lets look up the corresponding entry in HS_MASTERCLOCK and set TRACKTIME to True:
					enableTracktime($gameName);
					//Call trackOmTime and update time as required.  Remember:  the first person
					//to submit OMs does not incur a time penalty; everyone else does!
					trackOmTime($gameName, $userName);
					
					//Set the running clock to zero, for time recording purposes:
					//resetMasterclock($gameName);
						
			mysql_close($conn);
			

			//Tell log that you have set all of your Order Markers:
			$fp = fopen($_SESSION['gamename'], 'a');
			fwrite($fp, "<div class='msgln'>(".date("g:i A").")<strong><i>User ". $_SESSION['name'] ." has set all Order Markers.</i></strong><br></div>");

			fclose($fp);
			?>
			<div id="om-reveal-all-top"><button type="button" onclick="revealAllOms()">Roll Initiative and Reveal OMs</button></div>
			<?php
			
		} else {
			if($_POST['om1'] == "") {
			echo '<span class="error"><strong>Please add a card for Order Marker 1</strong></span><br />';
			}
			if($_POST['om2'] == "") {
			echo '<span class="error"><strong>Please add a card for Order Marker 2</strong></span><br />';
			}
			if($_POST['om3'] == "") {
			echo '<span class="error"><strong>Please add a card for Order Marker 3</strong></span><br />';
			}
			if($_POST['omx'] == "") {
			echo '<span class="error"><strong>Please add a card for Order Marker X</strong></span><br />';
			}
		}
	}


	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>HS Online</title>
	<link type="text/css" rel="stylesheet" href="css/style.css" />
	</head>
	<body>
	<div id="embedPageDiv">
		<div id="omForm">
		<form action="omframe.php" method="post">
			<h4>Set Order Markers:</h4>
			<?php //If OMs are set, let's display them in the corresponding form fields:
			$conn = db_connectQuery();
			if (!$conn)
			echo "There was an internal error.  Please try again later.";
			
			$getOM1 =  '';
			$getOM2 =  '';
			$getOM3 =  '';
			$getOMx =  '';
			$getOMxx =  '';
			if(isset($_SESSION['name'])){
			
				$gameName = htmlspecialchars($_SESSION['gamename']);
				$userName = trim(addslashes(htmlspecialchars($_SESSION['name'])));
				 $query = sprintf("SELECT om1, om2, om3, omx, omxx from hs_oms WHERE username = '%s' and game = '%s' ORDER BY omtime DESC LIMIT 1",
							 addslashes(htmlspecialchars($userName)),
							 mysql_real_escape_string($gameName));
				$result = mysql_query($query);
				$num_results = mysql_num_rows($result);

				for ($b=0; $b < $num_results; $b++)
				{
					$row = mysql_fetch_array($result);
					$getOM1 =  $row["om1"];
					$getOM2 =  $row["om2"];
					$getOM3 =  $row["om3"];
					$getOMx =  $row["omx"];
					$getOMxx =  $row["omxx"];

				} 
			}?>			
			<label for="name">OM 1:</label>
			<input type="text" name="om1" id="om1" maxlength="40" value="<?php echo $getOM1; ?>" /><br />
			<label for="name">OM 2:</label>
			<input type="text" name="om2" id="om2" maxlength="40" value="<?php echo $getOM2; ?>" /><br />
			<label for="name">OM 3:</label>
			<input type="text" name="om3" id="om3" maxlength="40" value="<?php echo $getOM3; ?>" /><br />
			<label for="name">OM X:</label>
			<input type="text" name="omx" id="omx" maxlength="40" value="<?php echo $getOMx; ?>" /><br />
			<label for="name"><strong>OPTIONAL OM (for C3G, customs, etc):</strong><br />OM XX:</label>
			<input type="text" name="omxx" id="omxx" maxlength="40" value="<?php echo $getOMxx; ?>" /><br />
			<input type="checkbox" id="addRand" name="addRand" value="Yes" <?php if ((isset($_SESSION['arc'])) AND ($_SESSION['arc'] == 1)){ ?> checked <?php } ?> /> Add random characters (for OM-removing abilities)<br />

			<button type="submit" class="adicebutton" name="addom" id="addom" value="Submit your Order Markers">Submit Order Markers</button>
		</form>
		</div>
		<?php 
		//Display Order markers, cross out OMs as they are revealed
				
				  $query = sprintf("SELECT om1, om2, om3, omx, omxx from hs_oms WHERE username = '%s' and game = '%s' ORDER BY omtime DESC LIMIT 1",
			             addslashes(htmlspecialchars($_SESSION['name'])),
                         mysql_real_escape_string($_SESSION['gamename']));
						$result = mysql_query($query);
						$num_results = mysql_num_rows($result);
						//var to determine if  all Order Marker locations have been revealed
						$varHideOmButtons = 0;
						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							//$sessionVar =  $row["om1"];
							//Display button to reveal all OMs.
								if((isset($row['om1']) && $row['om1'] != "") && (isset($row['om2']) && $row['om2'] != "") && (isset($row['om3']) && $row['om3'] != "") && (isset($row['omx']) && $row['omx'] != "")) {
									$omArray[] = $row['om1'];
									$omArray[] = $row['om2'];
									$omArray[] = $row['om3'];
									$omArray[] = $row['omx'];
									//Only display OM XX if the var has been set.
									if ($row['omxx'] != "") {
										$omArray[] = $omxx;
									}
									//Shuffle the OM placement
									shuffle($omArray);
									$arrayTotal = "";
									foreach($omArray as $value){

										$arrayTotal = $arrayTotal. "1 OM on ".$value."<br />";

									}
								?>
									<div id="om-reveal-all"><button type="button" onclick="revealAllOms()">Roll Initiative and Reveal OMs</button></div><br />
								<?php
								//set var to 1 and hide other buttons
								$varHideOmButtons = 1;
								}
							
								if(isset($row['om1']) && $row['om1'] != ""){
								?>
								<div id="om1master" <?php if($varHideOmButtons == 1){ ?> style="display:none;" <?php } ?>>
									<div id="om1set"><span class="omInfo">OM 1: <?php echo $row['om1']; ?></span></div> 
									<div id="om1-reveal"><button type="button"  class="adicebutton" onclick="revealom('1')">Reveal OM 1</button></div><br />
								</div>
								<?php
								}
								if(isset($row['om2']) && $row['om2'] != ""){
								?>
								<div id="om2master" <?php if($varHideOmButtons == 1){ ?> style="display:none;" <?php } ?>>
									<div id="om2set"<span class="omInfo">OM 2: <?php echo $row['om2']; ?></span></div> 
									<div id="om2-reveal"><button type="button" class="adicebutton" onclick="revealom('2')">Reveal OM 2</button></div><br />
								</div>
								<?php
								}
								if(isset($row['om3']) && $row['om3'] != ""){
								?>
								<div id="om3master" <?php if($varHideOmButtons == 1){ ?> style="display:none;" <?php } ?>>
									<div id="om3set"><span class="omInfo">OM 3: <?php echo $row['om3']; ?></span></div> 
									<div id="om3-reveal"><button type="button" class="adicebutton" onclick="revealom('3')">Reveal OM 3</button></div><br />
								</div>
								<?php
								}
								if(isset($row['omx']) && $row['omx'] != ""){
								?>
								<div id="om0master" <?php if($varHideOmButtons == 1){ ?> style="display:none;" <?php } ?>>
									<div id="om0set"><span class="omInfo">OM X: <?php echo $row['omx']; ?></span></div>	
									<div id="om0-reveal"><button type="button" class="adicebutton" onclick="revealom('X')">Reveal OM X</button></div><br />
								</div>
								<?php
								}
								if(isset($row['omxx']) && $row['omxx'] != ""){
								?>
								<div id="om00master" <?php if($varHideOmButtons == 1){ ?> style="display:none;" <?php } ?>>
									<div id="om00set"><span class="omInfo">OM XX: <?php echo $row['omxx']; ?></span></div>	
									<div id="om00-reveal"><button type="button" class="adicebutton" onclick="revealom('XX')">Reveal OM XX</button></div><br />
								</div>
								<?php
								}
								//Display link to show OM form
								?>
								<br /><a id="showOmForm" onclick="showOmForm();">[Show OM Form]</a><br />
									
								<?php
						}
		mysql_close($conn);
	?>
	</div>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
	<script type="text/javascript">
	//Reveal OM locations
	function revealAllOms() {
		var revealAll = 'revealoms';
		$.post("reveal-all-oms.php");	
		document.getElementById('om-reveal-all-top').style.display = 'none';
		document.getElementById('om-reveal-all').style.display = 'none';
		document.getElementById('omForm').style.display = 'none';
		document.getElementById('om1master').style.display = 'block';
		document.getElementById('om2master').style.display = 'block';
		document.getElementById('om3master').style.display = 'block';
		document.getElementById('om0master').style.display = 'block';
		document.getElementById('om00master').style.display = 'block';
		//Change opacity on SUBMIT OM button:
		//document.getElementById('addom').style.opacity=0.3;
		//document.getElementById('addom').style.filter="alpha(opacity=30)";
	}

		function revealom(omname)
		{
		$.post("revealom-DB.php", {text: omname});	
		//strike out revealed OM
			if (omname == '1') {
					//document.getElementById('om1-reveal').style.display = 'none';
					document.getElementById('om1-reveal').innerHTML = "<button type=\"button\" class=\"adicebutton\" onclick=\"doneTurn('1')\">Done with OM1 Turn</button>";
					var strikeLine = document.getElementById("om1set").innerHTML;
					document.getElementById("om1set").innerHTML = strikeLine.strike();
			}
			if (omname == '2') {
					//document.getElementById('om2-reveal').style.display = 'none';
					
					document.getElementById('om2-reveal').innerHTML = "<button type=\"button\" class=\"adicebutton\" onclick=\"doneTurn('2')\">Done with OM2 Turn</button>";
					var strikeLine = document.getElementById("om2set").innerHTML;
					document.getElementById("om2set").innerHTML = strikeLine.strike();
			}
			if (omname == '3') {
					//document.getElementById('om3-reveal').style.display = 'none';
					document.getElementById('om3-reveal').innerHTML = "<button type=\"button\" class=\"adicebutton\" onclick=\"doneTurn('3')\">Done with OM3 Turn</button>";
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

	function doneTurn(omid)
	{
		var selectid = "om" + omid + "-reveal";
		document.getElementById(selectid).style.display = 'none';
		//Hide any DONE buttons on previous OMs
		var i = omid - 1;
		for (var i; i>0; i--)
		  {
			var prevOm = "om" + i + "-reveal";
			document.getElementById(prevOm).style.display = 'none';			
		  }
		  //Increase OM Submit btn opacity
		  var btnOpa = omid * .3;
		  var btnOpa2 = omid * 30;
		  document.getElementById('addom').style.opacity=btnOpa;
		  document.getElementById('addom').style.filter="alpha(opacity="+btnOpa2+")";
		$.post("time-done.php");

	}
	
	function showOmForm() {
		$('#omForm').fadeIn('slow', function() {
				// Animation complete
		});
	}
	

	</script>
	</body>
	</html>
<?php
}
?>



		