<?php
include_once "goodstuff/base.php";
include "goodstuff/functions.php";
error_reporting(0);
//session_start();
sec_session_start();
$title = "Registration";
include "goodstuff/header.php";

 
    if(!empty($_POST['username'])):
        include_once "goodstuff/class.users.inc.php";
        $users = new hsUsers($db);
        echo $users->createAccount();
    else:
?>
 
<div id="stylized" class="myform">
	<form method="post" action="signup.php" id="registerform">
	<h1>HS Online App</h1>
	<p>Registration Information</p>

	<label> Player Name:
	<span class="small">Enter your online handle</span>
	</label>
	<input type="text" name="username" id="username" />
	
	<label> Email Address:
	<span class="small">The address that you will use for registration</span>
	</label>
	<input type="text" name="email" id="email" />
	
	<label>Password:
	<span class="small">Enter password</span>
	</label>
	<input type="password" name="password" id="password" />
	
	<label>Retype Password:
	<span class="small">Enter password again</span>
	</label>
	<input type="password" name="password2" id="password2" />

	<input type="submit" name="register" id="register" value="Register" />
	<div class="spacer"></div>

	</form>
</div>
 
<?php
    endif;
    include_once "goodstuff/footer.php";
?>