<?php
require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

// form handling

// delete user
if ($_POST['Delete']) {

if (!empty($_POST['delete_list'])) {

} else {
$page_content = '<div class="warning">You must first select a user before you can delete them.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
}
}

// modify user
if ($_POST['Edit']) {

if (!empty($_POST['modify_list'])) {

} else {
$page_content = '<div class="warning">You must first select a user before you can edit their details.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
}

}

function build_user_form() {
$page_content = '<tr><td class="required_input_title">Name*:</td><td class="input_field"><input name="name" type="text" size="20"></td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">Username*:</td><td class="input_field"><input name="username" type="text" size="20"></td></tr>' . "\n";
$page_content .= '<tr><td class="required_input_title">Password*:</td><td class="input_field"><input name="password" type="text" size="20"></td></tr>' . "\n";
$page_content .= '<tr><td class="input_title">Email:</td><td class="input_field"><input name="email" type="text" size="20"></td></tr>' . "\n";

return $page_content;
}

// all form drawing below
function populate_user_select() {
global $db;
// get list of available users
$modify_query = "SELECT users_id, name FROM admin_users";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$category);

while ($row = $res->fetchRow()) {
$page_content .= '<option value="'.$row[0].'">'.$row[1].'</option>' . "\n";
}

return $page_content;
}

$page = $_GET['page'];

// add new user
if ($page == 'add') {

$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Please fill in user information to create account:<br>' . "\n";
$page_content .= '* Indicates a required field' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="add_form">' . "\n";
$page_content .= '<table cellpadding="3">' . "\n";
$page_content .= build_user_form();
$page_content .= '</table>' . "\n";
$page_content .= '<br><input name="Add" value="Add" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

// modify existing users
} elseif ($page == 'modify') {

// draw modify user form
$page_content = '<div class="manager_form" align="center">' . "\n";
$page_content .= 'Select an existing user to modify:' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="modify_form">' . "\n";
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
$page_content .= 'Select an existing user to delete:' . "\n";
$page_content .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="modify_form">' . "\n";
$page_content .= '<input name="delete_user" type="hidden" value="1">' . "\n";

$page_content .= '<select name="delete_list" size="10">' . "\n";

$page_content .= populate_user_select();

$page_content .= '</select>' . "\n";
$page_content .= '<br><input name="Delete" value="Delete" type="submit">' . "\n";
$page_content .= '</form>' . "\n";
$page_content .= '</div>' . "\n";

}

// load  page header
require_once($_SERVER['DOCUMENT_ROOT']."includes/header.php");
?>
<link rel="stylesheet" type="text/css" href="../styling.css">
<div class="header">User Manager</div>

<table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0" class="admin_table">
  <tr>
    <td class="left_column" valign="top">
	<?PHP require(SURVERY_ADMIN_ROOT . 'includes/menu.php'); ?>
	</td>
    <td>
	<?PHP echo $page_content; ?>
	</td>
  </tr>
</table>