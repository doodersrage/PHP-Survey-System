<?php

require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

$page = $_GET['page'];

// form handling

// add or modify form
if ($_POST['submit_form'] == 1) {
set_vars();
$form_result = chk_form();

if ($form_result == 1) {
$page_content = '<div class="warning">There appears to be missing information in your form submission. <br> Please review and try again.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
} else {
$client_info = array($GLOBALS['field_type'],
$GLOBALS['field_length'],
$GLOBALS['max_chars'],
$GLOBALS['required'],
$GLOBALS['default_value'],
$GLOBALS['description'],
$GLOBALS['field_columns'],
$GLOBALS['name'],
$GLOBALS['type_values']
					);

if (empty($_POST['field_id'])) {
// write new client info to database
$modify_query = "INSERT INTO form_fields (field_type,field_length,max_chars,required,default_value,description,field_columns,name,type_values) VALUES (?,?,?,?,?,?,?,?,?);";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$client_info);
$page_content = '<div class="warning">New form has been added.</div>';
} else {
// update existing client
$modify_query = "UPDATE form_fields SET field_type=? ,field_length=? ,max_chars=? ,required=? ,default_value=? ,description=? ,field_columns=?, name=?, type_values=? WHERE field_id = '".$_POST['field_id']."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$client_info);
$page_content = '<div class="warning">Field information has been modified.</div>';
}

}

}

// get client information
function get_form_info() {
global $db;
$modify_query = "SELECT field_id, field_type, field_length, max_chars, required, default_value, description, field_columns, name, type_values FROM form_fields WHERE field_id = '".$_POST['modify_list']."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$row = $res->fetchRow();

$GLOBALS['field_id'] = $row[0];
$GLOBALS['field_type'] = $row[1];
$GLOBALS['field_length'] = $row[2];
$GLOBALS['max_chars'] = $row[3];
$GLOBALS['required'] = $row[4];
$GLOBALS['default_value'] = $row[5];
$GLOBALS['description'] = $row[6];
$GLOBALS['field_columns'] = $row[7];
$GLOBALS['name'] = $row[8];
$GLOBALS['type_values'] = $row[9];
}

// set post vars
function set_vars() {
$GLOBALS['field_type'] = $_POST['field_type'];
$GLOBALS['field_length'] = $_POST['field_length'];
$GLOBALS['max_chars'] = $_POST['max_chars'];
$GLOBALS['required'] = $_POST['required'];
$GLOBALS['default_value'] = $_POST['default_value'];
$GLOBALS['description'] = $_POST['description'];
$GLOBALS['field_columns'] = $_POST['field_columns'];
$GLOBALS['name'] = $_POST['name'];
$GLOBALS['type_values'] = $_POST['type_values'];
}

// check field form errors
function chk_form() {
$form_errors = 0;

if (empty($GLOBALS['name'])) {
$form_errors = 1;
}

return $form_errors;
}

// delete form
if ($_POST['Delete']) {

if (!empty($_POST['modify_list'])) {
$delete_client = $_POST['modify_list'];
$modify_query = "DELETE FROM form_fields WHERE field_id = '".$delete_client."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$page_content = '<div class="warning">Form has been deleted.</div>';
} else {
$page_content = '<div class="warning">You must first select a field before you can delete it.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
}
}

// modify form
if ($_POST['Edit']) {

$page = 'add';

if (empty($_POST['modify_list'])) {
$page_content = '<div class="warning">You must first select a field before you can edit its details.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
}

}

