<?php
require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

// set vars
if (isset($_POST['selected_region'])) $_SESSION['selected_region'] = $_POST['selected_region'];
$selected_region = $_SESSION['selected_region'];
if (isset($_POST['survey_status'])) $_SESSION['survey_status'] = $_POST['survey_status'];
$survey_status = $_SESSION['survey_status'];
if (isset($_POST['search'])) $_SESSION['search'] = $_POST['search'];
$search = str_replace("'","''",$_SESSION['search']);

// region and survey status selection
if ((!empty($selected_region) && $selected_region != 'Display All') || (!empty($survey_status) && $survey_status != '0') || !empty($search)) {
$where_clause = "WHERE ";
}
if ((!empty($selected_region) && $selected_region != 'Display All')) {
$where_clause .= "ci.city = '".$selected_region."' ";
}
if ((!empty($selected_region) && $selected_region != 'Display All') && (!empty($survey_status) && $survey_status != '0')) {
$where_clause .= "AND ";
}
if (!empty($survey_status) && $survey_status != '0') {
$where_clause .= ($survey_status == 2 ? "(cf.date_completed IS NOT NULL || cf.date_completed <> '')" : "(cf.date_completed IS NULL || cf.date_completed = '')");
}
if (!empty($search) && ((!empty($survey_status) && $survey_status != '0') || (!empty($selected_region) && $selected_region != 'Display All'))) {
$where_clause .= "AND ";
}
if (!empty($search)) {
$where_clause .= "(ci.contact_name LIKE '%".$search."%' OR ci.company LIKE '%".$search."%') ";
}

// pull selected information
$modify_query = "SELECT ci.client_id, ci.company, ci.contact_name, fi.name, cf.date_completed, fi.form_id FROM client_info ci LEFT JOIN client_forms cf ON ci.client_id = cf.client_id LEFT JOIN form_info fi ON fi.form_id = cf.form_id ".$where_clause." ORDER BY ci.company ASC";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);

while ($row = $res->fetchRow()) {
$page_content .= '<tr><td>'.$row[0].'</td><td>'.$row[1].'</td><td>'.$row[2].'</td><td>'.(!empty($row[4]) ? '<a href="../clients/review_completed.php?cid='.$row[0].'&fid='.$row[5].'" target="_blank" style="color:#000000;">' : '').$row[3].(!empty($row[4]) ? '</a>' : '').'</td><td>'.(!empty($row[4]) ? $row[4] : 'N/A').'</td></tr>' . "\n";
}

// prints state select drop down
function print_survey_status_select($selected_survey_status) {

$survey_status_array = array(
'0' => 'All',
'1' => 'Incomplete',
'2' => 'Complete'
);

$select_box = "<select name=\"survey_status\" limit=\"7\" onChange=\"this.form.submit();\">";
foreach ($survey_status_array as $ini => $name) {
$select_box .= "<option value=\"".$ini."\" ".($selected_survey_status == $ini ? "selected" : ""). ">".$name."</option> \r\n";
}
$select_box .= "</select>";

return $select_box;
}

// prints state select drop down
function print_region_select($selected_region) {
global $db;

$modify_query = "SELECT DISTINCT city FROM client_info ORDER BY city ASC";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);

$region_array = array();
while ($row = $res->fetchRow()) {
$region_array[] = $row[0];
}

$select_box = "<select name=\"selected_region\" limit=\"7\" onChange=\"this.form.submit();\">
				<option>Display All</option>";
foreach ($region_array as $name) {
$select_box .= "<option ".($selected_region == $name ? "selected" : ""). ">".$name."</option> \r\n";
}
$select_box .= "</select>";

return $select_box;
}



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
<form action="<?PHP echo $_SERVER['PHP_SELF']; ?>" method="post" name="results_filter">
<table width="100%" border="0" cellpadding="2" class="filter_table">
  <tr>
    <th colspan="5">Filter Results:<br>
	<input name="search" type="text"><input name="Submit" value="Search" type="Submit">
	</th>
  </tr>
  <tr>
    <th>Completed Survey: <?PHP echo print_survey_status_select($survey_status); ?></th>
    <th>Region: <?PHP echo print_region_select($selected_region); ?></th>
    <th>Grant Areas:</th>
  </tr>
</table>
</form>
<table width="100%" border="0" cellpadding="2" class="filter_table">
  <tr>
    <th>Client ID</th>
    <th>Company</th>
    <th>Contact Name</th>
    <th>Form Name / Results</th>
    <th>Date Completed</th>
  </tr>
	<?PHP echo $page_content; ?>
</table>
	</td>
  </tr>
</table>