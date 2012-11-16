<?php

require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");
// form handling

// vars
$page = $_GET['page'];
$edit = $_GET['edit'];
if (!empty($edit)) {
$_SESSION['refer_page'] = 'client-list.php';
} elseif (empty($edit)) {
$_SESSION['refer_page'] = 'client-manager.php?page=modify';
}

if (!empty($edit)) {
$_POST['modify_list'] = $edit;
$_POST['Edit'] = 'Edit';
}

// add or modify user
if ($_POST['submit_form'] == 1) {
set_vars();
$form_result = chk_form();

if ($form_result == 1) {
$page_content = '<div class="warning">There appears to be missing information in your form submission. <br> Please review and try again.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
} else {
$client_info = array($GLOBALS['contact_salutation'],
					$GLOBALS['contact_name'],
					$GLOBALS['company'],
					$GLOBALS['address1'],
					$GLOBALS['address2'],
					$GLOBALS['city'],
					$GLOBALS['state'],
					$GLOBALS['zip'],
					$GLOBALS['phone_number'],
					$GLOBALS['fax_number'],
					$GLOBALS['email_address'],
					$GLOBALS['psk']
					);

if (empty($_POST['client_id'])) {
// write new client info to database
$modify_query = "INSERT INTO client_info (contact_salutation,contact_name,company,address1,address2,city,state,zip,phone_number,fax_number,email_address,added,modified,psk) VALUES (?,?,?,?,?,?,?,?,?,?,?,NOW(),NOW(),?);";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$client_info);

$new_record_id = mysql_insert_id();

if (!empty($_POST['forms'])) {
foreach($_POST['forms'] as $selected_form) {
mysql_query("INSERT INTO client_forms (client_id,form_id) VALUES ('".$new_record_id."','".$selected_form."');");
}}
$page_content = '<div class="warning">New client has been added.</div>';
} else {
// update existing client
$modify_query = "UPDATE client_info SET contact_salutation=?, contact_name=?, company=?, address1=?, address2=?, city=?, state=?, zip=?, phone_number=?, fax_number=?, email_address=?, modified=NOW(), psk=? WHERE client_id = '".$_POST['client_id']."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$client_info);
//$page_content = '<div class="warning">Client Information has been modified.</div>';

if (!empty($_POST['forms'])) {
$form_count = count($_POST['forms']);
$roll_count = 0;
$in_clause = '';
foreach($_POST['forms'] as $selected_form) {
$roll_count++;
$form_check_query = mysql_query("SELECT client_id FROM client_forms WHERE client_id = '".$_POST['client_id']."' AND form_id = '".$selected_form."';");
$form_check_count = mysql_num_rows($form_check_query);
if ($form_check_count == 0) {
mysql_query("INSERT INTO client_forms (client_id,form_id) VALUES ('".$_POST['client_id']."','".$selected_form."');");
}
$in_clause .= ($roll_count != $form_count ? $selected_form . ',' : $selected_form);
}
mysql_query("DELETE FROM client_forms WHERE client_id = '".$_POST['client_id']."' AND form_id NOT IN (".$in_clause.");");
} else {
mysql_query("DELETE FROM client_forms WHERE client_id = '".$_POST['client_id']."';");
}

header("Location: ".$_SESSION['refer_page']);

}

}

}

// get client information
function get_client_info() {
global $db;
$modify_query = "SELECT contact_salutation, contact_name, company, address1, address2, city, state, zip, phone_number, fax_number, email_address, psk, client_id FROM client_info WHERE client_id = '".$_POST['modify_list']."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$row = $res->fetchRow();

$GLOBALS['contact_salutation'] = $row[0];
$GLOBALS['contact_name'] = $row[1];
$GLOBALS['company'] = $row[2];
$GLOBALS['address1'] = $row[3];
$GLOBALS['address2'] = $row[4];
$GLOBALS['city'] = $row[5];
$GLOBALS['state'] = $row[6];
$GLOBALS['zip'] = $row[7];
$GLOBALS['phone_number'] = $row[8];
$GLOBALS['fax_number'] = $row[9];
$GLOBALS['email_address'] = $row[10];
$GLOBALS['psk'] = $row[11];
$GLOBALS['client_id'] = $row[12];
}

// set post vars
function set_vars() {
$GLOBALS['contact_salutation'] = $_POST['contact_salutation'];
$GLOBALS['contact_name'] = $_POST['contact_name'];
$GLOBALS['company'] = $_POST['company'];
$GLOBALS['address1'] = $_POST['address1'];
$GLOBALS['address2'] = $_POST['address2'];
$GLOBALS['city'] = $_POST['city'];
$GLOBALS['state'] = $_POST['state'];
$GLOBALS['zip'] = $_POST['zip'];
$GLOBALS['phone_number'] = $_POST['phone_number'];
$GLOBALS['fax_number'] = $_POST['fax_number'];
$GLOBALS['email_address'] = $_POST['email_address'];
$GLOBALS['psk'] = $_POST['psk'];
}

// check form form errors
function chk_form() {
$form_errors = 0;

if (empty($GLOBALS['contact_name']) ||
empty($GLOBALS['company']) ||
empty($GLOBALS['address1']) ||
empty($GLOBALS['city']) ||
empty($GLOBALS['state']) ||
empty($GLOBALS['zip']) ||
empty($GLOBALS['phone_number'])) {
$form_errors = 1;
}

return $form_errors;
}

// delete user
if ($_POST['Delete']) {

if (!empty($_POST['modify_list'])) {
$delete_client = $_POST['modify_list'];
delete_client($delete_client);
$page_content = '<div class="warning">Client has been deleted.</div>';
} else {
$page_content = '<div class="warning">You must first select a user before you can delete them.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
}
}

