<?php
require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

// attache login script
require('client-top.php');

$form_id = $_POST['form_id'];


if (!empty($form_id)) {
// check for client form assignment
$client_form_assign = mysql_query("SELECT client_id FROM client_forms WHERE client_id = '".$_SESSION['client_id']."' AND form_id = '".$form_id."';");
$client_form_assign_count = mysql_num_rows($client_form_assign);

if ($client_form_assign_count == 0) {

// form information not found for client

} else {
// load form information
$modify_query = "SELECT name, description, enabled FROM form_info WHERE form_id = '".$form_id."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$row = $res->fetchRow();

if ($row[2] == 0) {

// survey has been disabled

} else {
$form_name = $row[0];
$form_description = $row[1];

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

$name = trim(strtolower(str_replace(" ","_",$name)));
// pull field post value
$current_field_val = $_POST[$name][$question_id];

if ($field_type == 'checkbox') {

foreach($current_field_val as $checkbox_val) {
// insert post value into results table
$insert_vars = array($form_id,$row_questions_field[0],$question_id,$_SESSION['client_id'],$checkbox_val);
print_r($insert_vars);
$insert_query = "INSERT INTO form_results (form_id,field_id,question_id,client_id,result) VALUES (?,?,?,?,?);";
$sth = $db->prepare($insert_query);
$res_insert = $db->execute($sth,$insert_vars);
}

} else {
// insert post value into results table
$insert_vars = array($form_id,$row_questions_field[0],$question_id,$_SESSION['client_id'],$current_field_val);
print_r($insert_vars);
$insert_query = "INSERT INTO form_results (form_id,field_id,question_id,client_id,result) VALUES (?,?,?,?,?);";
$sth = $db->prepare($insert_query);
$res_insert = $db->execute($sth,$insert_vars);
}

}

}
}

mysql_query("UPDATE client_forms SET date_completed = NOW() WHERE form_id = '".$form_id."';");
$form_output = '<div align="center">Thank you for completing the '.$form_name.' form. <br> <a href="index.php">Go back to account page.</a></div>';
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
<?PHP echo $form_output; ?>
</td>
  </tr>
</table>

<?php

require_once($_SERVER['DOCUMENT_ROOT']."includes/right_menu.php");
?>         
            

<?php

require_once($_SERVER['DOCUMENT_ROOT']."includes/footer.php");
?>