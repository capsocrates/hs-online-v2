<?php
//Functions used for the HS ONLINE app.
//Last updated: 2012-11-25

//List of blacklisted IP Addresses; IPs in this list will not be able to login to the app.
$deny = array("100.80.150.1");

function sec_session_start() {
        $session_name = 'sec_session_id'; // Set a custom session name
        $secure = false; // Set to true if using https.
        $httponly = true; // This stops javascript being able to access the session id. 
 
        ini_set('session.use_only_cookies', 1); // Forces sessions to only use cookies. 
        $cookieParams = session_get_cookie_params(); // Gets current cookies params.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly); 
        session_name($session_name); // Sets the session name to the one set above.
        session_start(); // Start the php session
        //session_regenerate_id(true); // regenerated the session, delete the old one.     
}

error_reporting(0);

function getPercent($val1, $val2, $precision) 
{
	$res = round( ($val1 / $val2) * 100, $precision );
	
	return $res;
}


function loginForm(){
	echo'
	<div id="stylized" class="myform">
	<form id="form" name="form" method="post" action="index.php">
	<h1>HS Online App</h1>
	<p><a href="https://docs.google.com" target="_blank">Sign in to Google Docs</a> | <a href="faqs.php" target="_blank">FAQs</a> | <a href="http://www.heroscapers.com/community/showthread.php?t=39769" target="_blank">Tutorial</a></p>

	<label>Name:
	<span class="small">Enter player name</span>
	</label>
	<input type="text" name="name" id="name" />

	<label>Game name:
	<span class="small">Leave blank to generate a new game</span>
	</label>
	<input type="text" name="game" id="game" />

	<label>Google Doc string:
	<span class="small">Located in your Google Doc url.  You must be logged in to Google Docs in order to view the document</span>
	</label>
	<input type="text" name="gdoc" id="gdoc" />
	<button type="submit" name="enter" id="enter" value="Enter">Enter</button>
	<div class="spacer"></div>

	</form>
	<p>Best viewed in Chrome or Firefox.</p>
	</div>
	
	';
	//$currentTimeoutInSecs = ini_get('session.gc_maxlifetime');
	//echo $currentTimeoutInSecs;
}
//*************************************
function login($email, $password, $mysqli) {
   // Using prepared Statements means that SQL injection is not possible. 
   if ($stmt = $mysqli->prepare("SELECT id, username, password, salt FROM members WHERE email = ? LIMIT 1")) { 
      $stmt->bind_param('s', $email); // Bind "$email" to parameter.
      $stmt->execute(); // Execute the prepared query.
      $stmt->store_result();
      $stmt->bind_result($user_id, $username, $db_password, $salt); // get variables from result.
      $stmt->fetch();
      $password = hash('sha512', $password.$salt); // hash the password with the unique salt.
 
      if($stmt->num_rows == 1) { // If the user exists
         // We check if the account is locked from too many login attempts
         if(checkbrute($user_id, $mysqli) == true) { 
            // Account is locked
            // Send an email to user saying their account is locked
            return false;
         } else {
         if($db_password == $password) { // Check if the password in the database matches the password the user submitted. 
            // Password is correct!
 
               $ip_address = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user. 
               $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
 
               $user_id = preg_replace("/[^0-9]+/", "", $user_id); // XSS protection as we might print this value
               $_SESSION['user_id'] = $user_id; 
               $username = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $username); // XSS protection as we might print this value
               $_SESSION['username'] = $username;
               $_SESSION['login_string'] = hash('sha512', $password.$ip_address.$user_browser);
               // Login successful.
               return true;    
         } else {
            // Password is not correct
            // We record this attempt in the database
            $now = time();
            $mysqli->query("INSERT INTO login_attempts (user_id, time) VALUES ('$user_id', '$now')");
            return false;
         }
      }
      } else {
         // No user exists. 
         return false;
      }
   }
}

function getRand($val) {
	$appendRand = mt_rand(1, 9999);
	$val = $val." ".$appendRand;
	return $val;
}

function removeApost($val) {
	$val = str_replace('\'', '', $val);
	$val = str_replace('/', '', $val);
	return $val;
}

function checkbrute($user_id, $mysqli) {
   // Get timestamp of current time
   $now = time();
   // All login attempts are counted from the past 2 hours. 
   $valid_attempts = $now - (2 * 60 * 60); 
 
   if ($stmt = $mysqli->prepare("SELECT time FROM login_attempts WHERE user_id = ? AND time > '$valid_attempts'")) { 
      $stmt->bind_param('i', $user_id); 
      // Execute the prepared query.
      $stmt->execute();
      $stmt->store_result();
      // If there has been more than 5 failed logins
      if($stmt->num_rows > 5) {
         return true;
      } else {
         return false;
      }
   }
}

