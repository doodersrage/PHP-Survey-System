<?php
require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

// attache login script
require('client-top.php');

// add or modify user
if ($_POST['submit_form'] == 1) {
set_vars();
$form_result = chk_form();

if ($form_result == 1) {
$page_content = '<div class="warning">There appears to be missing information in your form submission. <br> Please review and try again.</div>';
$page_content .= '';
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

// update existing client
$modify_query = "UPDATE client_info SET contact_salutation=?, contact_name=?, company=?, address1=?, address2=?, city=?, state=?, zip=?, phone_number=?, fax_number=?, email_address=?, modified=NOW(), psk=? WHERE client_id = '".$_SESSION['client_id']."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$client_info);
$page_content = '<div class="warning">Client Information has been modified.</div>';

$update_success = 1;

}

}

// get client information
function get_client_info() {
global $db;
$modify_query = "SELECT contact_salutation, contact_name, company, address1, address2, city, state, zip, phone_number, fax_number, email_address, psk, client_id FROM client_info WHERE client_id = '".$_SESSION['client_id']."';";
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
$page_content .= '<tr><td class="required_input_title">Tracking Number:</td><td class="input_field">'.$GLOBALS['psk'].'<input name="psk" type="hidden" value="'.$GLOBALS['psk'].'"><input name="submit_form" type="hidden" value="1">'.(!empty($GLOBALS['client_id']) ? '<input name="client_id" type="hidden" value="'.$GLOBALS['client_id'].'">' : '').'</td></tr>' . "\n";

return $page_content;
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
    <td class="left_column" valign="center">
	<?PHP
	
	if ($update_success != 1) {
	get_client_info();
	
	$page_content .= '<div class="manager_form" align="center">' . "\n";
	$page_content .= 'Please fill in user information to create account:<br>' . "\n";
	$page_content .= '* Indicates a required field' . "\n";
	$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="'.$form_name.'">' . "\n";
	$page_content .= '<table cellpadding="3">' . "\n";
	$page_content .= build_user_form();
	$page_content .= '</table>' . "\n";
	$page_content .= '<br><input name="Add" value="Update" type="submit">' . "\n";
	$page_content .= '</form>' . "\n";
	$page_content .= '</div>' . "\n";
	
	echo $page_content;
	} else {
	echo '<div align="center">Your changes have been submitted.<br><a href="index.php">Go back to the survey main page.</a></div>';
	}

	?>
	</td>
  </tr>
</table>

<?php

require_once($_SERVER['DOCUMENT_ROOT']."includes/right_menu.php");
?>         
            

<?php

require_once($_SERVER['DOCUMENT_ROOT']."includes/footer.php");
?>