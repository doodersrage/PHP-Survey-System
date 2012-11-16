<?php
require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");
require_once($_SERVER['DOCUMENT_ROOT']."includes/header.php");
?>
<link rel="stylesheet" type="text/css" href="/styling.css">

<!-- begin page content -->
<table border="0" cellpadding="0" cellspacing="0">
  <tbody><tr>
    <td class="left_border"></td>
        
    <td align="center" class="content">

<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" class="admin_table">
  <tr>
    <td class="left_column" valign="top">
	<?PHP require(SURVERY_ADMIN_ROOT . 'includes/menu.php'); ?>
	</td>
    <td>&nbsp;</td>
  </tr>
</table>

<?php

require_once($_SERVER['DOCUMENT_ROOT']."includes/right_menu.php");
?>         
            

<?php

require_once($_SERVER['DOCUMENT_ROOT']."includes/footer.php");
?>