function build_field_form() {

$page_content = '<tr><td class="required_input_title">Name*:</td><td class="input_field"><input name="name" type="text" size="20" value="'.$GLOBALS['name'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">Type*:</td><td class="input_field">'.build_field_type_dd('field_type',$GLOBALS['field_type']).'</td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Required:</td><td class="input_field">'.build_yn('required',$GLOBALS['required']).'</td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Value:<br>For Multiple Enter One Per Line<br>EX: test 1<br>test 2<br>test 3</td><td class="default_value"><textarea name="type_values" cols="30" rows="5">'.$GLOBALS['type_values'].'</textarea></td></tr>' . "\n";
$page_content .= '<tr><td class="input_link" colspan="2">Text Boxes Only</td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Max Characters:<br>NULL or 0=unlimited</td><td class="input_field"><input name="max_chars" type="text" size="20" value="'.$GLOBALS['max_chars'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="input_link" colspan="2">Text Areas Only</td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Columns:</td><td class="input_field"><input name="field_columns" type="text" size="20" value="'.$GLOBALS['field_columns'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="input_link" colspan="2">Text Areas and Text Boxes Only</td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Length:</td><td class="input_field"><input name="field_length" type="text" size="20" value="'.$GLOBALS['field_length'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="input_link" colspan="2">Other Settings</td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Description:<br>Displayed next to field. <br>Leave blank for no description.</td><td class="input_field"><textarea name="description" cols="30" rows="5">'.$GLOBALS['description'].'</textarea><input name="submit_form" type="hidden" value="1">'.(!empty($GLOBALS['field_id']) ? '<input name="field_id" type="hidden" value="'.$GLOBALS['field_id'].'">' : '').'</td></tr>' . "\n";

return $page_content;
}

// all form drawing below
function populate_form_select() {
global $db;
// get list of available forms
$modify_query = "SELECT field_id, name FROM form_fields ".(!empty($_POST['client_seach_val']) ? "WHERE description LIKE '%".$_POST['client_seach_val']."%' || name LIKE '%".$_POST['client_seach_val']."%' " : "")."ORDER BY description ASC";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);

while ($row = $res->fetchRow()) {
$page_content .= '<option value="'.$row[0].'">'.$row[1].'</option>' . "\n";
}

return $page_content;
}

// add new form
if ($page == 'add') {

if (!empty($_POST['Edit'])) get_form_info();

$form_name = 'add_form';

$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Please fill in form information to create new field<br>' . "\n";
$page_content .= '* Indicates a required field' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="'.$form_name.'">' . "\n";
$page_content .= '<table cellpadding="3">' . "\n";
$page_content .= build_field_form();
$page_content .= '</table>' . "\n";
$page_content .= '<br><input name="Add" value="'.(!empty($GLOBALS['field_id']) ? 'Update' : 'Add').'" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

// modify existing forms
} elseif ($page == 'modify') {

$form_name = 'modify_form';

// draw modify form form
$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Select an existing field to modify:' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="'.$form_name.'">' . "\n";
$page_content .= '<input name="modify_form" type="hidden" value="1">' . "\n";

$page_content .= '<select name="modify_list" size="10">' . "\n";

$page_content .= populate_form_select();

$page_content .= '</select>' . "\n";
$page_content .= '<br><input name="Delete" value="Delete" type="submit"><input name="Edit" value="Edit" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

// delete existing form
} elseif ($page == 'delete') {

// draw delete form form
$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Select an existing field to delete:' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="modify_form">' . "\n";
$page_content .= '<input name="delete_form" type="hidden" value="1">' . "\n";

$page_content .= '<select name="delete_list" size="10">' . "\n";

$page_content .= populate_form_select();

$page_content .= '</select>' . "\n";
$page_content .= '<br><input name="Delete" value="Delete" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

} elseif ($page == 'search') {

// draw delete form form
$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Enter a value to search the field database:' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '?page=modify" method="post" name="client_search">' . "\n";

$page_content .= '<input name="client_seach_val" type="text" value="'.$_POST['client_seach_val'].'">' . "\n";

$page_content .= '<br><input name="Search" value="Search" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

}

require_once($_SERVER['DOCUMENT_ROOT']."includes/header.php");
?>
<link rel="stylesheet" type="text/css" href="../styling.css">

<div class="header">Configure Fields</div>

<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" class="admin_table">
  <tr>
    <td class="left_column" valign="top">
	<?PHP require(SURVERY_ADMIN_ROOT . 'includes/menu.php'); ?>
	</td>
    <td class="right_column">
	<?PHP echo $page_content; ?>
	</td>
  </tr>
</table>