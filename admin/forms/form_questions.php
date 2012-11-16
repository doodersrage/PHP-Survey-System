<?php

require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

$form_id = (!empty($_GET['formid']) ? $_GET['formid'] : $_POST['formid']);

// write post data

function write_field_data($field_post,$field_title,$question_id) {
global $db;
if (!empty($_POST['question_id'])) {
// check for existing question assignment
$question_chk_qry = mysql_query("DELETE FROM field_to_questions WHERE question_id = '".$_POST['question_id']."' AND field_type = '".$field_title."';");
}
if (!empty($field_post)) {
foreach($field_post as $post_field_id) {
$data_array = array($post_field_id,$question_id);
$modify_query = "INSERT INTO field_to_questions (field_id,question_id,field_type) VALUES (?,?,'".$field_title."');";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$data_array);
}}
}

if ($_POST['submit_question']) {
set_vars();
$form_result = chk_form();

if ($form_result == 1) {
$page_content = '<div class="warning">There appears to be missing information in your form submission. <br> Please review and try again.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
} else {
$client_info = array($form_id,
					$GLOBALS['title'],
					$GLOBALS['sort_order'],
					$GLOBALS['enabled'],
					$GLOBALS['description']
					);

if (empty($_POST['question_id'])) {
// write new client info to database
$modify_query = "INSERT INTO form_questions (form_id,question_title,sort_order,enabled,question) VALUES (?,?,?,?,?);";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$client_info);

$new_record_id = mysql_insert_id();

// assign question fields
write_field_data($_POST['text_field'],'text_field',$new_record_id);
write_field_data($_POST['text_area'],'text_area',$new_record_id);
write_field_data($_POST['list_box'],'list_box',$new_record_id);
write_field_data($_POST['radio_button'],'radio_button',$new_record_id);
write_field_data($_POST['check_box'],'check_box',$new_record_id);
write_field_data($_POST['dropdown'],'dropdown',$new_record_id);

$page_content = '<div class="warning">New question has been added.</div>';
clearvars();
} else {
// update existing client
$modify_query = "UPDATE form_questions SET form_id=?,question_title=?,sort_order=?,enabled=?,question=? WHERE question_id = '".$_POST['question_id']."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth,$client_info);
$page_content = '<div class="warning">Question information has been modified.</div>';

// assign question fields
write_field_data($_POST['text_field'],'text_field',$_POST['question_id']);
write_field_data($_POST['text_area'],'text_area',$_POST['question_id']);
write_field_data($_POST['list_box'],'list_box',$_POST['question_id']);
write_field_data($_POST['radio_button'],'radio_button',$_POST['question_id']);
write_field_data($_POST['check_box'],'check_box',$_POST['question_id']);
write_field_data($_POST['dropdown'],'dropdown',$_POST['question_id']);

clearvars();
}

}

}

// pull edisting question info
if ($_POST['Edit']) {
get_form_info();
}

// delete selected question
if ($_POST['Delete']) {
mysql_query("DELETE FROM form_questions WHERE question_id = '".$_POST['question_id']."';");
mysql_query("DELETE FROM field_to_questions WHERE question_id = '".$_POST['question_id']."';");
$page_content = '<div class="warning">Question Deleted.</div>';
} 

function clearvars() {
$GLOBALS['title'] = '';
$GLOBALS['sort_order'] = '';
$GLOBALS['enabled'] = '';
$GLOBALS['description'] = '';
$_POST['question_id'] = '';
}

// set post vars
function set_vars() {
$GLOBALS['title'] = $_POST['title'];
$GLOBALS['sort_order'] = $_POST['sort_order'];
$GLOBALS['enabled'] = $_POST['enabled'];
$GLOBALS['description'] = $_POST['description'];
}

// get client information
function get_form_info() {
global $db;
$modify_query = "SELECT question_title, sort_order, enabled, question FROM form_questions WHERE question_id = '".$_POST['question_id']."';";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$row = $res->fetchRow();

$GLOBALS['title'] = $row[0];
$GLOBALS['sort_order'] = $row[1];
$GLOBALS['enabled'] = $row[2];
$GLOBALS['description'] = $row[3];
}

