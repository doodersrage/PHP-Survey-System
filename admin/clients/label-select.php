<?php

require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

// vars
$printer_friendly = $_GET['printer'];
$delete = $_GET['delete'];

if (!empty($delete)) {
delete_client($delete_client);
}

$modify_query = "SELECT company, contact_name, psk, client_id FROM client_info ".(!empty($_POST['client_seach_val']) ? "WHERE contact_name LIKE '%".$_POST['client_seach_val']."%' || company LIKE '%".$_POST['client_seach_val']."%' || email_address LIKE '%".$_POST['client_seach_val']."%' " : "")."ORDER BY company ASC";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);

$client_num = 0;

while ($row = $res->fetchRow()) {
$client_num++;
$page_content .= '<tr><td class="input_options_cnt">'.$client_num.'</td><td class="input_options">'.$row[0].'</td><td class="input_options">'.$row[1].'</td><td class="input_options" align="center"><input name="show_labels[]" type="checkbox" value="'.$row[3].'"></td></tr>';


}

require_once($_SERVER['DOCUMENT_ROOT']."includes/header.php");
?>

<div class="header">Clients Label Select</div>

<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" class="admin_table">
  <tr>
    <td class="left_column" valign="top">
	<?PHP require(SURVERY_ADMIN_ROOT . 'includes/menu.php'); ?>
	</td>
    <td class="right_column">
<link rel="stylesheet" type="text/css" href="../styling.css">
	<form  target="_blank" action="label-select-print.php" method="post">
	<table>
	<tr><td class="input_link">#</td><td class="input_link">Company</td><td class="input_link">Contact Name</td><td class="input_link">Print Label</td></tr>
	<?PHP echo $page_content; ?>
	<tr><td class="input_link" colspan="4"><input name="Submit" type="submit" value="Submit"></td></tr>
	</table>
	</form></td>
  </tr>
</table>
