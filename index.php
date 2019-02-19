<?php
/* *******************************
APP NAME: HS Online App
AUTHOR: FtC
CHANGE LOG:
2014-12-18: Display username upon login: lines 157-160


  ******************************** */
include "goodstuff/functions.php";
error_reporting(0);
//session_start();
sec_session_start();
/* VAR SECTION */
//Variable to track whether the user is playing or spectating
$errorCount = 0;
$appName = "HS Online App";
/* END VAR SECTION */
//If the URL contains a logfile and Gdoc variables, load them both in the browser, but without the chat ability or dice rolling.

$viewUrl="http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
//Removed $ from the front of below GET variable - something to do with new hosting environment, apparently:
if ($_GET["log"]){
$log = trim(htmlspecialchars($_GET["log"]));
$gdoc = trim(htmlspecialchars($_GET["gdoc"]));
}
//Set spectator var = 1 if LOG and GDOC variables are passed in the string.
if( (isset($log) AND $log != "") AND (isset($gdoc) AND $gdoc != "") ){
	$spectator = 1;
	//Set global variables and create session for game names
	$gameName = $log;
	$varDoc = $gdoc;
		$gameName = stripslashes(htmlspecialchars($gameName));
		
		if(file_exists($gameName) && filesize($gameName) > 0){
			$_SESSION['gamename'] = $gameName;
			$_SESSION['gdoc'] = $varDoc;
		
		} else {
			$errorCount += $errorCount;
		}
} else {
	$spectator = 0;
}


if(isset($_GET['logout'])){	 
	
	//Simple exit message
	$fp = fopen($_SESSION['gamename'], 'a');
	fwrite($fp, "<div class='msgln'><i>User ". $_SESSION['name'] ." has left the chat session.</i><br></div>");
	fclose($fp);
	
	session_destroy();
	header("Location: index.php"); //Redirect the user
}

