<?php
//============================================================+
// File name   : example_027.php
// Begin       : 2008-03-04
// Last Update : 2008-03-28
// 
// Description : Example 027 for TCPDF class
//               Barcodes
// 
// Author: Nicola Asuni
// 
// (c) Copyright:
//               Nicola Asuni
//               Tecnick.com s.r.l.
//               Via Della Pace, 11
//               09044 Quartucciu (CA)
//               ITALY
//               www.tecnick.com
//               info@tecnick.com
//============================================================+

/**
 * Creates an example PDF TEST document using TCPDF
 * @package com.tecnick.tcpdf
 * @abstract TCPDF - Example: barcodes.
 * @author Nicola Asuni
 * @copyright 2004-2008 Nicola Asuni - Tecnick.com S.r.l (www.tecnick.com) Via Della Pace, 11 - 09044 - Quartucciu (CA) - ITALY - www.tecnick.com - info@tecnick.com
 * @link http://tcpdf.org
 * @license http://www.gnu.org/copyleft/lesser.html LGPL
 * @since 2008-03-04
 */

require_once('../config/lang/eng.php');
require_once('../tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true); 

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("Nicola Asuni");
$pdf->SetTitle("TCPDF Example 027");
$pdf->SetSubject("TCPDF Tutorial");
$pdf->SetKeywords("TCPDF, PDF, example, test, guide");

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); 

//set some language-dependent strings
$pdf->setLanguageArray($l); 

//initialize document
$pdf->AliasNbPages();

// add a page
$pdf->AddPage();

// ---------------------------------------------------------

// set font
$pdf->SetFont("vera", "BI", 10);

// display barcode on footer
$pdf->setBarcode("0123456789");

// set barcode style
require_once("../barcode/barcode.php");
$barcode_style = 0;
//$barcode_style |= BCS_ALIGN_CENTER;
$barcode_style |= BCS_IMAGE_PNG;
$barcode_style |= BCS_TRANSPARENT;
//$barcode_style |= BCS_BORDER;
$barcode_style |= BCS_DRAW_TEXT;
$barcode_style |= BCS_STRETCH_TEXT;
//$barcode_style |= BCS_REVERSE_COLOR;

			
// I25 barcode
$pdf->writeBarcode($pdf->GetX(), $pdf->GetY(), 100, 15, "I25", $barcode_style, false, 2, "0123456789");
$pdf->Ln(18);

// C128A barcode
$pdf->writeBarcode($pdf->GetX(), $pdf->GetY(), 100, 15, "C128A", $barcode_style, false, 2, "C128A 0123456789");
$pdf->Ln(18);

// C128B barcode
$pdf->writeBarcode($pdf->GetX(), $pdf->GetY(), 100, 15, "C128B", $barcode_style, false, 2, "C128B 0123456789");
$pdf->Ln(18);

// C128C barcode
$pdf->writeBarcode($pdf->GetX(), $pdf->GetY(), 100, 15, "C128C", $barcode_style, false, 2, "0123456789");
$pdf->Ln(18);

// C39 barcode
$pdf->writeBarcode($pdf->GetX(), $pdf->GetY(), 100, 15, "C39", $barcode_style, false, 2, "C39 0123456789");
$pdf->Ln(18);

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+
?>