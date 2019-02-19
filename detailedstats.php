<?php
include "goodstuff/functions.php";
sec_session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
<title>Detailed Game Stats - HS Online</title>
<link type="text/css" rel="stylesheet" href="style.css" />
<style type="text/css">
#frame {
	padding: 20px;
}
#frame h1 {
 font: bold 15px "Trebuchet MS", Verdana, Arial, Helvetica, sans-serif;
 color: #6D929B;
}
table
{
border-collapse:collapse;
}
table,th, td
{
border: 1px solid black;
}
th {
	font: bold 11px "Trebuchet MS", Verdana, Arial, Helvetica,
	sans-serif;
	color: #6D929B;
	border-right: 1px solid #C1DAD7;
	border-bottom: 1px solid #C1DAD7;
	border-top: 1px solid #C1DAD7;
	letter-spacing: 2px;
	text-transform: uppercase;
	text-align: left;
	padding: 6px 6px 6px 12px;
	background: #ebf4fb;
}
td {
	border-right: 1px solid #C1DAD7;
	border-bottom: 1px solid #C1DAD7;
	background: #fff;
	padding: 6px 6px 6px 12px;
	color: #6D929B;
}


td.alt {
	background: #F5FAFA;
	color: #B4AA9D;
}
.bigName {
	font-weight:bold;
	color:#398597;
}
</style>
</head>
<body>
<div id="frame">
<?php
$log = trim(htmlspecialchars($_GET['log'])); 
if($log != ''){

	$gameName = $log;
	//$userName = trim(stripslashes(htmlspecialchars($_SESSION['name'])));

	
	include ("goodstuff/connections.php");

				if (mysqli_connect_errno()) {
				  exit('Connect failed: '. mysqli_connect_error());
				}

				// SELECT sql query
				$sql = "select gamename, username, action, skulls, shields, blanks, totals, d20, gametime from hs_rolls_itemized where gamename = '$gameName' ORDER BY username ASC, gametime ASC";

				// perform the query and store the result
				$result = $conn->query($sql);

				// if the $result contains at least one row
				if ($result->num_rows > 0) {
				echo "<h1>HS Online: detailed stats for <a href=\"$gameName\">$gameName</a></h1>";
				echo "<table><tr><th>Player</th><th>Action</th><th>Skulls</th><th>Shields</th><th>blanks</th><th>d20</th><th>Total</th><th>Time</th></tr>";
				  // output data of each row from $result
				  	$action = '';
					$playerName = '';
					$i = 1;

				  while($row = $result->fetch_assoc()) {
							$skulls = $row['skulls'];
							$shields = $row['shields'];
							$blanks = $row['blanks'];
							$d20 = $row['d20'];
							$total = $row['totals'];

							if($row['action'] == 1) {
								$action = "Attack";
								$shields = '-';
								$d20 = '-';
								
							}
							if($row['action'] == 2) {
								$action = "Defend";
								$skulls = '-';
								$d20 = '-';
							}
							if($row['action'] == 3) {
								$action = "d20-Init";
								$skulls = '-';
								$blanks = '-';
								$total = '-';
							}
							if($row['action'] == 0) {
								$action = "d20";
								$skulls = '-';
								$shields = '-';
								$blanks = '-';
								$total = '-';
							}
							if($playerName != $row['username']) {
								$newName = "<td class='bigName'>".htmlspecialchars_decode($row['username'])."</td>";
							} else {
								$newName = '<td></td>';
							}
							$playerName = htmlspecialchars_decode($row['username']);	

							echo "<tr>$newName<td>".$action."</td><td>$skulls</td><td>$shields</td><td>$blanks</td><td>$d20</td><td>$total</td><td>".$row['gametime']."</td></tr>";

				  }
				}
				else {
				  echo "<p>There are no stats for this game.</p>";
				}
				echo "</table>";

			$conn->close();
}

?>
</div>
</body>
</html>	