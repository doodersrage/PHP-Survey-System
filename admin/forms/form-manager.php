<?php

require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");
include_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/fckeditor/fckeditor.php") ;

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
$client_info = array($GLOBALS['name'],
					$GLOBALS['description'],
					$GLOBALS['enabled'],
					$GLOBALS['letter']
					);

if (empty($_POST['form_id'])) {
// write new client info to database
$modify_query = "INSERT INTO form_info (name,description,enabled,letter,added,modified) VALUES (?,?,?,?,NOW(),NOW());";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$client_info);

$new_record_id = mysql_insert_id();

//$page_content = '<div class="warning">New form has been added.</div>';
header("Location: form_questions.php?formid=".$new_record_id);
} else {
// update existing client
$modify_query = "UPDATE form_info SET name=?, description=?, enabled=?, letter=?, modified=NOW() WHERE form_id = '".$_POST['form_id']."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$client_info);
$page_content = '<div class="warning">Form information has been modified.</div>';
}

}

}

// get client information
function get_form_info() {
global $db;
$modify_query = "SELECT name, description, enabled, form_id, letter FROM form_info WHERE form_id = '".$_POST['modify_list']."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$row = $res->fetchRow();

$GLOBALS['name'] = $row[0];
$GLOBALS['description'] = $row[1];
$GLOBALS['enabled'] = $row[2];
$GLOBALS['form_id'] = $row[3];
$GLOBALS['letter'] = $row[4];
}

// set post vars
function set_vars() {
$GLOBALS['name'] = $_POST['name'];
$GLOBALS['description'] = $_POST['description'];
$GLOBALS['enabled'] = $_POST['enabled'];
$GLOBALS['form_id'] = $_POST['form_id'];
$GLOBALS['letter'] = $_POST['letter'];
}

// check form form errors
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
$modify_query = "DELETE FROM form_info WHERE form_id = '".$delete_client."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$page_content = '<div class="warning">Form has been deleted.</div>';
} else {
$page_content = '<div class="warning">You must first select a form before you can delete it.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
}
}

// modify form
if ($_POST['Edit']) {

$page = 'add';

if (empty($_POST['modify_list'])) {
$page_content = '<div class="warning">You must first select a form before you can edit its details.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
}

}

function build_form_form() {

$page_content = '<tr><td class="required_input_title">Form Name*:</td><td class="input_field"><input name="name" type="text" size="20" value="'.$GLOBALS['name'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">Enabled*:</td><td class="input_field">'.build_yn('enabled',$GLOBALS['enabled']).(!empty($GLOBALS['form_id']) ? '<input name="form_id" type="hidden" value="'.$GLOBALS['form_id'].'">' : '').'<input name="submit_form" type="hidden" value="1"></td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Description:</td><td class="input_field">';

$oFCKeditor = new FCKeditor('description') ;
$oFCKeditor->BasePath = '../includes/fckeditor/' ;
$oFCKeditor->Value = $GLOBALS['description'];
$page_content .= $oFCKeditor->Create();

$page_content .= '</td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Letter:</td><td class="input_field"><textarea name="letter" cols="60" rows="20">'.$GLOBALS['letter'].'</textarea></td></tr>' . "\n";

if (!empty($GLOBALS['form_id'])) {
$page_content .= '<tr><td class="input_link" colspan="2"><a href="form_questions.php?formid='.$GLOBALS['form_id'].'">Edit Questions</a></td></tr>' . "\n";
}
return $page_content;
}

// all form drawing below
function populate_form_select() {
global $db;
// get list of available forms
$modify_query = "SELECT form_id, name FROM form_info ".(!empty($_POST['client_seach_val']) ? "WHERE name LIKE '%".$_POST['client_seach_val']."%' " : "")."ORDER BY name ASC";
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
$page_content .= 'Please fill in form information to create new form<br>' . "\n";
$page_content .= '* Indicates a required field' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="'.$form_name.'">' . "\n";
$page_content .= '<table cellpadding="3" width="100%">' . "\n";
$page_content .= build_form_form();
$page_content .= '</table>' . "\n";
$page_content .= '<br><input name="Add" value="'.(!empty($GLOBALS['form_id']) ? 'Update' : 'Add').'" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

// modify existing forms
} elseif ($page == 'modify') {

$form_name = 'modify_form';

// draw modify form form
$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Select an existing form to modify:' . "\n";
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
$page_content .= 'Select an existing form to delete:' . "\n";
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
$page_content .= 'Enter a value to search the form database:' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '?page=modify" method="post" name="client_search">' . "\n";

$page_content .= '<input name="client_seach_val" type="text" value="'.$_POST['client_seach_val'].'">' . "\n";

$page_content .= '<br><input name="Search" value="Search" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

}

require_once($_SERVER['DOCUMENT_ROOT']."includes/header.php");
?>
<link rel="stylesheet" type="text/css" href="../styling.css">

<br>
<div class="header">Form - Manager</div>

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