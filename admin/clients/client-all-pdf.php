<?php
require_once($_SERVER['DOCUMENT_ROOT']."2008AnnualSurvey/admin/includes/application_top.php");

define('FPDF_FONTPATH','../libs/fpdf153/font/');
require('WriteHTML.php');

$pdf=new PDF();
$pdf->Open();

$formid = $_GET['formid'];

$client_form_query = "SELECT client_id FROM client_forms WHERE form_id = '".$formid."' ORDER BY client_id ASC";
$sth = $db->prepare($client_form_query);
$res_client = $db->execute($sth);
while ($row_client = $res_client->fetchRow()) {

set_time_limit(30);

$modify_query = "SELECT contact_salutation, contact_name, company, address1, address2, city, state, zip, phone_number, fax_number, email_address, psk FROM client_info WHERE client_id = '".$row_client[0]."' ORDER BY company ASC";
$sth = $db->prepare($modify_query);
$res = $db->execute($sth);
$row = $res->fetchRow();

$date = date("F j, Y");
$salutation = $row[0];
$name = $row[1];
$company = $row[2];
$address1 = $row[3];
$address2 = $row[4];
$city = $row[5];
$state = $row[6];
$zip = $row[7];
$phone_number = $row[8];
$fax_number = $row[9];
$email_address = $row[10];
$psk = $row[11];

// format vars
$address = $company . '<br>' . $address1 . '<br>' . (!empty($address2) ? $address2 . '<br>' : '') . $city . ', ' . $state . ' ' . $zip;

$name_break = explode(' ',$name);
$fname = array_pop($name_break);

$letter_query = "SELECT letter FROM form_info WHERE form_id = '".$formid."';";
$sth_letter = $db->prepare($letter_query);
$res_letter = $db->execute($sth_letter);
$row_letter = $res_letter->fetchRow();

$letter = $row_letter[0];
// assemble letter

// replaced vars
$replaced_vars = array('$date$',
	'$name$',
	'$address$',
	'$salutation$',
	'$fname$',
	'$tracking_number$',
	);

$new_vars = array($date,
			$name,
			$address,
			$salutation,
			$fname,
			$psk,
			);

$new_letter = str_replace($replaced_vars,$new_vars,$letter);

$pdf->AddPage();
$pdf->SetFont('Arial');
$pdf->Cell(0,30,$pdf->Image('../../images/vectec_logo.jpg',75,8,64),0,1); 
//$pdf->Image('http://www.vectec.org/enews/images/bill_signature.jpg',12,225,14);
$pdf->WriteHTML($new_letter);
}
$pdf->Output();
?>