<?php
    
    require('html_to_pdf.inc.php');
    $htmltopdf = new HTML_TO_PDF();
    
    //$htmltopdf->useURL(HKC_USE_EASYW);  // default HKC_USE_ABC other HKC_USE_EASYW
    $htmltopdf->saveFile("abc.pdf");
    $htmltopdf->downloadFile("abc.pdf");
    //$result = $htmltopdf->convertHTML("<b>MY TEST</b>");
    $result = $htmltopdf->convertURL("http://test.ultraglobal.info/govazo/");
    if($result==false)
        echo $htmltopdf->error();
?> 