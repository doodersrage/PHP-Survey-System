<?php
//============================================================+
// File name   : example_006.php
// Begin       : 2008-03-04
// Last Update : 2008-04-18
// 
// Description : Example 006 for TCPDF class
//               WriteHTML and RTL support
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
 * @abstract TCPDF - Example: WriteHTML and RTL support
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
$pdf->SetTitle("TCPDF Example 006");
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
$pdf->SetFont("FreeSerif", "", 11);

// create some HTML content
$htmlcontent = "&lt; € &euro; &#8364; &amp; è &egrave; &copy; &gt; \\slash \\\\double-slash \\\\\\triple-slash<br /><h1>heading 1</h1><h2>heading 2</h2><h3>heading 3</h3><h4>heading 4</h4><h5>heading 5</h5><h6>heading 6</h6>ordered list:<br /><ol><li><b>bold text</b></li><li><i>italic text</i></li><li><u>underlined text</u></li><li><a href=\"http://www.tecnick.com\" dir=\"ltr\">link to http://www.tecnick.com</a></li><li>test break<br />second line 12,34.56 text text<br />third line</li><li>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt.</li><li dir=\"rtl\">RTL TEXT</li><li dir=\"ltr\">LTR TEXT</li><li><b>T</b>E<i>S</i><u>T</u> <del>line through</del></li><li><font size=\"+3\">font + 3</font></li><li><small>small text</small></li><li>normal <sub>subscript</sub> <sup>superscript</sup></li></ul><dl><dt>Coffee</dt><dd>Black hot drink</dd><dt>Milk</dt><dd>White cold drink</dd></dl>Table:<br /><table border=\"1\" cellspacing=\"1\" cellpadding=\"1\"><tr><th>#</th><th>A</th><th>B</th></tr><tr><th>1</th><td bgcolor=\"#cccccc\" align=\"center\">A1</td><td>B1</td></tr><tr><th>&nbsp;</th><td>A2 € &euro; &#8364; &amp; è &egrave; gf</td><td bgcolor=\"#FFFF00\"><font color=\"#FF0000\">B3</font></td></tr></table><hr />images:<br /><img src=\"../images/logo_example.png\" alt=\"test alt attribute\" width=\"100\" height=\"100\" border=\"0\" align=\"top\" /><img src=\"../images/logo_example.gif\" alt=\"test alt attribute\" width=\"100\" height=\"100\" border=\"0\" align=\"top\" /><img src=\"../images/logo_example.jpg\" alt=\"test alt attribute\" width=\"100\" height=\"100\" border=\"0\" />";

// output the HTML content
$pdf->writeHTML($htmlcontent, true, 0, true, 0);

// output some RTL HTML content
$pdf->writeHTML("The words &#8220;<span dir=\"rtl\">&#1502;&#1494;&#1500; [mazel] &#1496;&#1493;&#1489; [tov]</span>&#8221; mean &#8220;Congratulations!&#8221;", true, 0, true, 0);

// reset pointer to the last page
$pdf->lastPage();

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// Print all HTML colors

// add a page
$pdf->AddPage();

require_once('../htmlcolors.php');

$textcolors = "<h1>HTML Text Colors</h1>";
$bgcolors = "<hr /><h1>HTML Background Colors</h1>";

foreach($webcolor as $k => $v) {
	//$htmlcolors .= "<span bgcolor=\"#".$v."\" color=\"#".$v."\">".$v."</span>-";
	$textcolors .= "<span color=\"#".$v."\">".$v."</span> ";
	$bgcolors .= "<span bgcolor=\"#".$v."\" color=\"#333333\">".$v."</span> ";
}

// output the HTML content
$pdf->writeHTML($textcolors, true, 0, true, 0);
$pdf->writeHTML($bgcolors, true, 0, true, 0);

// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

// Test word-wrap

// create some HTML content
$htmltxt = "<hr /><h1>Word-wrap</h1><b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i> <b>thisisaverylongword</b> <i>thisisanotherverylongword</i>";

// output the HTML content
$pdf->writeHTML($htmltxt, true, 0, true, 0);

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output();

//============================================================+
// END OF FILE                                                 
//============================================================+
?>