function login_check($mysqli) {
   // Check if all session variables are set
   if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])) {
     $user_id = $_SESSION['user_id'];
     $login_string = $_SESSION['login_string'];
     $username = $_SESSION['username'];
     $ip_address = $_SERVER['REMOTE_ADDR']; // Get the IP address of the user. 
     $user_browser = $_SERVER['HTTP_USER_AGENT']; // Get the user-agent string of the user.
 
     if ($stmt = $mysqli->prepare("SELECT password FROM hs_users WHERE id = ? LIMIT 1")) { 
        $stmt->bind_param('i', $user_id); // Bind "$user_id" to parameter.
        $stmt->execute(); // Execute the prepared query.
        $stmt->store_result();
 
        if($stmt->num_rows == 1) { // If the user exists
           $stmt->bind_result($password); // get variables from result.
           $stmt->fetch();
           $login_check = hash('sha512', $password.$ip_address.$user_browser);
           if($login_check == $login_string) {
              // Logged In!!!!
              return true;
           } else {
              // Not logged in
              return false;
           }
        } else {
            // Not logged in
            return false;
        }
     } else {
        // Not logged in
        return false;
     }
   } else {
     // Not logged in
     return false;
   }
}

//TIME TRACKING FUNCTIONS//

//Function to update the startclock column in hs_rolls.  This means that the user has performed an action that tracks time.

function updateStartclock($gameName, $userName) {


	//$curTime = time();
	$curTime= date("Y-m-d H:i:s"); 
	$query = sprintf("UPDATE hs_masterclock SET tracktime = 1, gameclock = '%s' WHERE gamename = '%s'",
			             $curTime,
						 mysql_real_escape_string($gameName));                        
						 $result = mysql_query($query);
	//Now, activate the time tracking for the player who submitted the OM:
	$query2 = sprintf("UPDATE hs_rolls SET tracktime = 1 WHERE gamename = '%s' AND username = '%s'",
						 mysql_real_escape_string($gameName),
			             addslashes(htmlspecialchars($userName)));                        
						 $result2 = mysql_query($query2);
	

}

//Function to activate the MasterClock, create entry in hs_masterclock if it does not yet exist.

function activateMasterclock($gameName) {

	$query = sprintf("SELECT gamename from hs_masterclock WHERE gamename = '%s'",
			addslashes(htmlspecialchars($gameName)));  
					$result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					
					if (mysql_num_rows($result)<1) {//Add the game to hs_masterclock
						 $query2 = sprintf("insert into hs_masterclock(gamename, tracktime) values ('%s','1')",
			             addslashes(htmlspecialchars($gameName)));                        
						 $result2 = mysql_query($query2);
					} else {
						//update the existing row to reflect the new OMs
						$query3 = sprintf("UPDATE hs_masterclock SET tracktime = 1 WHERE gamename = '%s'",
			             addslashes(htmlspecialchars($gameName)));                        
						 $result3 = mysql_query($query3);

					}

}

//Initialize MasterClock, fired off when the doc is created:

function startMasterclock($gameName){
$query = sprintf("SELECT gamename from hs_masterclock WHERE gamename = '%s'",
			addslashes(htmlspecialchars($gameName)));  
					$result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					
					if (mysql_num_rows($result)<1) {//Add the game to hs_masterclock
						 $query2 = sprintf("insert into hs_masterclock(gamename, tracktime, countInitRoll) values ('%s','1','0')",
			             addslashes(htmlspecialchars($gameName)));                        
						 $result2 = mysql_query($query2);
					}


}

//Function to enable Time Tracking on the corresponding HS_MASTERCLOCK entry.  This means that OMs have been set for the game.
function enableTracktime($gameName){
$query = sprintf("SELECT gamename from hs_masterclock WHERE gamename = '%s'",
			addslashes(htmlspecialchars($gameName)));  
					$result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					
					if (mysql_num_rows($result)>0) {//Add the game to hs_masterclock
						 $query2 = sprintf("UPDATE hs_masterclock SET tracktime = 1 WHERE gamename = '%s'",
			             addslashes(htmlspecialchars($gameName)));                        
						 $result2 = mysql_query($query2);
					}


}

//Function to reset the masterclock to 0:

function resetMasterclock($gameName) {

	$query = sprintf("SELECT gamename from hs_masterclock WHERE gamename = '%s'",
			addslashes(htmlspecialchars($gameName)));  
					$result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					
					if (mysql_num_rows($result)<1) {//Add the game to hs_masterclock
						 $query2 = sprintf("insert into hs_masterclock(gamename, countOmPlacement, countInitRoll, gameclock) values ('%s','1','0', now())",
			             addslashes(htmlspecialchars($gameName)));                        
						 $result2 = mysql_query($query2);
					} else {
						//update the existing row to reflect the new OMs
						$query3 = sprintf("UPDATE hs_masterclock SET tracktime = 1, gameclock = now(), countOmPlacement = 1, countInitRoll = 0 WHERE gamename = '%s'",
			             addslashes(htmlspecialchars($gameName)));                        
						 $result3 = mysql_query($query3);

					}

}

