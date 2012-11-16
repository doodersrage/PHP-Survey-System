<?php
require_once("admin/includes/application_top.php");

require_once("../includes/header.php");
?>
<link rel="stylesheet" type="text/css" href="styling.css">

<!-- begin page content -->
<table border="0" cellpadding="0" cellspacing="0">
  <tbody><tr>
    <td class="left_border"></td>
        
    <td align="center" class="content">
<div class="login_form">Enter your username and password to signin.
  <form action="" method="get" name="loginform">
  <span style="color: #666666"><strong>Username:</strong></span>
  <input name="user_name" type="text" size="20">
  <br>
  <span style="color: #666666"><strong>Password:</strong></span>
  <input name="password" type="password" size="20">
  <br>
<input name="login_submit" type="hidden" value="1">
<input name="Login" type="submit" value="Login" class="login_button">
</form>
</div>
<p>&nbsp;</p>
<p><a href="index.php">BACK TO CLIENT LOGIN</a></p>
<?php

require_once("../includes/right_menu.php");
?>         
            

<?php

require_once("../includes/footer.php");
?>