// modify user
if ($_POST['Edit']) {

$page = 'add';

if (empty($_POST['modify_list'])) {
$page_content = '<div class="warning">You must first select a user before you can edit their details.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
}

}

function draw_forms_list($client_id = '') {
global $db;
$modify_query = "SELECT  form_id, name FROM form_info ORDER BY name ASC;";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
while($row = $res->fetchRow()) {
$form_chk_qry = mysql_query("SELECT client_forms_id FROM client_forms WHERE form_id = '".$row[0]."'".(!empty($client_id) ? " AND client_id = '".$client_id."'" : "").";");
$form_count = mysql_num_rows($form_chk_qry);
$forms .=  '<input name="forms[]" type="checkbox" value="'.$row[0].'" '.($form_count > 0 ? 'checked' : '').'> :' . $row[1].'<br>';
}

return $forms;
}

function build_user_form() {

if (empty($GLOBALS['psk'])) $GLOBALS['psk'] = generatePassword(10,2);

$page_content = '<tr><td class="required_input_title">Contact Salutation*:</td><td class="input_field">'.build_salutation($GLOBALS['contact_salutation']).'</td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">Contact Name*:</td><td class="input_field"><input name="contact_name" type="text" size="30" value="'.$GLOBALS['contact_name'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">Company*:</td><td class="input_field"><input name="company" type="text" size="30" value="'.$GLOBALS['company'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">Address1*:</td><td class="input_field"><input name="address1" type="text" size="30" value="'.$GLOBALS['address1'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Address2:</td><td class="input_field"><input name="address2" type="text" size="30" value="'.$GLOBALS['address2'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">City*:</td><td class="input_field"><input name="city" type="text" size="30" value="'.$GLOBALS['city'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">State*:</td><td class="input_field">'.print_state_select($GLOBALS['state']).'</td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">Zip*:</td><td class="input_field"><input name="zip" type="text" size="10" value="'.$GLOBALS['zip'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">Phone Number*:</td><td class="input_field"><input name="phone_number" type="text" size="13" maxlength="13" value="'.$GLOBALS['phone_number'].'" onclick="javascript:getIt(this)" ></td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Fax Number:</td><td class="input_field"><input name="fax_number" type="text" size="13" maxlength="13" value="'.$GLOBALS['fax_number'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Email Address:</td><td class="input_field"><input name="email_address" type="text" size="30" value="'.$GLOBALS['email_address'].'"></td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Forms:</td><td class="input_field">'.draw_forms_list($GLOBALS['client_id']).'</td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">Tracking Number:</td><td class="input_field">'.$GLOBALS['psk'].'<input name="psk" type="hidden" value="'.$GLOBALS['psk'].'"><input name="submit_form" type="hidden" value="1">'.(!empty($GLOBALS['client_id']) ? '<input name="client_id" type="hidden" value="'.$GLOBALS['client_id'].'">' : '').'</td></tr>' . "\n";

return $page_content;
}

// all form drawing below
function populate_user_select() {
global $db;
// get list of available users
$modify_query = "SELECT client_id, company, contact_name FROM client_info ".(!empty($_POST['client_seach_val']) ? "WHERE contact_name LIKE '%".$_POST['client_seach_val']."%' || company LIKE '%".$_POST['client_seach_val']."%' || email_address LIKE '%".$_POST['client_seach_val']."%' " : "")."ORDER BY company ASC";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);

while ($row = $res->fetchRow()) {
$page_content .= '<option value="'.$row[0].'">'.$row[1].' , '.$row[2].'</option>' . "\n";
}

return $page_content;
}


// add new user
if ($page == 'add') {

if ($_POST['Edit']) get_client_info();

$form_name = 'add_form';

$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Please fill in user information to create account:<br>' . "\n";
$page_content .= '* Indicates a required field' . "\n";
$page_content .= '<form action="" method="post" name="'.$form_name.'">' . "\n";
$page_content .= '<table cellpadding="3">' . "\n";
$page_content .= build_user_form();
$page_content .= '</table>' . "\n";
$page_content .= '<br><input name="Add" value="'.(!empty($GLOBALS['client_id']) ? 'Update' : 'Add').'" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

// modify existing users
} elseif ($page == 'modify') {

$form_name = 'modify_form';

// draw modify user form
$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Select an existing client to modify:' . "\n";
$page_content .= '<form action="" method="post" name="'.$form_name.'">' . "\n";
$page_content .= '<input name="modify_user" type="hidden" value="1">' . "\n";

$page_content .= '<select name="modify_list" size="10">' . "\n";

$page_content .= populate_user_select();

$page_content .= '</select>' . "\n";
$page_content .= '<br><input name="Delete" value="Delete" type="submit"><input name="Edit" value="Edit" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

// delete existing user
} elseif ($page == 'delete') {

// draw delete user form
$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Select an existing client to delete:' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="modify_form">' . "\n";
$page_content .= '<input name="delete_user" type="hidden" value="1">' . "\n";

$page_content .= '<select name="delete_list" size="10">' . "\n";

$page_content .= populate_user_select();

$page_content .= '</select>' . "\n";
$page_content .= '<br><input name="Delete" value="Delete" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

} elseif ($page == 'search') {

// draw search user form
$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Enter a value to search the client database:' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '?page=modify" method="post" name="client_search">' . "\n";

$page_content .= '<input name="client_seach_val" type="text" value="'.$_POST['client_seach_val'].'">' . "\n";

$page_content .= '<br><input name="Search" value="Search" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

}

require_once($_SERVER['DOCUMENT_ROOT']."includes/header-survey.php");
?>
<link rel="stylesheet" type="text/css" href="../styling.css">

<div class="header">Client - Manager</div>

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