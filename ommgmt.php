<?php
//Order Marker Management code
session_start();
if(isset($_SESSION['name'])){
	if(isset($_POST['addom'])){
		if(($_POST['om1'] != "") && ($_POST['om2'] != "") && ($_POST['om3'] != "") && ($_POST['om4'] != "")){
			$_SESSION['om1'] = stripslashes(htmlspecialchars($_POST['om1']));
			$_SESSION['om2'] = stripslashes(htmlspecialchars($_POST['om2']));
			$_SESSION['om3'] = stripslashes(htmlspecialchars($_POST['om3']));
			$_SESSION['omx'] = stripslashes(htmlspecialchars($_POST['omx']));
		}
		else{
			if($_POST['om1'] != "") {
			echo '<span class="error">Please add a card for Order Marker 1</span>';
			}
			if($_POST['om2'] != "") {
			echo '<span class="error">Please add a card for Order Marker 2</span>';
			}
			if($_POST['om3'] != "") {
			echo '<span class="error">Please add a card for Order Marker 3</span>';
			}
			if($_POST['om4'] != "") {
			echo '<span class="error">Please add a card for Order Marker 4</span>';
			}
		}

		/*
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
		*/
	}
}
?>