// check form form errors
function chk_form() {
$form_errors = 0;

if (empty($GLOBALS['title'])) {
$form_errors = 1;
}

return $form_errors;
}

// populate form field types
function populate_selected_field_types($field_type,$question_id) {
global $db;
// get list of available forms
$modify_query = "SELECT ff.name FROM form_fields ff LEFT JOIN field_to_questions ftq ON ff.field_id = ftq.field_id WHERE ff.field_type = '".$field_type."' and ftq.question_id = '".$question_id."' ORDER BY ff.name ASC;";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);

while ($row = $res->fetchRow()) {
$page_content .= $row[0].'<br>' . "\n";
}

return $page_content;
}

// populate form field types
function populate_field_types($field_type) {
global $db;
// get list of available forms
$modify_query = "SELECT field_id, name FROM form_fields WHERE field_type = '".$field_type."' ORDER BY name ASC;";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);


while ($row = $res->fetchRow()) {
if ($_POST['Edit']) {
$question_chk_qry = mysql_query("SELECT field_id FROM field_to_questions WHERE question_id = '".$_POST['question_id']."' AND field_id = '".$row[0]."';");
$question_count = mysql_num_rows($question_chk_qry);
}
$page_content .= $row[1].': <input name="'.$field_type.'[]" type="checkbox" value="'.$row[0].'" '.($question_count > 0 ? 'checked' : '').'><br>' . "\n";
}

return $page_content;
}

