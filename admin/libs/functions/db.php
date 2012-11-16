<?php

require_once SURVERY_ADMIN_ROOT . '/libs/DB.php';

function dbconnect() {
	global $db;
	if ( is_object($db) ) {
		return $db;
	}


	$dsn = array(

		'phptype'  => DB_TYPE,
		'hostspec' => DB_HOST,
		'database' => DB_NAME,
		'username' => DB_USERNAME,
		'password' => DB_PASSWORD,
	
	);

	$db = DB::connect($dsn);

	if (DB::isError($db)) {
		die($db->getMessage());
	}
	
	return $db;
}


PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'handle_pear_error');

function handle_pear_error ($error_obj) {
	if ( PEAR_DB_DONTDIE ) {
		return;
	}

	echo "</TD></TD></TD></TR></TR></TR></TABLE></TABLE></TABLE></CENTER></CENTER>";
	echo "<HR><H2>Error (Programming exception)</H2>";

	if (DEBUG_ENV) {
		echo "<H3>Information:</H3>";
		#print '<pre>';
		#print_r($error_obj);
		#print "</pre>\n";
		die ("<B>Error:</B><BR>".$error_obj->getMessage()."<BR>\n<B>Debug:</B><BR>".$error_obj->getDebugInfo());
	} else {
			die ('Sorry you request can not be processed now. Try again later');
	}			

}

?>