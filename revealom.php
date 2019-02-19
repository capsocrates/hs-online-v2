<?php
//User has chosen to reveal an OM, display the OM and remove the session variable for that OM
session_start();
if(isset($_SESSION['name'])){
	$text = htmlspecialchars($_POST['text']);

	
	if ($text == 1 OR $text == "1"){
		$sessionVar = $_SESSION['om1'];
		$omNumber = 1;
		//unset variable
		unset($_SESSION['om1']);
	}
	if ($text == 2 OR $text == "2"){
		$sessionVar = $_SESSION['om2'];
		$omNumber = 2;
		//unset variable
		unset($_SESSION['om2']);
	}
	if ($text == 3 OR $text == "3"){
		$sessionVar = $_SESSION['om3'];
		$omNumber = 3;
		//unset variable
		unset($_SESSION['om3']);
	}
	if ($text == "X"){
		$sessionVar = $_SESSION['omx'];
		$omNumber = "X";
		//unset variable
		unset($_SESSION['omx']);
	}

		
	if($sessionVar != "" AND $omNumber != ""){
		$fp = fopen($_SESSION['gamename'], 'a');
		fwrite($fp, "<div class='msgln'>(".date("g:i A").")<i>User ". $_SESSION['name'] ." revealed OM <strong>". $omNumber ."</strong> on <strong>". $sessionVar."</strong></i><br></div>");
		fclose($fp);
	}
}

?>