// form handling
if (empty($form_id)) {
$page_content = '<div class="warning">You appear to have not chosen a form to edit. Please go back and select a form before you can begin to edit form questions.';
$page_content .= '<br><input value="Back" type="button" onclick="Javascript: window.history.back();"></div>';
} else {

// check for existing questions
$question_chk_qry = mysql_query("SELECT question_id FROM form_questions WHERE form_id = '".$form_id."';");
$question_count = mysql_num_rows($question_chk_qry);
if ($question_count == 0) {
$page_content = '<div align="center">No questions currently exist for this form.</div>';

} else {
$page_content .= '<p align="center">There are currently '.$question_count.' questions assigned to this form.</p>';

$modify_query = "SELECT question_title, question, sort_order, enabled, question_id FROM form_questions WHERE form_id = '".$form_id."' ORDER BY sort_order ASC;";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$question_num = 0;
while ($row = $res->fetchRow()) {
$question_num++;
$page_content .= '<form name="form1" method="post" action="" class="question_field"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" class="input_link_current">Question '.$question_num.'</td>
          </tr>
        <tr>
          <td valign="top" class="input_options" width="50%"><table width="100%"  border="0" cellpadding="2">
            <tr>
              <td class="required_input_title" width="89">Question Title: </td>
              <td class="input_field">'.$row[0].'</td>
            </tr>
            <tr>
              <td class="input_title">Sort Order:</td>
              <td class="input_field">'.$row[2].'</td>
            </tr>
            <tr>
              <td class="required_input_title">Enabled:</td>
              <td class="input_field">'.($row[3] == 1 ? 'YES' : 'NO').'</td>
            </tr>
          </table></td>
          <td valign="top" class="input_options" width="50%"><table width="100%"  border="0" cellpadding="2">
            <tr>
              <td rowspan="2" class="input_title">Description: </td>
              <td class="input_field">'.$row[1].'</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" class="input_link_current">Enabled Question Fields: </td>
        </tr>
        <tr>
          <td colspan="2"><table width="100%"  border="0" cellpadding="1">
            <tr>
              <td class="input_link_option">Text Fields </td>
              <td class="input_link_option">Text Areas </td>
              <td class="input_link_option">List Boxes </td>
              <td class="input_link_option">Radio Buttons </td>
              <td class="input_link_option">Check Boxes</td>
              <td class="input_link_option">Drop Downs </td>
            </tr>
            <tr>
              <td align="right" class="input_options">'.
			  populate_selected_field_types('text_field',$row[4]).
			  '</td>
              <td align="right" class="input_options">'.
			  populate_selected_field_types('text_area',$row[4]).
			  '</td>
              <td align="right" class="input_options">'.
			  populate_selected_field_types('list_box',$row[4]).
			  '</td>
              <td align="right" class="input_options">'.
			  populate_selected_field_types('radio_button',$row[4]).
			  '</td>
              <td align="right" class="input_options">'.
			  populate_selected_field_types('check_box',$row[4]).
			  '</td>
              <td align="right" class="input_options">'.
			  populate_selected_field_types('dropdown',$row[4]) .
			  '</td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" class="input_link"><input name="question_id" type="hidden" value="'.$row[4].'"><input name="Delete" type="submit" value="Delete"><input name="Edit" type="submit" value="Edit"></td>
          </tr>
      </table></form>';
}
}

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
    <form name="form1" method="post" action="" class="question_field_new">
      <table width="100%"  border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td colspan="2" class="input_link_new"><?PHP echo ($_POST['Edit'] ? 'Edit' : 'New'); ?> Question Form</td>
          </tr>
        <tr>
          <td valign="top" class="input_options"><table width="100%"  border="0" cellpadding="2">
            <tr>
              <td class="required_input_title">Question Title: </td>
              <td class="input_field"><input type="text" name="title" value="<?PHP echo $GLOBALS['title']; ?>"></td>
            </tr>
            <tr>
              <td class="input_title">Sort Order:</td>
              <td class="input_field"><input name="sort_order" type="text" size="11" maxlength="11" value="<?PHP echo $GLOBALS['sort_order']; ?>"></td>
            </tr>
            <tr>
              <td class="required_input_title">Enabled:</td>
              <td class="input_field"><?PHP echo build_yn('enabled',$GLOBALS['enabled']); ?></td>
            </tr>
          </table></td>
          <td valign="top" class="input_options"><table width="100%"  border="0" cellpadding="2">
            <tr>
              <td rowspan="2" class="input_title">Description: </td>
              <td class="input_field"><textarea name="description" cols="35" rows="5"><?PHP echo $GLOBALS['description']; ?></textarea></td>
            </tr>
          </table></td>
        </tr>
        <tr>
          <td colspan="2" class="input_link">Select Question Form Fields: </td>
        </tr>
        <tr>
          <td colspan="2"><table width="100%"  border="0" cellpadding="1">
            <tr>
              <td class="input_link">Text Fields </td>
              <td class="input_link">Text Areas </td>
              <td class="input_link">List Boxes </td>
              <td class="input_link">Radio Buttons </td>
              <td class="input_link">Check Boxes</td>
              <td class="input_link">Drop Downs </td>
            </tr>
            <tr>
              <td align="right" class="input_options">
			  <?PHP 
			  echo populate_field_types('text_field',$_POST['question_id']);
			  ?>
			  </td>
              <td align="right" class="input_options">
			  <?PHP 
			  echo populate_field_types('text_area',$_POST['question_id']);
			  ?>
			  </td>
              <td align="right" class="input_options">
			  <?PHP 
			  echo populate_field_types('list_box',$_POST['question_id']);
			  ?>
			  </td>
              <td align="right" class="input_options">
			  <?PHP 
			  echo populate_field_types('radio_button',$_POST['question_id']);
			  ?>
			  </td>
              <td align="right" class="input_options">
			  <?PHP 
			  echo populate_field_types('check_box',$_POST['question_id']);
			  ?>
			  </td>
              <td align="right" class="input_options">
			  <?PHP 
			  echo populate_field_types('dropdown',$_POST['question_id']);
			  ?>
			  </td>
            </tr>
          </table></td>
        </tr>
      </table>
	  <div align="center">
	    <input type="hidden" name="submit_question" value="1">
		<?PHP echo !empty($_POST['question_id']) ? '<input name="question_id" type="hidden" value="'.$_POST['question_id'].'">' : ''; ?>
	    <input name="Submit" type="submit" value="Submit"></div>
    </form>    
	<?PHP echo $page_content; ?>
    </td>
  </tr>
</table>