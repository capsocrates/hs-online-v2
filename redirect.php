<?php
	include 'googleid.php';
     $openid = new GoogleOpenID;
     if ($openid->is_valid())
       print 'logged in as ' . $_GET['openid_ext1_value_email'];
     else
       print 'Failed validation.';
  ?>