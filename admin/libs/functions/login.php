<?PHP

// check login pass against stored password
function compare_login_pass($saved_pass,$submitted_pass,$created) {
$login_check = 0;

if ($saved_pass == md5($submitted_pass.$created)) $login_check = 1;

return $login_check;
}

// login user
function login_user($psk) {

// look up user in database
$user_query = mysql_query("SELECT client_id, contact_name, created FROM client_info WHERE psk = '".$psk."';");
$user_query_result = mysql_fetch_array($user_query);
if (mysql_num_rows($user_query) != 0) {

// generate and store session_id
$_SESSION['sessionid'] = md5(date(r).$psk);
$_SESSION['contact_name'] = $user_query_result['contact_name'];
$_SESSION['client_id'] = $user_query_result['client_id'];

mysql_query("UPDATE client_info SET session_id = '".$_SESSION['sessionid']."' WHERE psk = '".$psk."';");

} else {
header("Location: ../index.php?error=incorrectlogin");
}
}

// remove user session cache
function logout_user() {
session_unset();
session_destroy();
header("Location: ../index.php");
}

// user session check
function session_check($session_id,$contact_name) {
$session_check = 0;
$session_query = mysql_query("SELECT session_id FROM client_info WHERE session_id = '".$session_id."' AND contact_name = '".$contact_name."' AND session_id is not null and session_id <> '';");
$session_query_result = mysql_fetch_array($session_query);
if ($session_query_result['session_id'] == $session_id && mysql_num_rows($session_query) > 0) $session_check = 1;

$session_check = 1;
return $session_check;
}

?>