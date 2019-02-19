<?php

/**
 * User interaction handler for  HS Online Apps
 * 
 * 
 * @author DMK, 2012-11-20
 * @NOTE:  Removed DEV from 'online-dev'!
 */
  // Turn off all error reporting
error_reporting(0);
class hsUsers
{
	/**
	 * The database object
	 * 
	 * @var object
	 */
	private $_db;

	/**
	 * Checks for a database object and creates one if none is found
	 * 
	 * @param object $db
	 * @return void
	 */
	public function __construct($db=NULL)
	{
		if(is_object($db))
		{
			$this->_db = $db;
		}
		else
		{
			$dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME;
			$this->_db = new PDO($dsn, DB_USER, DB_PASS);
			

		}
	}

	/**
	 * Checks and inserts a new account email into the database
	 * 
	 * @return string	a message indicating the action status
	 */
	public function createAccount()
	{
		$e = trim($_POST['email']);
		$u = trim($_POST['username']);
		$v = sha1(time());
		
		$sql = "SELECT COUNT(username) AS theCount
				FROM hs_users
				WHERE username=:name";
		if($stmt = $this->_db->prepare($sql)) {
			$stmt->bindParam(":name", $u, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			if($row['theCount']!=0) {
				return "<h2> Error </h2>"
					. "<p> Sorry, that name is already in use. "
					. "Please try again. </p>";
			}
			if(!$this->sendVerificationEmail($u, $v, $e)) {
				return "<h2> Error </h2>"
					. "<p> There was an error sending your"
					. " verification email. Please use a valid email address.</p>";
			}
			$stmt->closeCursor();
		}
		
		$sql = "INSERT INTO hs_users(username, email, ver_code)
				VALUES(:user, :email, :ver)";
		if($stmt = $this->_db->prepare($sql)) {
			$stmt->bindParam(":user", $u, PDO::PARAM_STR);
			$stmt->bindParam(":email", $e, PDO::PARAM_STR);
			$stmt->bindParam(":ver", $v, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			return "<h2> Success! </h2>"
					. "<p> Your account was successfully "
					. "created with the username <strong>$u</strong>."
					. " Check your email!";

		} else {
			return "<h2> Error </h2><p> Your account could not be created. "
				. "Please try again. </p>";
		}
	}

	/**
	 * Checks credentials and verifies a user account
	 * 
	 * @return array	an array containing a status code and status message
	 */
	public function verifyAccount()
	{
		$sql = "SELECT username
				FROM hs_users
				WHERE ver_code=:ver
				AND SHA1(username)=:user
				AND verified=0";

		if($stmt = $this->_db->prepare($sql))
		{
			$stmt->bindParam(':ver', $_GET['v'], PDO::PARAM_STR);
			$stmt->bindParam(':user', $_GET['e'], PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			if(isset($row['username']))
			{
				// Logs the user in if verification is successful
				$_SESSION['Username'] = $row['username'];
				$_SESSION['LoggedIn'] = 1;
			}
			else
			{
				return array(4, "<h2>Verification Error</h2>\n"
					. "<p>This account has already been verified. "
					. "Did you <a href=\"/tools/hs/online-dev/password.php\">forget "
					. "your password?</a>");
			}
			$stmt->closeCursor();

			// No error message is required if verification is successful
			return array(0, NULL);
		}
		else
		{
			return array(2, "<h2>Error</h2>\n<p>Database error.</p>");
		}
	}

	/**
	 * Changes a user's email address
	 * 
	 * @return boolean	TRUE on success and FALSE on failure
	 */
	public function updateEmail()
	{
		if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
		{
			return FALSE;
		}
		$sql = "UPDATE hs_users
				SET email=:email
				WHERE userid=:user
				LIMIT 1";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
			$stmt->bindParam(':user', $_POST['id'], PDO::PARAM_INT);
			$stmt->execute();
			$stmt->closeCursor();
	
			// Updates the session variable
			$_SESSION['Username'] = htmlentities($_POST['username'], ENT_QUOTES);
	
			return TRUE;
		}
		catch(PDOException $e)
		{
			return FALSE;
		}
	}

	/**
	 * Changes the user's password
	 * 
	 * @return boolean	TRUE on success and FALSE on failure
	 */
	public function updatePassword()
	{
		if(isset($_POST['p']) && isset($_POST['r']) && $_POST['p']==$_POST['r'])
		{
			$sql = "UPDATE hs_users
					SET password=MD5(:pass), verified=1
					WHERE ver_code=:ver
					LIMIT 1";
			try
			{
				$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(":pass", $_POST['p'], PDO::PARAM_STR);
				$stmt->bindParam(":ver", $_POST['v'], PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor();

				return TRUE;
			}
			catch(PDOException $e)
			{
				return FALSE;
			}
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * Resets a user's status to unverified and sends them an email
	 * 
	 * @return mixed	TRUE on success and a message on failure
	 */
	public function resetPassword()
	{
		list($u, $v) = $this->retrieveAccountInfo($_POST['username']);
		if(!isset($u)) { echo "No account found! <a href=\"/tools/hs/online-dev/password.php\">Try again.</a>"; return FALSE; }
		$sql = "UPDATE hs_users
				SET verified=0
				WHERE userid=:user
				AND ver_code=:ver
				LIMIT 1";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(":user", $u, PDO::PARAM_STR);
			$stmt->bindParam(":ver", $v, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
		}
		catch(PDOException $e)
		{
			return $e->getMessage();
		}

		// Send the reset email
		if(!$this->sendResetEmail($_POST['username'], $v))
		{
			return "Sending the email failed!";
		}
		return TRUE;
	}

	/**
	 * Sends an email to a user with a link to verify their new account
	 * 
	 * @param string $email	The user's email address
	 * @param string $ver	The random verification code for the user
	 * @return boolean		TRUE on successful send and FALSE on failure
	 */
	private function sendVerificationEmail($user, $ver, $email)
	{
		$e = sha1($user); // For verification purposes
		$to = trim($email);
	
		$subject = "[HS Online] Please Verify Your Account";

		$headers = <<<MESSAGE
From: HS Online <skorcard@gmail.com>
Content-Type: text/plain;
MESSAGE;

		$msg = <<<EMAIL
You have a new account at the HS Online!

To get started, please activate your account and choose a
password by following the link below.

Your username: $user

Activate your account: http://www.skorcard.net/tools/hs/online-dev/accountverify.php?v=$ver&e=$e


--
Thanks!

EMAIL;

		return mail($to, $subject, $msg, $headers);
	}

	/**
	 * Sends a link to a user that lets them reset their password
	 * 
	 * @param string $email	the user's email address
	 * @param string $ver	the user's verification code
	 * @return boolean		TRUE on success and FALSE on failure
	 */
	private function sendResetEmail($email, $ver)
	{
		$e = sha1($email); // For verification purposes
		$to = trim($email);
	
		$subject = "[HS Online] Request to Reset Your Password";

		$headers = <<<MESSAGE
From: HS Online<skorcard@gmail.com>
Content-Type: text/plain;
MESSAGE;

		$msg = <<<EMAIL
We just heard you forgot your password! To get going again,
head over to the link below and choose a new password.

Follow this link to reset your password:
http://www.skorcard.net/tools/hs/online-dev/resetpassword.php?v=$ver&e=$e


--
Thanks!

EMAIL;

		return mail($to, $subject, $msg, $headers);
	}

	/**
	 * Checks credentials and logs in the user
	 * 
	 * @return boolean	TRUE on success and FALSE on failure
	 */
	public function accountLogin()
	{
	
		$sql = "SELECT username
	    		FROM hs_users
	    		WHERE username=:user
	    		AND password=MD5(:pass)
	    		LIMIT 1";

	    try
	    {
	    	$stmt = $this->_db->prepare($sql);
	    	$stmt->bindParam(':user', $_POST['username'], PDO::PARAM_STR);
	    	$stmt->bindParam(':pass', $_POST['password'], PDO::PARAM_STR);
	    	$stmt->execute();
			
			//PHP vs 5.1.6 does not like the row Count function.  So, use fetchALL and then count the results.
			//discussion at http://stackoverflow.com/questions/883365/count-with-pdo
			//Write results to array
			$queryResult = $stmt->fetchAll();
			$arrayResult = count($queryResult);
			//print_r($result);

	    	//if($stmt->rowCount()==1)
			//Log user in if value = 1
			if($arrayResult==1)
	    	{
	    		$_SESSION['Username'] = htmlentities($_POST['username'], ENT_QUOTES);
	    		$_SESSION['LoggedIn'] = 1;
	    		return TRUE;
	    	}
	    	else
	    	{
	    		return FALSE;
	    	}
	    }
	    catch(PDOException $e)
	    {
	    	return FALSE;
	    }
	}

	/**
	 * Deletes an account and all associated lists and items
	 * 
	 * @return void
	 */
	public function deleteAccount()
	{
		if(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']==1)
		{

			// Delete the user's list(s)
			/*
			$sql = "DELETE FROM united_users
					WHERE userid=:user";
			try
			{
				$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(":user", $_POST['user-id'], PDO::PARAM_INT);
				$stmt->execute();
				$stmt->closeCursor();
			}
			catch(PDOException $e)
			{
				die($e->getMessage());
			}
			*/
			// Delete the user
			$sql = "DELETE FROM hs_users
					WHERE userid=:user
					AND username=:email";
			try
			{
				$stmt = $this->_db->prepare($sql);
				$stmt->bindParam(":user", $_POST['id'], PDO::PARAM_INT);
				$stmt->bindParam(":email", $_SESSION['Username'], PDO::PARAM_STR);
				$stmt->execute();
				$stmt->closeCursor();
			}
			catch(PDOException $e)
			{
				die($e->getMessage());
			}

			// Destroy the user's session and send to a confirmation page
			unset($_SESSION['LoggedIn'], $_SESSION['Username']);
			header("Location: /tools/hs/online-dev/gone.php");
			exit;
		}
		else
		{
			header("Location: /account.php?delete=failed");
			exit;
		}
	}

	/**
	 * Retrieves the ID and verification code for a user
	 * 
	 * @param string $user	The username to search by
	 * @return mixed		an array of info or FALSE on failure
	 */
	public function retrieveAccountInfo($user=NULL)
	{
		$user = isset($user) ? $user : $_SESSION['Username'];
		$sql = "SELECT id, ver_code
	            FROM hs_users
	            WHERE username=:user";
		try
		{
			$stmt = $this->_db->prepare($sql);
			$stmt->bindParam(':user', $user, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			$stmt->closeCursor();
			return array($row['userid'], $row['ver_code']);
		}
		catch(PDOException $e)
		{
			return FALSE;
		}
	}
		/**
	 * Grabs all Items to display on the Admin home page
	 * 
	 */
	public function gameHistory()
	{
		$sql = "SELECT b.gamename, b.gdoc, b.gametime, i.gid
						FROM hs_gamename b, hs_playergames i
						WHERE b.id = i.gid AND 
						i.pid = (
							SELECT id
							FROM hs_users
							WHERE username=:user
						) ORDER BY b.id DESC";
						
		if($stmt = $this->_db->prepare($sql)) {
			$stmt->bindParam(':user', $_SESSION['Username'], PDO::PARAM_STR);
			$stmt->execute();
			$rowCount = 0; //Count var for table row striping
			  //row striping - function to check for even or odd
			function isEven($num) 
			{
				return ($num % 2 == 0);	
			}
			// end rowstriping addition
			?>
			<table class="table2">
			<tr>
			<th>Game</th><th>Gdoc</th><th>Game ID</th><th>Timestampe</th>
			</tr>
			<?php
			while($row = $stmt->fetch())
			{				
				
			?>
					<tr <?php if (isEven($rowCount) == 1) { echo 'class="tableStripe"'; } // end rowstriping addition	?>>
						<td><?php echo $row['gamename']; ?></td>
						<td><?php echo $row['gdoc']; ?></td>
						<td><?php echo $row['gid']; ?></td>
						<td><?php echo $row['gametime']; ?></td>
					</tr>
						

			<?php
			$rowCount++;
			}
			$stmt->closeCursor();
			?>
			</table>
			<?php
		} else {
			echo "<p>Something went wrong. ", $db->error, "</p>";
		}
		
	}
}

?>