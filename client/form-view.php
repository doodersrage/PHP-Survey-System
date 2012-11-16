<?php
require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

// attache login script
require('client-top.php');

$form_id = $_GET['form_id'];

// check for client form assignment
$client_form_assign = mysql_query("SELECT client_id FROM client_forms WHERE client_id = '".$_SESSION['client_id']."' AND form_id = '".$form_id."' AND (date_completed IS NULL OR date_completed = '');");
$client_form_assign_count = mysql_num_rows($client_form_assign);

if ($client_form_assign_count == 0) {
$form_output = '<div align="center"><p><strong>We are sorry but it appears that you have not been assigned to this form or you have already filled it out in the past.</strong><br><input value="Back" type="button" onclick="Javascript: window.history.back();"></p></div>';
} else {
// load form information
$modify_query = "SELECT name, description, enabled FROM form_info WHERE form_id = '".$form_id."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$row = $res->fetchRow();

if ($row[2] == 0) {
$form_output = '<div align="center"><p><strong>We are sorry but the survey you have selected appears to be disabled.</strong><br><input value="Back" type="button" onclick="Javascript: window.history.back();"></p></div>';
} else {
$form_name = $row[0];
$form_description = $row[1];
$form_output = '<div class="form_name">'.$form_name.'</div>' . '<div class="form_description">'.$form_description.'</div>';

// get form question info
$modify_query = "SELECT question_id, question_title, question FROM form_questions WHERE form_id = '".$form_id."' AND enabled = 1 ORDER BY sort_order ASC;";
$sth = $db->prepare($modify_query);
$res_questions = $db->execute($sth);
$question_count = 0;
while($row_questions = $res_questions->fetchRow()) {
$question_id = $row_questions[0];
$question_title = $row_questions[1];
$question = $row_questions[2];

$question_count++;
$form_output .= '<div class="question_title">'.$question_count.'. '.$question_title.'</div><div class="question">'.$question.'</div>';

// get form question field data
$modify_query = "SELECT field_id FROM field_to_questions WHERE question_id = '".$question_id."';";
$sth = $db->prepare($modify_query);
$res_questions_field = $db->execute($sth);
$question_field_count = 0;
while($row_questions_field = $res_questions_field->fetchRow()) {
$question_field_count++;

// get form question field data
$modify_query = "SELECT field_type, field_length, max_chars, required, default_value, description, field_columns, name, type_values FROM form_fields WHERE field_id = '".$row_questions_field[0]."';";
$sth = $db->prepare($modify_query);
$res_field = $db->execute($sth);
$row_field = $res_field->fetchRow();

// assign field vars
$field_type = $row_field[0];
$field_length = $row_field[1];
$max_chars = $row_field[2];
$required = $row_field[3];
$default_value = $row_field[4];
$description = $row_field[5];
$field_columns = $row_field[6];
$name = $row_field[7];
$type_values = $row_field[8];

$form_output .= '<div class="question_fields">'. draw_survey_field($field_type,$name,$field_length,$max_chars,$required,$question_id,$description,$field_columns,$type_values) .'</div>';

}
if ($question_field_count == 0) $form_output .= '<div align="center"><p><strong>We are sorry but no fields have been assigned to this question.</strong></p></div>';

}
if ($question_count == 0) $form_output .= '<div align="center"><p>We are sorry but no questions have been assigned to the selected form.</p></div>'; else $form_output .= '<div align="center"><input name="Submit" type="submit" value="Submit"></div>';
}

}


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
<form action="form-check.php" method="post">
<?PHP echo $form_output; ?>
<input name="form_id" type="hidden" value="<?PHP echo $form_id; ?>">
</form>
</td>
  </tr>
</table>

<?php

require_once($_SERVER['DOCUMENT_ROOT']."includes/right_menu.php");
?>         
            

<?php

require_once($_SERVER['DOCUMENT_ROOT']."includes/footer.php");
?>