<?PHP

function generatePassword($length=9, $strength=0) {
    $vowels = 'aeuy';
    $consonants = 'bdghjmnpqrstvz';
    if ($strength & 1) {
        $consonants .= 'BDGHJLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= "AEUY";
    }
    if ($strength & 4) {
        $consonants .= '23456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%';
    }

    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }
	
	// make sure password is not assigned to anyone else and if so rerun generate script
	$password_check = mysql_query("SELECT client_id FROM client_info WHERE psk = '".$password."';");
	if (mysql_num_rows($password_check) > 0) generatePassword(10,2);
	
    return $password;
}

// prints state select drop down
function print_state_select($selected_state) {

$state_array = array(
'AL' => 'Alabama',
'AK' => 'Alaska',
'AZ' => 'Arizona',
'AR' => 'Arkansas',
'CA' => 'California',
'CO' => 'Colorado',
'CT' => 'Connecticut',
'DE' => 'Delaware',
'DC' => 'District Of Columbia',
'FL' => 'Florida',
'GA' => 'Georgia',
'HI' => 'Hawaii',
'ID' => 'Idaho',
'IL' => 'Illinois',
'IN' => 'Indiana',
'IA' => 'Iowa',
'KS' => 'Kansas',
'KY' => 'Kentucky',
'LA' => 'Louisiana',
'ME' => 'Maine',
'MD' => 'Maryland',
'MA' => 'Massachusetts',
'MI' => 'Michigan',
'MN' => 'Minnesota',
'MS' => 'Mississippi',
'MO' => 'Missouri',
'MT' => 'Montana',
'NE' => 'Nebraska',
'NV' => 'Nevada',
'NH' => 'New Hampshire',
'NJ' => 'New Jersey',
'NM' => 'New Mexico',
'NY' => 'New York',
'NC' => 'North Carolina',
'ND' => 'North Dakota',
'OH' => 'Ohio',
'OK' => 'Oklahoma',
'OR' => 'Oregon',
'PA' => 'Pennsylvania',
'RI' => 'Rhode Island',
'SC' => 'South Carolina',
'SD' => 'South Dakota',
'TN' => 'Tennessee',
'TX' => 'Texas',
'UT' => 'Utah',
'VT' => 'Vermont',
'VA' => 'Virginia',
'WA' => 'Washington',
'WV' => 'West Virginia',
'WI' => 'Wisconsin',
'WY' => 'Wyoming'
);

$select_box = "<select name=\"state\" limit=\"7\"> 
<option value=\"\" selected=\"selected\">Select a State</option> \r\n";
foreach ($state_array as $ini => $name) {
$select_box .= "<option value=\"".$ini."\" ".($selected_state == $ini ? "selected" : ""). ">".$name."</option> \r\n";
}
$select_box .= "</select>";

return $select_box;
}

function build_salutation($selected_sal) {
$salutation_array = array('Mr.','Ms.','Mrs.','Dr.');

$select_box = "<select name=\"contact_salutation\" limit=\"7\"> \r\n";
foreach ($salutation_array as $name) {
$select_box .= "<option value=\"".$name."\" ".($selected_sal == $name ? "selected" : ""). ">".$name."</option> \r\n";
}
$select_box .= "</select>";

return $select_box;
}

function build_yn($field_name,$selected_yn) {
$yn_array = array('1' => 'YES','0' => 'NO');

$select_box = "<select name=\"".$field_name."\" limit=\"7\"> \r\n";
foreach ($yn_array as $ini => $name) {
$select_box .= "<option value=\"".$ini."\" ".($selected_yn == $ini ? "selected" : ""). ">".$name."</option> \r\n";
}
$select_box .= "</select>";

return $select_box;
}

function build_field_type_dd($field_name,$selected_sal) {
$salutation_array = array('text_field','text_area','radio_button','checkbox','dropdown','list_box');

$select_box = "<select name=\"".$field_name."\" limit=\"7\"> \r\n";
foreach ($salutation_array as $name) {
$select_box .= "<option value=\"".$name."\" ".($selected_sal == $name ? "selected" : ""). ">".$name."</option> \r\n";
}
$select_box .= "</select>";

return $select_box;
}

function delete_client($client_id) {
mysql_query("DELETE FROM client_info WHERE client_id = '".$client_id."';");
mysql_query("DELETE FROM client_forms WHERE client_id = '".$client_id."';");
mysql_query("DELETE FROM form_results WHERE client_id = '".$client_id."';");

}

function draw_survey_field($field_type,$name,$field_length = '',$max_chars = '',$required = '',$question_id = '',$description = '',$field_columns = '',$type_values = '') {

$name = trim(strtolower(str_replace(" ","_",$name)));

switch($field_type) {
case 'text_field':
$input = (!empty($description) ? $description . ': ' : '') . '<input name="'.$name.'['.$question_id.']" type="text" '.(!empty($field_length) ? 'size="'.$field_length.'" ' : ''). (!empty($max_chars) ? 'maxlength="'.$max_chars.'" ' : '') . 'value="'.$type_values.'">';
break;
case 'text_area':
$input = (!empty($description) ? $description . ': ' : '') . '<textarea name="'.$name.'['.$question_id.']" cols="'.$field_columns.'" rows="4">'.$type_values.'</textarea>';
break;
case 'radio_button':
$radio_value = explode("\n",$type_values);
$input = (!empty($description) ? $description . ': ' : '');
foreach($radio_value as $field_value) {
$input .= $field_value.': <input name="'.$name.'['.$question_id.']" type="radio" value="'.trim(str_replace("\n","",$field_value)).'">&nbsp;';
}
break;
case 'checkbox':
$check_value = explode("\n",$type_values);
$input = (!empty($description) ? $description . ': ' : '');
foreach($check_value as $field_value) {
$input .= $field_value.': <input name="'.$name.'['.$question_id.'][]" type="checkbox" value="'.trim(str_replace("\n","",$field_value)).'">&nbsp;';
}
break;
case 'dropdown':
$input = (!empty($description) ? $description . ': ' : '');
$input = '<select name="'.$name.'['.$question_id.']">';
$dropd_value = explode("\n",$type_values);
foreach($dropd_value as $field_value) {
$input .= '<option value="'.$field_value.'">'.$field_value.'</option>';
}
$input .= '</select>';
break;
case 'list_box':
$input = (!empty($description) ? $description . ': ' : '');
$input = '<select name="'.$name.'['.$question_id.']" size="5">';
$dropd_value = explode("\n",$type_values);
foreach($dropd_value as $field_value) {
$input .= '<option value="'.$field_value.'">'.$field_value.'</option>';
}
$input .= '</select>';
break;
}

return $input;
}
?>