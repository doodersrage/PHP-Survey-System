<?PHP
class htmlfunctions
{

// create radio button
function radio_button ($name,$checked = false,$id = '',$value = '',$tabindex = '') {
if ($value == '') $value = 1;
$radio = '<input ';
$radio .= 'name="'.$name.'" ';
$radio .= ($id != '' ? 'id="'.$id.'" ' : ' ');
$radio .= 'type="radio" ';
$radio .= 'value="'.$value.'" ';
$radio .= ($tabindex != '' ? 'tabindex="'.$tabindex.'" ' : '');
$radio .= ($checked == true ? 'checked' : '');
$radio .= '>';

return $radio;
}

// create checkbox
function checkbox ($name,$value = '',$id = '',$checked = false,$tabindex = '') {
$checkb = '<input ';
$checkb .= 'name="'.$name.'" ';
$checkb .= 'type="checkbox" ';
$checkb .= ($id != '' ? 'id="'.$id.'" ' : ' ');
$checkb .= 'value="'.$value.'"';
$checkb .= ($tabindex != '' ? 'tabindex="'.$tabindex.'" ' : '');
$checkb .= ($checked == true ? 'checked' : '');
$checkb .= '>';

return $checkb;
}

// create text field
function text_field ($name,$size = '',$id = '',$value = '',$type = 'text',$tabindex = '',$maxlength = '') {
$textf = '<input ';
$textf .= 'name="'.$name.'" ';
$textf .= 'type="'.$type.'" ';
$textf .= ($id != '' ? 'id="'.$id.'" ' : ' ');
$textf .= 'value="'.$value.'" ';
$textf .= ($size != '' ? 'size="'.$size.'" ' : '');
$textf .= ($tabindex != '' ? 'tabindex="'.$tabindex.'" ' : '');
$textf .= ($maxlength != '' ? 'maxlength="'.$maxlength.'" ' : '');
$textf .= '>';

return $textf;
}

// create password field
function password_field ($name,$size,$id = '',$value = '',$tabindex = '') {
$passf = '<input ';
$passf .= 'name="'.$name.'" ';
$passf .= 'type="password" ';
$passf .= ($id != '' ? 'id="'.$id.'" ' : ' ');
$passf .= 'value="'.$value.'" ';
$passf .= 'size="'.$size.'" ';
$passf .= ($tabindex != '' ? 'tabindex="'.$tabindex.'" ' : '');
$passf .= '>';

return $passf;
}

// create text area
function textarea ($name,$columns,$rows,$value = '',$id = '',$tabindex = '') {
$texta = '<textarea ';
$texta .= 'name="'.$name.'" ';
$texta .= 'cols="'.$columns.'" ';
$texta .= 'rows="'.$rows.'" ';
$texta .= ($id != '' ? 'id="'.$id.'" ' : ' ');
$texta .= ($tabindex != '' ? 'tabindex="'.$tabindex.'" ' : '');
$texta .= '>'.$value.'</textarea>';

return $texta;
}

// create filebox
function filebox ($name,$id = '',$tabindex = '') {
$file = '<input ';
$file .= 'name="'.$name.'" ';
$file .= 'type="file" ';
$file .= ($id != '' ? 'id="'.$id.'" ' : ' ');
$file .= ($tabindex != '' ? 'tabindex="'.$tabindex.'" ' : '');
$file .= '>';

return $file;
}

// create select box
function select_box ($name,$optionvalues,$optionnames,$selected = '',$id = '',$tabindex = '') {
$selb = '<select ';
$selb .= 'name="'.$name.'" ';
$selb .= 'id="'.$id.'" ';
$selb .= ($tabindex != '' ? 'tabindex="'.$tabindex.'" ' : '');
$selb .= '>';

//create select box options
$options_array = my_array_combine($optionvalues, $optionnames);
// find how many items exit within arrays
$array_count = count($optionvalues);

foreach ($optionvalues as $optionnum) {
$selboptions .= '<option ';
$selboptions .= 'value="'.$optionnum.'" ';
$selboptions .= ($selected == $optionnum ? 'selected="selected">' : '>');
$selboptions .= $options_array[$optionnum];
$selboptions .= '</option>';
}

$selb .= $selboptions . '</select>';

return $selb;
}

function submit_button ($value, $tabindex = '') {
$submit = '<input type="submit" name="Submit" ';
$submit .= 'value="'.$value.'" ';
$submit .= ($tabindex != '' ? 'tabindex="'.$tabindex.'" ' : '');
$submit .= '>';

return $submit;
}

function write_link ($file,$linkname,$target = '',$class = '',$id = '') {
$link = '<a ';
$link .= 'href="'.ADMIN_ADDRESS.$file.'" ';
$link .= ($target != '' ? 'target="'.$target.'" ' : ''); 
$link .= ($class != '' ? 'class="'.$class.'" ' : ''); 
$link .= ($id != '' ? 'id="'.$id.'" ' : ''); 
$link .= '>'.$linkname.'</a>';

return $link;
}

// create form tag
function draw_form ($name,$action,$method = '',$encryptype = '') {
$form = '<form ';
$form .= 'name="'.$name.'" ';
$form .= ($method != '' ? 'method="'.$method.'" ' : 'method="post" ');
$form .= ($encryptype != '' ? 'enctype="'.$encryptype.'" ' : '');
$form .= ( $action != '' ? 'action="'.$action.'"' : 'action=""' );
$form .= '>';

return $form;
}

}
?>