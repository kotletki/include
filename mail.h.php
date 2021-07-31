<?php

function email($to, $from_email, $reply_to, $from_name, $subject, $message, $cc = "", $bcc = "", $content_type = "text/html", $charset = "utf8", $with_attachment = 1, $additional_headers = "") {

	$boundary1 = rand(0,9)."-".rand(10000000000,9999999999)."-".rand(10000000000,9999999999)."=:".rand(10000,99999);
	$boundary2 = rand(0,9)."-".rand(10000000000,9999999999)."-".rand(10000000000,9999999999)."=:".rand(10000,99999);

	$eol = "\r\n";

	if ($cc) {$cc = $eol."Cc: ".$cc;}
	if ($bcc) {$bcc = $eol."Bcc: ".$bcc;}

	$headers = "";
	$ftype = array();
	$fname = array();

	if ($with_attachment && is_array($_FILES)) {
		foreach (array_keys($_FILES) as $key) {
			for ($i = 0; $i < count($_FILES[$key]["name"]); $i++) {
				if (is_uploaded_file($_FILES[$key]["tmp_name"][$i]) && !empty($_FILES[$key]["size"][$i]) && !empty($_FILES[$key]["name"][$i])) {
					$h = fopen($_FILES[$key]["tmp_name"][$i], "rb"); 
					$f_contents = fread($h, $_FILES[$key]["size"][$i]); 
					$attachment[] = chunk_split(base64_encode($f_contents));
					fclose($h); 
					$ftype[] = $_FILES[$key]["type"][$i];
					$fname[] = $_FILES[$key]["name"][$i];
				}
			}
		}
	}

	if (is_array($ftype)) { // WITH ATTACHMENT

		$headers = "From: ".$from_name." <".$from_email.">";
		$headers .= $eol."Reply-To: ".$reply_to;
		$headers .= $cc.$bcc;
		$headers .= $eol."MIME-Version: 1.0";
		$headers .= $eol."Content-Type: multipart/mixed; boundary=\"".$boundary1."\"";

		$attachments = "";

		for ($j = 0; $j < count($ftype); $j++){
			$attachments .= $eol.$eol."--".$boundary1;
			$attachments .= $eol."Content-Type: ".$ftype[$j]."; name=\"".$fname[$j]."\"";
			$attachments .= $eol."Content-Transfer-Encoding: base64";
			$attachments .= $eol."Content-Disposition: attachment; filename=\"".$fname[$j]."\"";
			$attachments .= $eol.$eol.$attachment[$j];
		}

		$body = "";

		$body .= $eol.$eol."--".$boundary1;
		$body .= $eol."Content-Type: multipart/alternative; boundary=\"".$boundary2."\"";
		$body .= $eol.$eol."--".$boundary2;
		$body .= $eol."Content-Type: text/plain; charset=".$charset;
		$body .= $eol.$eol.$message;
		$body .= $eol.$eol."--".$boundary2;
		$body .= $eol."Content-Type: text/html; charset=".$charset;
		$body .= $eol.$eol.$message;
		$body .= $eol.$eol."--".$boundary2."--";
		$body .= $eol.$attachments;
		$body .= $eol.$eol."--".$boundary1."--";

	} else { // WITHOUT ATTACHMENT

		$headers  = "From: ".$from_name." <".$from_email.">";
		$headers .= $eol."Reply-To: ".$reply_to;
		$headers .= $cc.$bcc;
		$headers .= $eol."MIME-Version: 1.0";
		$headers .= $eol."Content-Type: ".$content_type."; charset=".$charset;
		$headers .= $eol.$additional_headers;
		$body = $message;

	}

	if (!mail($to, $subject, $body, $headers)) {return 0;}
	return 1;

}

function is_email($email) {
	if (!preg_match("/^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}$/i", $email)) {return 0;}
	return 1;
}
