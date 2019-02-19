<?php
error_reporting(0);
session_start();

if(isset($_GET['logout'])){	 
	
	//Simple exit message
	$fp = fopen($_SESSION['gamename'], 'a');
	fwrite($fp, "<div class='msgln'><i>User ". $_SESSION['name'] ." has left the chat session.</i><br></div>");
	fclose($fp);
	
	session_destroy();
	header("Location: fullsize.php"); //Redirect the user
}

function loginForm(){
	echo'
	<div id="loginform">
	<form action="fullsize.php" method="post">
		<p>Please enter your name to continue:</p>
		<label for="name">Name:</label>
		<input type="text" name="name" id="name" />
		<p><label for="game">Enter name of game:<br /> (leave blank to generate a new game)</label><br />
		<input type="text" name="game" id="game" /></p>

		<input type="submit" name="enter" id="enter" value="Enter" />
	</form>
	</div>
	';
}

if(isset($_POST['enter'])){
	if($_POST['name'] != ""){
		$_SESSION['name'] = stripslashes(htmlspecialchars($_POST['name']));
	}
	else{
		echo '<span class="error">Please type in a name</span>';
	}
	if($_POST['game'] != ""){
		//Check to see if a game exists with that name.
		//If not, tell the user to enter a new game name;
		$gameName = trim($_POST['game']);
		$gameName = stripslashes(htmlspecialchars($gameName));
		if(file_exists($gameName) && filesize($gameName) > 0){
			$_SESSION['gamename'] = $gameName;
		
		} else {
			echo '<h3>There is no game with that name.  Please try again.</h3>';
			session_destroy();
			exit;
		}
				
	} else {
		//Create a new file with a random name
		//First, generate a random name
		//$gameName = ($prefix . uniqid( hash("md5", time()), TRUE ) . time());
		$gameName = ($prefix . uniqid() . time());
		//printf("uniqid(): %s\r\n", uniqid());
		$gameName = $gameName.".html";

		//$fp = fopen ($gameName, "x");
		//chmod($gameName, 0644);
		//$textContents = stripslashes($varSelectedFile);
		//fwrite($fp, $varSelectedFile);
		$fp = fopen($gameName, 'a');
		fwrite($fp, "<div class='msgln'>(".date("g:i A").") <b>".$_SESSION['name']."</b>: ".stripslashes(htmlspecialchars($text))."<br></div>");
		fclose($fp);
		$_SESSION['gamename'] = $gameName;
	}
}

?>
<!--Force IE6 into quirks mode with this comment tag-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Skorcard Game Board: Online game simulator</title>

<style type="text/css">

body{
margin: 0;
padding: 0;
border: 0;
overflow: hidden;
height: 100%; 
max-height: 100%; 
}


#topbar{
position:absolute;
top: 0;
bottom: 0; 
left: 0;
width: 100%;
height: 50px;
overflow: hidden; /*Disable scrollbars. Set to "scroll" to enable*/
background: #ccc;
color: #000;
}
#framecontent{
position: absolute;
top: 50px;
bottom: 0; 
left: 0;
width: 400px; /*Width of frame div*/
height: 100%;
overflow: hidden; /*Disable scrollbars. Set to "scroll" to enable*/
background: #ccc;
color: #000;
}

#maincontent{
position: fixed;
top: 50px; 
left: 400px; /*Set left value to WidthOfFrameDiv*/
right: 0;
bottom: 0;
overflow: auto; 
background: #fff;
}

.innertube{
margin: 15px; /*Margins for inner DIV inside each DIV (to provide padding)*/
}

.topinnertube{
margin: 3px 5px 3px 5px;
}

* html body{ /*IE6 hack*/
padding: 0 0 0 200px; /*Set value to (0 0 0 WidthOfFrameDiv)*/
}

* html #maincontent{ /*IE6 hack*/
height: 100%; 
width: 100%; 
}

#chatbox {
	text-align:left;
	margin:0 auto;
	margin-bottom:25px;
	padding:10px;
	background:#fff;
	height:270px;
	width:340px;
	border:1px solid #000;
	overflow:auto; }

	#usermsg {
	width:290px;
	border:1px solid #000; }
	
input { font:12px arial; }
</style>



</head>

<body>
<?php
if(!isset($_SESSION['name'])){
	loginForm();
}
else{
?>
<div id="topbar">
<div class="topinnertube">
Top bar: Includes account options, dice creatorn (set text names for dice, aka skulls, etc), log file (and ability to change log file name), Google doc key import form, online users list
</div>
</div>
<div id="framecontent">
<div class="innertube">
	<h2>Order Marker Management</h2>
	<div id="hiddenom" class="drag">
	
		<iframe frameborder="0" height="300" src="omframe.php" width="100%"> </iframe>
	
	
	</div>
	<div id="hiddenchat">
	<h2>Chat</h2>
		<div id="chatbox"><?php
		if(file_exists($_SESSION['gamename']) && filesize($_SESSION['gamename']) > 0){
			$handle = fopen($_SESSION['gamename'], "r");
			$contents = fread($handle, filesize($_SESSION['gamename']));
			fclose($handle);
			
			echo $contents;
		}
		?></div>
		
		<form name="message" action="">
			<input name="usermsg" type="text" id="usermsg" size="63" />
			<input name="submitmsg" type="submit"  id="submitmsg" value="Send" />
		</form>
	</div>

</div>
</div>


<div id="maincontent">
<div class="innertube">

	<?php //you will need to pass the Google Doc variable into the post when initially signing in
		//the user will also have to sign in to Google Docs externally, before they will be able to edit the document ?>
		<iframe src="https://docs.google.com/drawings/d/1n1yJgsIzMpT19OSPwRzxf9mJr826G4aZtelQepWDStg/edit?hl=en_US" scrolling="no" style="width:100%;height:100%;border:none;"/>

</div>
</div>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3/jquery.min.js"></script>
<script type="text/javascript">
// jQuery Document
$(document).ready(function(){
	//If user submits the form
	$("#submitmsg").click(function(){	
		var clientmsg = $("#usermsg").val();
		$.post("post.php", {text: clientmsg});				
		$("#usermsg").attr("value", "");
		return false;
	});
	
	
	//Load the file containing the chat log
	function loadLog(){		
		var oldscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
		$.ajax({

			url: "<?php echo $_SESSION['gamename']; ?>",
			cache: false,
			success: function(html){		
				$("#chatbox").html(html); //Insert chat log into the #chatbox div				
				var newscrollHeight = $("#chatbox").attr("scrollHeight") - 20;
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
	//var roll = document.getElementById("skull").value;
	$.post("rolldice.php", { roll: dice} );
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


function d20roll()
{
	$.post("rolltwenty.php");	
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
<?php
}
?>
</body>
</html>

