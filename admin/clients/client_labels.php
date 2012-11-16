<?php
require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

define('FPDF_FONTPATH','../libs/fpdf153/font/');
require_once('PDF_Label.php');

/*-------------------------------------------------
To create the object, 2 possibilities:
either pass a custom format via an array
or use a built-in AVERY name
-------------------------------------------------*/

// Example of custom format; we start at the second column
//$pdf = new PDF_Label(array('name'=>'perso1', 'paper-size'=>'A4', 'marginLeft'=>1, 'marginTop'=>1, 'NX'=>2, 'NY'=>7, 'SpaceX'=>0, 'SpaceY'=>0, 'width'=>99.1, 'height'=>38.1, 'metric'=>'mm', 'font-size'=>14), 1, 2);
// Standard format
$pdf = new PDF_Label('5160');

$pdf->Open();
//$pdf->AddPage();

// client query
$modify_query = "SELECT contact_salutation, contact_name, company, address1, address2, city, state, zip, phone_number, fax_number, email_address, psk FROM client_info ORDER BY zip ASC, contact_name ASC";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
while($row = $res->fetchRow()) {

$salutation = $row[0];
$name = $row[1];
$company = $row[2];
$address1 = $row[3];
$address2 = $row[4];
$city = $row[5];
$state = $row[6];
$zip = $row[7];

// Print labels
if (!empty($address2)) {
$pdf->Add_PDF_Label(sprintf("%s\n%s\n%s\n%s, %s %s", $salutation . ' ' . $name, $address1, $address2, $city, $state, $zip));
} else {
$pdf->Add_PDF_Label(sprintf("%s\n%s\n%s, %s %s", $salutation . ' ' . $name, $address1, $city, $state, $zip));
}
}

$pdf->Output();
?>