//Function to set countOmPlacment in hr_masterclock to T or F: vars = session/game name and 1 or 0 for var:
function countOmPlacement($gameName,$gameVar) {

	$query = sprintf("UPDATE hs_masterclock SET countOmPlacement = %s WHERE gamename = '%s'",
	mysql_real_escape_string($gameVar),                        
	mysql_real_escape_string($gameName));                        
	$result = mysql_query($query);
	if (!$result)
		echo "Could not execute query";

}

//Function to set countInitRoll in hr_masterclock to T or F: vars = session/game name and 1 or 0 for var:
function countInitRoll($gameName,$gameVar) {

	$query = sprintf("UPDATE hs_masterclock SET countInitRoll = %s WHERE gamename = '%s'",
	mysql_real_escape_string($gameVar),                        
	mysql_real_escape_string($gameName));                        
	$result = mysql_query($query);
	if (!$result)
		echo "Could not execute query";

}
//Function to set the player as active for the particular game
function setActivePlayer($gameName, $userName) {
	 $query = sprintf("UPDATE hs_rolls SET tracktime = 1 WHERE gamename = '%s' and username = '%s'",
						 mysql_real_escape_string($gameName),
                         mysql_real_escape_string($userName));
						 $result = mysql_query($query);
}
//Function to get the difference in the current timestamp and the gameclock time value, then calculate the player's new total time, and  set the gameclock to the current timestamp:
 function addPlayerTime($gameName, $userName) {
		
		 $query = sprintf("SELECT gameclock from hs_masterclock WHERE gamename = '%s' and tracktime = 1",
			addslashes(htmlspecialchars($gameName)));  
					$result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					if (mysql_num_rows($result)<1) {//Do nothing, as the entry does not exist.  This means that a non-playing user has rolled a dice.
						return;
						
					} else {
					//Grab the last gameclock time value
					$num_results = mysql_num_rows($result);
					for ($b=0; $b < $num_results; $b++)
					{
						$row = mysql_fetch_array($result);
						$gameClock =  $row["gameclock"];
					//Grab the 
					//Update the totalclock value by grabbing the startclock value (current unix timestamp), comparing it to the current time value, and then noting the difference.  Then, add the difference to the totalclock column.
					
					//NOTE that tracktime for the user MUST be set to 1, which means that they have submitted OMs and are considered as active participants in the game.

						//Select the startclock value//
					    $query2 = sprintf("SELECT totalclock from hs_rolls WHERE username = '%s' and gamename = '%s' and tracktime = 1",
			            mysql_real_escape_string($userName),
                        mysql_real_escape_string($gameName));
						$result2 = mysql_query($query2);
						$num_results2 = mysql_num_rows($result2);

						for ($bb=0; $bb < $num_results2; $bb++)
						{
							$row2 = mysql_fetch_array($result2);
							//grab the total time for the user
							$totalClock =  $row2["totalclock"];

						}
						//Get the current timestamp:
						$currentTimestamp = date("Y-m-d H:i:s"); 
					}
		
						//get the diff between the currentTimeStamp and the startClock
						
						$startClock=strtotime($gameClock);
						$currentTimestampNew=strtotime($currentTimestamp);
						$passedTime = $currentTimestampNew - $startClock;
						//Now, add this value to totalClock, which is the total time accrued by the player:
						$newTotal = $passedTime + $totalClock;
						
						//Now, update the player's total time:
						 $query3 = sprintf("UPDATE hs_rolls SET gametime = NOW(), totalclock = '%s' WHERE gamename = '%s' and username = '%s'",
			             addslashes(htmlspecialchars($newTotal)),
						 mysql_real_escape_string($gameName),
                         mysql_real_escape_string($userName));
						 $result3 = mysql_query($query3);
						 
						 //And reset the gameclock to current timestamp:
						 $query4 = sprintf("UPDATE hs_masterclock SET gameclock = '%s' WHERE gamename = '%s'",
						 $currentTimestamp,
			             addslashes(htmlspecialchars($gameName)));                        
						 $result4 = mysql_query($query4);
        } 


}


//Function to convert the player's total turn time into a readable format
function displayTotalTime($unitTotal) {

	   $h = $unitTotal / 3600 % 24;
	   $m = $unitTotal / 60 % 60; 
	   $s = $unitTotal % 60;

	   return "{$h}h, {$m}m, {$s}s";

}



//Function to get time differences between 2 timestamps; source = http://www.if-not-true-then-false.com/2010/php-calculate-real-differences-between-two-dates-or-timestamps/

 
  // Time format is UNIX timestamp or
  // PHP strtotime compatible strings
