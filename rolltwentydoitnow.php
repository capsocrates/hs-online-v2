<?php
session_start();
if(isset($_SESSION['name'])){
	//$text = htmlspecialchars($_POST['text']);
	//random number 
	$text =  10;
	$fp = fopen($_SESSION['gamename'], 'a');
	fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i>User ". $_SESSION['name'] ." rolled <strong>". $text ."</strong> on the d20</i><br></div>");
	fclose($fp);
	
	$fp = fopen('yeolechatlog.shtml', 'a');
	fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i>User ". $_SESSION['name'] ." rolled <strong>". $text ."</strong> on the d20</i><br></div>");
	fclose($fp);
	
}

?>
