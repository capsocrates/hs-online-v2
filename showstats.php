<?php
include "goodstuff/functions.php";
sec_session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>HS Online - Stats</title>
	<link type="text/css" rel="stylesheet" href="css/style.css" />

	</head>
	<body>
	<div id="embedPageDiv">
	<?php
	if(isset($_SESSION['name'])){
		if(isset($_POST['showstats'])){
	//UPDATE THIS PAGE TO EITHER PDO OR MYSQLI!!
	//Display Stats for the Game Sessions
				$gameName = htmlspecialchars($_SESSION['gamename']);
				$userName = trim(stripslashes(htmlspecialchars($_SESSION['name'])));
				include ("goodstuff/connections.php");
				// check connection
				if (mysqli_connect_errno()) {
				  exit('Connect failed: '. mysqli_connect_error());
				}

				// SELECT sql query
				$sql = "select username, skulls, shields, blanks, dskulls, dshields, dblanks, totalclock from hs_rolls where gamename = '$gameName'";

				// perform the query and store the result
				$result = $conn->query($sql);

				// if the $result contains at least one row
				if ($result->num_rows > 0) {
				  // output data of each row from $result
				  while($row = $result->fetch_assoc()) {
							$askulls = $row['skulls'];
							$ashields = $row['shields'];
							$ablanks = $row['blanks'];
							$dskulls = $row['dskulls'];
							$dshields = $row['dshields'];
							$dblanks = $row['dblanks'];
							$totalClock = $row['totalclock'];
							//Get the clock use in a human readable format:
							if($totalClock == 0) {
								$clockUse = "NA";
							} else {
								$clockUse = displayTotalTime($totalClock);
							}
							$totalDice = $askulls + $ashields + $ablanks + $dskulls + $dshields + $dblanks;
							$attackDice = $askulls + $ashields + $ablanks;
							$defenseDice = $dskulls + $dshields + $dblanks;
							echo "<h4>".htmlspecialchars_decode($row['username'])."</h4><p>Attack rolls: ".$askulls." out of ".$attackDice." (".getPercent($askulls,$attackDice, 2)."%)<br />Defense rolls: ".$dshields." out of ".$defenseDice." (".getPercent($dshields,$defenseDice, 2)."%)<br />Total dice rolled: ".$totalDice."<br />Time used: ".$clockUse."<br /><br />";
				  }
				  echo "<a target=\"_blank\" href=\"detailedstats.php?log=".$gameName."\">[View a detailed report]</a></p>";
				} else {
				  echo "<p>There are no stats for this game.</p>";
				}

				$conn->close();

		}		
	}
	?>
		<form action="showstats.php" method="post">
			<button type="submit" name="showstats" class="adicebutton" id="showstats" value="View Game Stats">View Game Stats</button>
		</form>
</div>	
</body>
</html>

