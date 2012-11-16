<?PHP

function send_email($to_address,$from_address,$subject,$plaintext_message = '',$html_message = '') {
//set vars
$from_address = $from_address;
$email_subject = $subject;
$plain_text_body = $plaintext_message;
$html_body = $html_message;

# -=-=-=- MIME BOUNDARY

$mime_boundary = "----gazettejournal.net----".md5(time());

# -=-=-=- MAIL HEADERS

//$to = "doodersrage@yahoo.com";
$subject = $email_subject;

$headers = "From: ".$from_address."\n";
$headers .= "Reply-To: ".$from_address."\n";
$headers .= "MIME-Version: 1.0\n";
$headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";

# -=-=-=- TEXT EMAIL PART

$message = "--$mime_boundary\n";
$message .= "Content-Type: text/plain; charset=UTF-8\n";
$message .= "Content-Transfer-Encoding: 8bit\n\n";

$message .= $plain_text_body . "\n";

# -=-=-=- HTML EMAIL PART
 
$message .= "--$mime_boundary\n";
$message .= "Content-Type: text/html; charset=UTF-8\n";
$message .= "Content-Transfer-Encoding: 8bit\n\n";

$message .= "<html>\n";
$message .= "<body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:14px; color:#666666;\">\n";
$message .= $html_body;
$message .= "</body>\n";
$message .= "</html>\n";

# -=-=-=- FINAL BOUNDARY

$message .= "--$mime_boundary--\n\n";

# -=-=-=- SEND MAIL

$mail_sent = @mail( $to_address, $subject, $message, $headers );
}

?>