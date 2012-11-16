<?php
require_once("admin/includes/application_top.php");

switch ($_GET['error']) {
case 'incorrectlogin':
$error_message = "<p>The login information that you have provided does not appear to be correct. Please try again.</p>";
break;
}

require_once("../includes/header.php");
?>
<link rel="stylesheet" type="text/css" href="styling.css">

<!-- begin page content -->
<table border="0" cellpadding="0" cellspacing="0">
  <tbody><tr>
    <td class="left_border"></td>
        
    <td align="center" class="content">
<div class="login_form">
<?PHP echo $error_message; ?>
Enter your tracking number to sign in.
<form action="client/" method="post" name="loginform">
  <span style="color: #666666"><strong>Tracking Number:</strong></span>
  <input name="track_number" type="text" size="20" maxlength="20">
  <br>
<input name="login_submit" type="hidden" value="1">
<input name="Login" type="submit" value="Login" class="login_button">
</form>
</div>
<p>&nbsp;</p>
<p><a href="admin/">ADMIN</a></p>
<?php

require_once("../includes/right_menu.php");
?>         
            

<?php

require_once("../includes/footer.php");
?>