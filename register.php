<?php
include "goodstuff/functions.php";
error_reporting(0);
//session_start();
sec_session_start();
//Variable to track whether the user is playing or spectating
$errorCount = 0;

$title = "Registration";
include "goodstuff/header.php";

//Process Registration
if(isset($_POST['register'])){
	if($_POST['name'] != ""){
		$userName = trim(stripslashes(htmlspecialchars($_POST['name'])));
		$userName = trim(stripslashes(htmlspecialchars($_POST['name'])));
		$_SESSION['name'] = $userName;
		
		$v = sha1(time());
		
		$sql = "SELECT COUNT(username) AS theCount
				FROM united_users
				WHERE username=:email";
		if($stmt = $this->_db->prepare($sql)) {
			$stmt->bindParam(":email", $u, PDO::PARAM_STR);
			$stmt->execute();
			$row = $stmt->fetch();
			if($row['theCount']!=0) {
				return "<h2> Error </h2>"
					. "<p> Sorry, that email is already in use. "
					. "Please try again. </p>";
			}
			if(!$this->sendVerificationEmail($u, $v)) {
				return "<h2> Error </h2>"
					. "<p> There was an error sending your"
					. " verification email. Please "
					. "<a href=\"mailto:updates@umsystem.edu\">contact "
					. "us</a> for support. We apologize for the "
					. "inconvenience. </p>";
			}
			$stmt->closeCursor();
		}
		
		$sql = "INSERT INTO united_users(username, ver_code)
				VALUES(:email, :ver)";
		if($stmt = $this->_db->prepare($sql)) {
			$stmt->bindParam(":email", $u, PDO::PARAM_STR);
			$stmt->bindParam(":ver", $v, PDO::PARAM_STR);
			$stmt->execute();
			$stmt->closeCursor();
			return "<h2> Success! </h2>"
					. "<p> Your account was successfully "
					. "created with the username <strong>$u</strong>."
					. " Check your email!";

		} else {
			return "<h2> Error </h2><p> Your account could not be created. "
				. "Please try again, or <a href=\"mailto:updates@umsystem.edu\">contact us</a>. </p>";
		}
		
		
		
		
		
		
		
		
		
	} else {
		echo '<span class="error">Please type in a name</span>';
	}

} else {
?><div id="stylized" class="myform">
	<form id="form" name="form" method="post" action="index.php">
	<h1>HS Online App</h1>
	<p>Registration Information</p>

	<label> Player Name:
	<span class="small">Enter your online handle</span>
	</label>
	<input type="text" name="name" id="name" />
	
	<label> Email Address:
	<span class="small">The address that you will use for registration</span>
	</label>
	<input type="text" name="email" id="email" />
	
	<label>Password:
	<span class="small">Enter password</span>
	</label>
	<input type="text" name="password" id="password" />
	
	<label>Retype Password:
	<span class="small">Enter password again</span>
	</label>
	<input type="text" name="password2" id="password2" />

	<input type="submit" name="register" id="register" value="Register" />
	<div class="spacer"></div>

	</form>
	</div>
<?php
}	
include "goodstuff/footer.php";
?>
