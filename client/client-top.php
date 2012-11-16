<?PHP
// start session
session_start();

// attache login script
require(SURVERY_ADMIN_ROOT.'libs/functions/login.php');

if ($_POST['login_submit'] == 1) {
login_user($_POST['track_number']);
}

// check user status
if (session_check($_SESSION['sessionid'],$_SESSION['contact_name']) == 0) {
logout_user();
header("Location: ../index.php");
}

// logout user
if ($_GET['logout_user']==1) {
logout_user();
}

?>