<?PHP

// pull config info
require('config.php');

// attache db
require(SURVERY_ADMIN_ROOT.'libs/functions/db.php');
dbconnect();

// load general functions
require(SURVERY_ADMIN_ROOT.'libs/functions/general.php');

// attache form script
require(SURVERY_ADMIN_ROOT.'libs/classes/htmlfunctions.php');
$form = new htmlfunctions;
?>