if($log == "") {
	if(isset($_POST['enter'])){
		//If user's IP is in the blacklist, they can't login!
		if (in_array ($_SERVER['REMOTE_ADDR'], $deny)) {
		   header("Location: index.php");
		   exit();
		}	
		if($_POST['name'] != ""){
			$userName = trim(stripslashes(htmlspecialchars($_POST['name'])));
			//Nuke the apostrophes in the usernames, mainly to avoid problems with some of the older code
			$userName = str_replace("'","", $userName) ;
			$_SESSION['name'] = $userName;
		} else {
			echo '<span class="error">Please type in a name</span>';
		}
		if(($_POST['game'] != "") OR (isset($_SESSION['gamename']))){ 
			//Check to see if a game exists with that name.
			//If not, tell the user to enter a new game name;
				if($_POST['game'] != "") {
					$gameName = trim($_POST['game']);
					$gameName = stripslashes(htmlspecialchars($gameName));
				} else {
				//Set GAMENAME to the GAME variable pulled from the URL string
					$gameName = $_SESSION['gamename'];
				}
			if(file_exists($gameName) && filesize($gameName) > 0){
				$_SESSION['gamename'] = $gameName;
			
			} else {
				echo '<h3>There is no game with that name.  Please try again.</h3>';
				session_destroy();
				exit;
			}
					
		} else {
			if(!isset($_SESSION['gamename'])){
				
				
				//Create a new file with a random name
				//First, generate a random name
				
				$gameName = ($prefix . uniqid() . time());

				$gameName = $gameName.".html";


				$fp = fopen($gameName, 'a');
				fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
				fclose($fp);
				$_SESSION['gamename'] = $gameName;
				$userIP = $_SERVER['REMOTE_ADDR'];
				//Write playername and/or game to DB
					include ("goodstuff/connections.php");
					$conn = db_connectQuery();
						if (!$conn)
						//Add game
						 echo "There was an internal error.  Please try again later.";
						 //create game entry in hs_mastertime:
						 
						 //TIME*************************************
						 resetMasterclock($gameName);
						 $query = "select gamename from hs_games where gamename = '".$gameName."'";
						 $result = mysql_query($query);
						if (!$result)
								echo "Could not execute query";
						
						if (mysql_num_rows($result)<1) {//Add gamename to table
							$query = "insert into hs_games(gamename, gametime) values
									('".$gameName."', NOW())";
							$result = mysql_query($query);
						}
						//Add user
						$query = "select username from hs_users where username = '".$userName."'";
						 $result = mysql_query($query);
						if (!$result)
								echo "Problem adding user.";
						
						if (mysql_num_rows($result)<1) {//Add gamename to table
							$query = "insert into hs_users(username, jointime, ipaddress) values
									('".$userName."', NOW(), '".$userIP."')";
							$result = mysql_query($query);				

						} else {//update IP address for account
							$result = mysql_query("UPDATE hs_users SET jointime = NOW(), ipaddress = '".$userIP."' WHERE username = '".$userName."'");
						}
			
						//End DB write
					mysql_close($conn);
				}
		}
		//Obtain the G Doc string:
		if($_POST['gdoc'] != ""){
			$varDoc = stripslashes(htmlspecialchars($_POST['gdoc']));
		}
		
		if(isset($_SESSION['gdoc'])){
			$varDoc = $_SESSION['gdoc'];
		}
		
	//Display the a 'user x has logged in' message:
		if((isset($_SESSION['name'])) && (isset($_SESSION['gamename']))){
				$fp = fopen($_SESSION['gamename'], 'a');
				fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b> has logged in!<br></div>");
				fclose($fp);

		}
		
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<title><?php echo $appName; ?></title>
<link type="text/css" rel="stylesheet" href="css/style.css" />
<script language="javascript">
	var name = "#floatMenu";
	var menuYloc = null;
	
		$(document).ready(function(){
			menuYloc = parseInt($(name).css("top").substring(0,$(name).css("top").indexOf("px")))
			$(window).scroll(function () { 
				offset = menuYloc+$(document).scrollTop()+"px";
				$(name).animate({top:offset},{duration:500,queue:false});
			});
		}); 
</script>

</head>
<body>
<div id="container">
<?php
//If the log/gdoc vars are not set, display the login form
if(!isset($_SESSION['name']) AND ($log == "" AND $gdoc == "")){
	loginForm();
} else{

 //Display the embedded G Doc
	if ($spectator == 0) { ?><div id="maincontent"><div class="innertube"><?php } else { ?><div id="maincontent-guest"><div class="innertube-guest"><?php } ?>


		<?php //you will need to pass the Google Doc variable into the post when initially signing in
			//the user will also have to sign in to Google Docs externally, before they will be able to edit the document ?>
			<?php if ($spectator == 0) { ?>				
				<iframe src="https://docs.google.com/drawings/d/<?php echo $varDoc; ?>/edit?hl=en_US" scrolling="no" style="height: 100%; width: 100%;"></iframe>
			<?php } else { ?>
				<iframe src="https://docs.google.com/drawings/d/<?php echo $varDoc; ?>/edit?hl=en_US&ui=2&rm=demo&chrome=false&embedded=true" width="100%" height="100%" scrolling="yes" frameborder="0"></iframe>
				<script type="text/javascript">
				  $('iframe.auto-height').iframeAutoHeight({debug: false});
				</script>
			<?php } ?>
			

	</div>
	</div>

	<div id="floatMenu" class="ui-widget-content">
	<?php
	//Display all tools for logged in users.
	if ($spectator == 0) { ?> 
		<div class="topPanel">
			<strong>HS Online</strong> | Welcome, <strong><?php if (isset($_SESSION['name'])) { echo $_SESSION['name']; } else { echo "Guest"; } ?></strong> | 
			<strong><a href="faqs.php" target="_blank" class="textlink">FAQs</a></strong> | <a id="exit" class="textlink" href="#">Logout</a><br />
			Game name: <input type="text" name="savedgamename" value="<?php echo $_SESSION['gamename']; ?>" readonly="readonly" size="33" />


			<noscript>Please enable Javascript in your browser in order to use the enhanced functionality of this site.</noscript>
		</div>
			<div id="multiOpenAccordion">
				<h3><a class="navpanel-cat" href="#">Chat Menu</a></h3>
				<div>

						<div id="chatbox"><?php
						if(file_exists($_SESSION['gamename']) && filesize($_SESSION['gamename']) > 0){
							$handle = fopen($_SESSION['gamename'], "r");
							$contents = fread($handle, filesize($_SESSION['gamename']));
							fclose($handle);
							
							echo $contents;
						}
						?></div>

					
						<form name="message" action="">
							<input name="usermsg" type="text" id="usermsg" size="63" /><br />
							<span class="spanText">Quick message menu:</span><br /> <select id="quickmsg" name="quickmsg">
							<option value=""></option>
							<option value="Attacking highlighted">Attacking highlighted</option>
							<option value="Deadly Strike, so double the skulls">Deadly Strike</option>
							<option value="Done">Done</option>
							<option value="Double-check your dice">Double-check your dice</option>
							<option value="Double-check your movement">Double-check your movement</option>
							<option value="End of Round">End of Round</option>
							<option value="New Round">New Round</option>
							<option value="Place Order Markers">Place Order Markers</option>
							<option value="Ready">Ready</option>
							<option value="Opponent-Done">Remind opponent to press Done</option>
							<option value="Roll for initiative">Roll for initiative</option>
							<option value="Roll for trap">Roll for trap</option>
							<option value="Wannok">Roll for Wannok</option>
							<option value="Shields of Valor, so double the shields">Shields of Valor</option>
							<option value="Opponent rolls for leaving engagement">Tell opponent to roll for leaving engagement</option>
							<option value="Opponent rolls for engagement strike">Tell opponent to roll for engagement strike</option>
							<option value="Opponent can scatter or scurry or vanish">Tell opponent to scatter/scurry/vanish</option>
							<option value="Tough/Warforged Resolve, so add one automatic shield">Tough/Warforged Resolve</option>
							<option value="I blame Filthy!">Blame Filthy</option>
							</select>
							<button name="submitmsg" class="adicebutton" type="submit" id="submitmsg" value="Send">Send</button>
						</form>
						<hr />
						<h5>Game Clock - Actions:</h5>
						<button type="button" class="adicebutton" title="Press this button after you complete your turn, or scatter/roll for engagement strike, and so forth." onclick="doneTurn()">Done</button>&nbsp;|&nbsp;<button type="button" class="adicebutton" title="If you and your opponent(s) leave a game, everyone needs to press this button once they return." onclick="resumeGame()">Resume a paused game</button>

				</div>
				<h3><a class="navpanel-cat" href="#">Dice Tools</a></h3>
				<div>
					<div><strong>Choose the number of dice to roll:</strong><br />
						<input type="checkbox" id="valDice" name="valDice" value="valkyrie"> Roll Valkyrie dice<br />
					<table width="296" border="0" align="center" cellpadding="0" cellspacing="0">
					<tr><td>
					  <table>
					  <tr>
							<td align="center">
							<strong class="colorattack">ATTACKING:</strong>
								<p>
								  <button type="button" class="adicebutton" onclick="diceRoller(1)">01</button>&nbsp;
								  <button type="button" class="adicebutton" onclick="diceRoller(2)">02</button>&nbsp;
								  <button type="button" class="adicebutton" onclick="diceRoller(3)">03</button>&nbsp;
								  <button type="button" class="adicebutton" onclick="diceRoller(4)">04</button>&nbsp;
								<br />
								  <button type="button" class="adicebutton" onclick="diceRoller(5)">05</button>&nbsp;
								  <button type="button" class="adicebutton" onclick="diceRoller(6)">06</button>&nbsp;
								  <button type="button" class="adicebutton" onclick="diceRoller(7)">07</button>&nbsp;
								  <button type="button" class="adicebutton" onclick="diceRoller(8)">08</button>&nbsp;
								<br />              
								<button type="button" class="adicebutton" onclick="diceRoller(9)">09</button>&nbsp;
								<button type="button" class="adicebutton" onclick="diceRoller(10)">10</button>&nbsp;
								<button type="button" class="adicebutton" onclick="diceRoller(11)">11</button>&nbsp;
								<button type="button" class="adicebutton" onclick="diceRoller(12)">12</button>&nbsp;
								</p>
							</td>
					  </tr>
					  </table>
					  </td>
					  <td>
					  <table>
					  <tr>
							<td align="center">
							<strong class="colordefend">DEFENDING:</strong>
								<p>
								  <button type="button" class="ddicebutton" onclick="diceRollerD(1)">01</button>&nbsp;
								  <button type="button" class="ddicebutton" onclick="diceRollerD(2)">02</button>&nbsp;
								  <button type="button" class="ddicebutton" onclick="diceRollerD(3)">03</button>&nbsp;
								  <button type="button" class="ddicebutton" onclick="diceRollerD(4)">04</button>&nbsp;
								<br />
								  <button type="button" class="ddicebutton" onclick="diceRollerD(5)">05</button>&nbsp;
								  <button type="button" class="ddicebutton" onclick="diceRollerD(6)">06</button>&nbsp;
								  <button type="button" class="ddicebutton" onclick="diceRollerD(7)">07</button>&nbsp;
								  <button type="button" class="ddicebutton" onclick="diceRollerD(8)">08</button>&nbsp;
								<br />             
								<button type="button" class="ddicebutton" onclick="diceRollerD(9)">09</button>&nbsp;
								<button type="button" class="ddicebutton" onclick="diceRollerD(10)">10</button>&nbsp;
								<button type="button" class="ddicebutton" onclick="diceRollerD(11)">11</button>&nbsp;
								<button type="button" class="ddicebutton" onclick="diceRollerD(12)">12</button>&nbsp;
								</p>
							</td>
					  </tr>
					  </table>
					  </td></tr>
					</table>
					<button type="button" class="adicebutton" onclick="d20roll()">Roll 20-Sided Die</button></td>

			</div>
				</div>
				<h3><a class="navpanel-cat" href="#">Order Marker Mgmt</a></h3>
				<div>
					<iframe frameborder="0" height="100%" src="omframe.php" width="100%"> </iframe>
				</div>
				<h3><a class="navpanel-cat" href="#">Stats</a></h3>
				<div>
					<iframe frameborder="0" height="100%" src="showstats.php" width="100%"> </iframe>
				</div>

			</div>
	<?php } else { //Guest viewing, display limited menu ?>
		<div class="topPanel">
			<strong><?php echo $appName; ?></strong> | Best viewed in Chrome or Firefox<br /><br />
				<div>
				<div id="chatbox" style="height:300px;"><?php
				if(file_exists($_SESSION['gamename']) && filesize($_SESSION['gamename']) > 0){
					$handle = fopen($_SESSION['gamename'], "r");
					$contents = fread($handle, filesize($_SESSION['gamename']));
					fclose($handle);
					
					echo $contents;
				}
				?></div>
				</div>
				
			<div>
			<form id="form" name="form" method="post" action="index.php" onsubmit="return validateForm();">
			<input type="text" name="name" id="name" />

			<input type="submit" name="enter" id="enter" value="Enter Name and Login" />

			</form>
			</div>
		</div>

	<?php } ?>
	</div>

	  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	  <script type="text/javascript" src="scripts/jquery.multi-open-accordion-1.5.3.min.js"></script>
	  <link type="text/css" rel="stylesheet" href="css/jquery-toolbar-custom.css" />

	  <script>
	  $(function() {
		$( "#floatMenu" ).draggable();
	  });
	  </script>

	<script type="text/javascript">
		var name = "#floatMenu";
		var menuYloc = null;
	// jQuery Document
	$(document).ready(function(){

		//If user submits the form
		$("#submitmsg").click(function(){	
			var clientmsg = $("#usermsg").val();
			if ( $('#quickmsg').val() !== ""){clientmsg = $("#quickmsg").val();}
			$.post("post.php", {text: clientmsg});				
			$('#usermsg').val('');
			$('#quickmsg').val('');
			return false;
		});

		$('#multiOpenAccordion').multiOpenAccordion({
					active: [1, 2],
					click: function(event, ui) {
						//console.log('clicked')
					},
					init: function(event, ui) {
						//console.log('whoooooha')
					},
					tabShown: function(event, ui) {
						//console.log('shown')
					},
					tabHidden: function(event, ui) {
						//console.log('hidden')
					}
					
				});
				
		$('#multiOpenAccordion').multiOpenAccordion("option", "active", []);
		$("#d20").click(function(){	
			var d20 = Math.floor(Math.random()*20) +1;
			document.getElementById("dtwenty").value = d20;
			document.getElementById("dtwenty").innerHTML = document.getElementById("dtwenty").value;

			$.post("rolltwenty.php", {text: d20});				
			return false;
		});
		$("#regulardice").click(function(){	
			var rolledDice = parseInt($("#rolleddice").val());
			alert(rolledDice);
			roll(parseInt(rolledDice));
					
			return false;
		});
		//Dropdown menu stuff:
			$('#floatMenu > li').click(function(e) { // limit click to children of mainmenue
				var $el = $('ul',this); // element to toggle
				$('#floatMenu > li > ul').not($el).slideUp(); // slide up other elements
				$el.stop(true, true).slideToggle(400); // toggle element
				return false;
			});
			$('#floatMenu > li > ul > li').click(function(e) {
				e.stopPropagation();  // stop events from bubbling from sub menu clicks
			});

		
		
		//Load the file containing the chat log
		function loadLog(){		
			var oldscrollHeight = $("#chatbox").prop("scrollHeight") - 20;
			$.ajax({

				url: "<?php echo $_SESSION['gamename']; ?>",
				cache: false,
				success: function(html){		
					$("#chatbox").html(html); //Insert chat log into the #chatbox div				
					var newscrollHeight = $("#chatbox").prop("scrollHeight") - 20;
					if(newscrollHeight > oldscrollHeight){
						$("#chatbox").animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll to bottom of div
					}				
				},
			});
		}
		setInterval (loadLog, 2500);	//Reload file every 2.5 seconds
		
		//If user wants to end session
		$("#exit").click(function(){
			var exit = confirm("Are you sure you want to end the session?");
			if(exit==true){window.location = 'index.php?logout=true';}		
		});
		
		
	});
	</script>
	<script type="text/javascript">
	function diceRoller(dice)
	{
		//Display symbols if Valkyrie dice are checked
		if ($("#valDice").prop("checked")) {
			valDice = 1;
		} else {
			valDice = 0;
		}
		$.post("rolldice.php", { roll: dice, val: valDice } );
	}

	function diceRollerD(dice)
	{
		//Dislay symbols if Valkyrie dice are checked
		if ($("#valDice").prop("checked")) {
			valDice = 1;
		} else {
			valDice = 0;
		}
		$.post("rolldiceD.php", { roll: dice, val: valDice } );
	}

	function resetdice ()
	{
		document.getElementById("skull").value = 0;
		document.getElementById("shield").value = 0;
		document.getElementById("blank").value = 0;
		document.getElementById("twenty").value = 0;
		document.getElementById("twenty").innerHTML = document.getElementById("twenty").value
		document.getElementById("skull").innerHTML = document.getElementById("skull").value
		document.getElementById("shield").innerHTML = document.getElementById("shield").value
		document.getElementById("blank").innerHTML = document.getElementById("blank").value
		document.getElementById("adddice").checked=false
	}

	function resetom ()
	{
		document.getElementById("om1").value = "";
		document.getElementById("om2").value = "";
		document.getElementById("om3").value = "";
		document.getElementById("omx").value = "";
		document.getElementById("om1").innerHTML = document.getElementById("om1").value
		document.getElementById("om2").innerHTML = document.getElementById("om2").value
		document.getElementById("om3").innerHTML = document.getElementById("om3").value
		document.getElementById("omx").innerHTML = document.getElementById("omx").value
	}


	function roll (dtmp)
	{
		diceRoller (dtmp);
	}

	function rerollskulls ()
	{
		var tmp = document.getElementById("skull").value
		document.getElementById("skull").value = 0;
		diceRoller (tmp);
	}

	function rerollshields ()
	{
		var tmp = document.getElementById("shield").value
		document.getElementById("shield").value = 0;
		diceRoller (tmp);
	}

	function rerollblanks ()
	{
		var tmp = document.getElementById("blank").value
		document.getElementById("blank").value = 0;
		diceRoller (tmp);
	}

	function d20roll()
	{
		$.post("rolltwenty.php");	
	}

	function resumeGame()
	{
	$.post("resumegame.php");	
	}

	 function toggle(id) {
			var state = document.getElementById(id).style.display;
				if (state == 'block') {
					document.getElementById(id).style.display = 'none';
				} else {
					document.getElementById(id).style.display = 'block';
				}
	}

	</script>
	<script type="text/javascript">
	function validateForm()
	{
	var x=document.forms["form"]["name"].value;

	if (x==null || x=="" || x.charAt("0") == " ")
	  {
	  alert("Please enter a name.");
	  return false;
	  }
	}
	function doneTurn()
	{
		$.post("time-done.php");
	}

	</script>
<?php
}
?>
</div>
</body>
</html>