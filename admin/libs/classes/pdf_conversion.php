<?php
/**
* HTML2PDFReport Generator Class
*
* @author  :  MA Razzaque Rupom <rupom_315@yahoo.com>, <rupom.bd@gmail.com>
*             Moderator, phpResource (http://groups.yahoo.com/group/phpresource/)
*             URL: http://www.rupom.info  
* @version :  1.0
* @date       06/05/2006
* Purpose  :  Generating Pdf Report from HTML
*/
class Html2PdfReport
{
   private $url;   
   private $pdfWidth   = 850;
   private $remoteApp  = "http://services.phpresgroup.org/pdf/public_html/html2ps.php";
   private $pdfVersion = '1.3'; //default PDF Version (for acrobat )

   /**
   * Sets URL that will be converted to PDF
   * @param URL of the HTML file
   * @return none
   */
   function setUrl($url)
   {
      $this->url = $url;
   }
   
   /**
   * Sets width of the PDF
   * @param Integer pdf width
   * @return none
   */
   function setPdfWidth($pdfWidth)
   {
         if(is_numeric($pdfWidth))
      {
         $this->pdfWidth = $pdfWidth;    
      }
   }
   
   /**
   * Sets PDF version (added on 06/23/2006 by Rupom)
   * @param Integer pdf version
   * @return none
   */
   function setPdfVersion($pdfVersion)
   {
         /*
         $pdfVersion = 1.3 for Adobe Acrobat Reader 4
         $pdfVersion = 1.4 for Adobe Acrobat Reader 5
         $pdfVersion = 1.5 for Adobe Acrobat Reader 6
         */
      $this->pdfVersion = $pdfVersion;    
   }   
   
   /**
   * Gets PDF report
   * @param none
   * @return none
   */       
   function getPdfReport()
   {
            
      $htmlUrl = $this->url;
      $pdfFileName = basename($htmlUrl).'.pdf';
      
      // Outputting PDF Report
      header("Content-type: application/pdf");
               
      // It will be called basename($this->htmlUrl).pdf         
      header("Content-Disposition: attachment; filename=".$pdfFileName);
      
      // The PDF source is the returned value of method generatePdfReport()      
      echo $this->generatePdfReport();    
          
   }//EO Method

   /**
   * Generates PDF report from remote application
   * @param none
   * @return report data on PDF mode
   */          
   function generatePdfReport()
   {
        $remoteApp     = $this->remoteApp;
          $waterMarkHtml = "phpresgroup.org";//change it according to your need
          $htmlUrl       = urlencode($this->url);          
          $pdfWidth      = $this->pdfWidth;
          $pdfVersion    = $this->pdfVersion;
          
          
          $requestString = "process_mode=single&URL=$htmlUrl&pixels=$pdfWidth&scalepoints=1&renderimages=1&renderlinks=1&renderfields=1&media=Letter&cssmedia=screen&leftmargin=10&rightmargin=10&topmargin=15&bottommargin=15&encoding=&headerhtml=&footerhtml=&watermarkhtml=$waterMarkHtml&method=fpdf&pdfversion=$pdfVersion&output=0&convert=Convert+File";             
      
        //Init the curl session
        $ch = curl_init();
        // set the post-to url (do not include the ?query+string here!)
        curl_setopt ($ch, CURLOPT_URL, $remoteApp);
        // Header control
        curl_setopt ($ch, CURLOPT_HEADER, 0);
        //Tell it to make a POST, not a GET
        curl_setopt ($ch, CURLOPT_POST, 1);
        // Put the query string here starting without "?"
        curl_setopt ($ch, CURLOPT_POSTFIELDS, $requestString);
        // This allows the output to be set into a variable
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        // execute the curl session and return the output to a variable $response        
        $response = curl_exec ($ch);
        // Close the curl session
        curl_close ($ch);        
        
        return $response;              
   }//EO Method
   
   /**
   * Debugs dump/data
   * @param $dump
   * @return none
   */
   function dBug($dump)
   {
         echo '<pre>';
         print_r($dump);
         echo '</pre>';
   }
   
}//EO Class
?> 