function gameTimeDiff($time1, $time2, $precision) {
	// Set timezone
	date_default_timezone_set("UTC");
    // If not numeric then convert texts to unix timestamps
    if (!is_int($time1)) {
      $time1 = strtotime($time1);
    }
    if (!is_int($time2)) {
      $time2 = strtotime($time2);
    }
 
    // If time1 is bigger than time2
    // Then swap time1 and time2
    if ($time1 > $time2) {
      $ttime = $time1;
      $time1 = $time2;
      $time2 = $ttime;
    }
 
    // Set up intervals and diffs arrays
    $intervals = array('year','month','day','hour','minute','second');
    $diffs = array();
 
    // Loop thru all intervals
    foreach ($intervals as $interval) {
      // Set default diff to 0
      $diffs[$interval] = 0;
      // Create temp time from time1 and interval
      $ttime = strtotime("+1 " . $interval, $time1);
      // Loop until temp time is smaller than time2
      while ($time2 >= $ttime) {
	$time1 = $ttime;
	$diffs[$interval]++;
	// Create new temp time from time1 and interval
	$ttime = strtotime("+1 " . $interval, $time1);
      }
    }
 
    $count = 0;
    $times = array();
    // Loop thru all diffs
    foreach ($diffs as $interval => $value) {
      // Break if we have needed precission
      if ($count >= $precision) {
	break;
      }
      // Add value and interval 
      // if value is bigger than 0
      if ($value > 0) {
	// Add s if value is not 1
	if ($value != 1) {
	  $interval .= "s";
	}
	// Add value and interval to times array
	$times[] = $value . " " . $interval;
	$count++;
      }
    }
 
    // Return string with times
    return implode(", ", $times);
 }
 
 //Function to set omtimecount and ominitcount to zero;
function numberedOmReveal($gameName) {
	$query = sprintf("UPDATE hs_masterclock SET omtimecount = 0, ominitcount = 0 WHERE gamename = '%s'",
				mysql_real_escape_string($gameName));
				$result = mysql_query($query);
}
 
 //Function to assign time to all users who have submitted OMs AFTER the first submission for the turn.
 function trackOmTime($gameName, $userName) {
		
		 $query = sprintf("SELECT omtimecount from hs_masterclock WHERE gamename = '%s' and tracktime = 1",
			addslashes(htmlspecialchars($gameName)));  
					$result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					if (mysql_num_rows($result)<1) {
						//Do nothing, since there is some sort of error.
						return;
						
					} else {
						//Grab the omtimeclock value
						$num_results = mysql_num_rows($result);
						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							$omTimeCount =  $row["omtimecount"];
						}
					}
					
					if ($omTimeCount == 0){ //since $omTimeCount is 0, increment it by 1 and then call resetMasterClock
							 $omTimeCount = 1;
							 $query2 = sprintf("UPDATE hs_masterclock SET omtimecount = 1 WHERE gamename = '%s'",
							 mysql_real_escape_string($gameName));
							 $result2 = mysql_query($query2);
							 //Set the running clock to zero:
							 resetMasterclock($gameName);
					
					} else { //omTimeCount is TRUE
							//Add playerTime to player's clock:
							addPlayerTime($gameName, $userName);
							//Set the running clock to zero:
							resetMasterclock($gameName);

								
					}		

}

 //Function to assign time to all users who have rolled Initative/Revealed OMs AFTER the first submission for the turn.
 function trackInitTime($gameName, $userName) {
		//Since OMs have been set, a related hs_masterclock row/entry should already exist.
		 $query = sprintf("SELECT ominitcount from hs_masterclock WHERE gamename = '%s' and tracktime = 1",
			addslashes(htmlspecialchars($gameName)));  
					$result = mysql_query($query);
					if (!$result)
							echo "Could not execute query";
					if (mysql_num_rows($result)<1) {  //do nothing, as there is obviously an issue.
						return;
						
					} else {
						//Grab the omtimeclock value
						$num_results = mysql_num_rows($result);
						for ($b=0; $b < $num_results; $b++)
						{
							$row = mysql_fetch_array($result);
							$omInitCount =  $row["ominitcount"];
						}
					}
					
					if ($omInitCount == 0){ //since $omTimeCount is 0, increment it by 1 and then call resetMasterClock
							 $omInitCount = 1;
							 $query2 = sprintf("UPDATE hs_masterclock SET ominitcount = 1 WHERE gamename = '%s'",
							 mysql_real_escape_string($gameName));
							 $result2 = mysql_query($query2);
							 //Set the running clock to zero:
							 resetMasterclock($gameName);
					
					} else { //omInitCount is TRUE, so assign time
						
							//Add playerTime to player's clock:
							addPlayerTime($gameName, $userName);
							//Set the running clock to zero:
							resetMasterclock($gameName);

								
					}		

}

?>