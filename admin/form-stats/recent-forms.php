<?php
require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

$get_date = mktime()-2592000;

// pull selected information
$modify_query = "SELECT ci.client_id, ci.company, ci.contact_name, fi.name, cf.date_completed, fi.form_id FROM client_info ci LEFT JOIN client_forms cf ON ci.client_id = cf.client_id LEFT JOIN form_info fi ON fi.form_id = cf.form_id WHERE cf.date_completed > '".$get_date."' ORDER BY ci.company ASC";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);

while ($row = $res->fetchRow()) {
$page_content .= '<tr><td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td><td>'.(!empty($row[4]) ? '<a href="../clients/review_completed.php?cid='.$row[0].'&fid='.$row[5].'" target="_blank" style="color:#000000;">' : '').$row[3].(!empty($row[4]) ? '</a>' : '').'</td><td>'.(!empty($row[4]) ? $row[4] : 'N/A').'</td></tr>' . "\n";
}
if (empty($page_content)) $page_content = '<tr><td colspan="5" align="center">No form filling found within the last 30 days.</td></tr>' . "\n";

require_once($_SERVER['DOCUMENT_ROOT']."includes/header-survey.php");
?>
<link rel="stylesheet" type="text/css" href="../styling.css">

<div class="header">Form Stats - Filtered Forms</div>

<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" class="admin_table">
  <tr>
    <td class="left_column" valign="top">
	<?PHP require(SURVERY_ADMIN_ROOT . 'includes/menu.php'); ?>
	</td>
    <td class="right_column">
<table width="100%" border="0" cellpadding="2" class="filter_table">
  <tr>
    <th>Client ID</th>
    <th>Company</th>
    <th>Contact Name</th>
    <th>Form Name</th>
    <th>Date Completed</th>
  </tr>
	<?PHP echo $page_content; ?>
</table>
	</td>
  </tr>
</table>