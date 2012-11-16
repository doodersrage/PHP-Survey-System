<?php
require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

// attache login script
require('client-top.php');

$modify_query = "SELECT client_id, company FROM client_info WHERE session_id = '".$_SESSION['sessionid']."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$row = $res->fetchRow();

$company = $row[1];
$client_id = $row[0];


require_once($_SERVER['DOCUMENT_ROOT']."includes/header.php");
?>
<link rel="stylesheet" type="text/css" href="../styling.css">

<!-- begin page content -->
<table border="0" cellpadding="0" cellspacing="0">
  <tbody><tr>
    <td class="left_border"></td>
        
    <td align="center" class="content">

<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" class="admin_table">
  <tr>
    <td valign="top">
	  <div align="center"><h1><strong>Welcome <?PHP echo $company; ?></strong></h1></div>
	  <div class="completed_forms">
	  <div class="section_header"><p><a href="info-update.php">Update contact information.</a></p></div></div>
	  <div class="completed_forms">
	  <div class="section_header"><p>Available Surveys:</p></div>
	  <div align="center"><?PHP 
	  $modify_query = "SELECT fi.form_id, fi.name, fi.added FROM form_info fi LEFT JOIN client_forms cf ON fi.form_id = cf.form_id WHERE cf.client_id = '".$client_id."' AND fi.enabled = 1 AND (cf.date_completed IS NULL OR cf.date_completed = '') ORDER BY fi.name ASC;";
	  $sth = $db->prepare($modify_query);
	  $res_forms = $db->execute($sth);
	  $new_survey_cnt = 0;
	  while($row_forms = $res_forms->fetchRow()) {
	  $new_survey_cnt++;
	  $new_survey_result .= '<tr><td><a href="form-view.php?form_id='.$row_forms[0].'">' . $row_forms[1] . '</a></td><td>'.date("M-d-Y",strtotime($row_completed_forms[2])).'</td></tr>';
	  }
	  if ($new_survey_cnt == 0) {
	  echo 'You have either completed all of your assigned surveys or none have been assigned to you.';
	  } else {
	  $new_survey_table = '<table class="completed_header"><tr><th><strong>Name:</strong></th><th>Date Added:</th></tr>';
	  $new_survey_table .= $new_survey_result;
	  $new_survey_table .= '</table>';
	  echo $new_survey_table;
	  }
	  ?></div></div>
	  <div class="completed_forms">
	  <div class="section_header"><p>Completed Surveys:</p></div>
	  <div align="center"><?PHP 
	  $modify_query = "SELECT fi.form_id, fi.name, cf.date_completed FROM form_info fi LEFT JOIN client_forms cf ON fi.form_id = cf.form_id WHERE cf.client_id = '".$client_id."' AND fi.enabled = 1 AND (date_completed IS NOT NULL AND date_completed <> '') ORDER BY fi.name ASC;";
	  $sth = $db->prepare($modify_query);
	  $res_completed_forms = $db->execute($sth);
	  $survey_count = 0;
	  while($row_completed_forms = $res_completed_forms->fetchRow()) {
	  $survey_count++;
	  $survey_results .= '<tr><td><strong>' . $row_completed_forms[1] . '</strong></td><td>'.date("M-d-Y",strtotime($row_completed_forms[2])).'</td><td><a href="form-details.php?form_id='.$row_completed_forms[0].'">View Results</a></td></tr>';
	  }
	  if ($survey_count == 0) {
	  echo 'You have not yet completed a survey.';
	  } else {
	  $survey_table = '<table class="completed_header"><tr><th><strong>Name:</strong></th><th>Date Completed:</th><th>Options:</th></tr>';
	  $survey_table .= $survey_results;
	  $survey_table .= '</table>';
	  echo $survey_table;
	  }
	  ?></div></div>
	  <div align="center"><p><a class="logout_link" href="?logout_user=1">Log Out</a></p></div>
	  </td>
  </tr>
</table>

<?php

require_once($_SERVER['DOCUMENT_ROOT']."includes/right_menu.php");
?>         
            

<?php

require_once($_SERVER['DOCUMENT_ROOT']."includes/footer.php");
?>