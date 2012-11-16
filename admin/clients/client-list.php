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
$page_content .= '<tr><td class="input_options_cnt">'.$client_num.'</td><td class="input_options">'.$row[0].'</td><td class="input_options">'.$row[1].'</td><td class="input_options">'.$row[2].'</td><td class="input_options">';

if (!empty($row[3])) {
$modify_query = "SELECT fi.form_id, fi.name FROM form_info fi LEFT JOIN client_forms cf ON fi.form_id = cf.form_id WHERE cf.client_id = '".$row[3]."' ORDER BY fi.name ASC";
$sth = $db->prepare($modify_query);
$res_form = $db->execute($sth);
$page_content .= '<table align="right">';
while ($form_row = $res_form->fetchRow()) {
$page_content .= '<tr><td align="right" style="border-bottom:1px solid #000"><a href="client-pdf.php?cid='.$row[3].'&fid='.$form_row[0].'" target="_blank" style="text-decoration:none; color:#000000;">'.$form_row[1].'</a></td>';
if ($printer_friendly != 1) $page_content .= '<td style="border-bottom:1px solid #000"><input name="print_letter[]" type="checkbox" value="'.$row[3].'-'.$form_row[0].'"></td>';
$page_content .= '</tr>';
}
$page_content .= '</table>';
} else {
$page_content .= '&nbsp;';
}

$page_content .= '</td>';

if ($printer_friendly != 1) {
$page_content .= '<td class="input_options">';
if (!empty($row[3])) {
$modify_query = "SELECT fi.form_id, fi.name FROM form_info fi LEFT JOIN client_forms cf ON fi.form_id = cf.form_id WHERE cf.client_id = '".$row[3]."' AND (cf.date_completed IS NOT NULL OR cf.date_completed <> '') ORDER BY fi.name ASC";
$sth = $db->prepare($modify_query);
$res_form = $db->execute($sth);

while ($form_row = $res_form->fetchRow()) {
$page_content .= '<a href="review_completed.php?cid='.$row[3].'&fid='.$form_row[0].'" target="_blank" style="color:#000000;">'.$form_row[1].'</a><br>';
}
} else {
$page_content .= '&nbsp;';
}
$page_content .= '</td>';
}

$page_content .= ($printer_friendly != 1 ? '<td class="input_options"><a style="color:#000000;" href="client-manager.php?page=add&edit='.$row[3].'">EDIT</a> / <a style="color:#000000;" href="?delete='.$row[3].'">DELETE</a></td>' : '').'</tr>' . "\n";
}

// get form list
$modify_query = "SELECT fi.form_id, fi.name FROM form_info fi ORDER BY fi.name ASC";
$sth = $db->prepare($modify_query);
$res_form_all = $db->execute($sth);

while ($form_row_all = $res_form_all->fetchRow()) {
$forms_all .= '<a style="color:#000000;" href="client-all-pdf.php?formid='.$form_row_all[0].'" target="_blank">'.$form_row_all[1].'</a><br>';
}

if ($printer_friendly != 1) {
require_once($_SERVER['DOCUMENT_ROOT']."includes/header.php");
?>

<div class="header">Clients Index</div>

<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" class="admin_table">
  <tr>
    <td class="left_column" valign="top">
	<?PHP require(SURVERY_ADMIN_ROOT . 'includes/menu.php'); ?>
	</td>
    <td class="right_column">
<form target="_blank" action="client-pdf-select.php" method="post"><?PHP }	?>
<link rel="stylesheet" type="text/css" href="../styling.css">
	<table>
	<tr><td class="input_link">#</td><td class="input_link">Company</td><td class="input_link">Contact Name</td><td class="input_link">Tracking Number</td><td class="input_link" width="95">Client Letter</td><?PHP if ($printer_friendly != 1) { ?><td class="input_link">Survey Results</td><td class="input_link">Action</td><?PHP } ?></tr>
	<?PHP echo $page_content; ?>
	<?PHP if ($printer_friendly != 1) { ?><tr><td class="input_link" colspan="3">Print Letter for All Clients:</td><td class="input_link" colspan="2"><?PHP echo $forms_all; ?></td><td class="input_link" colspan="2"></td></tr><?PHP } ?>
	</table>
	<?PHP if ($printer_friendly != 1) { ?>
<div align="center"><input name="Submit" type="submit" value="View Selected Letters"></div>
</form>	
<div align="center"><a href="?printer=1" target="_blank" style="color:#000000;">Printer Friendly View</a></div>
	</td>
  </tr>
</table>
<?PHP }	?>