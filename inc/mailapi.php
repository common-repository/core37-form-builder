<?php

include_once 'encrypt.php';


function setGmailSenderName($defname)
{
	return get_option( 'c37_form_gmail_sender_name' );
}

function setGmailSenderEmail($defemail)
{
	$crypt = new C37_Crypt();

	return $crypt->decrypt( get_option( 'c37_form_gmail_username' ) );
}

function setSMTPSenderName($defname)
{
	return get_option( 'c37_form_smtp_sender_name' );
}

function setSMTPSenderEmail($defemail)
{
	$crypt = new C37_Crypt();
	return $crypt->decrypt( get_option( 'c37_form_smtp_username' ) );
}

function c37ConfigSMTP($mail)
{
	$crypt = new C37_Crypt();
	$username = $crypt->decrypt( get_option( 'c37_form_smtp_username' ) );
	$password = $crypt->decrypt( get_option( 'c37_form_smtp_password' ) );
	$host = get_option( 'c37_form_smtp_host' );
	$port = get_option( 'c37_form_smtp_port' );

	$mail->SMTPDebug=0;
	$mail->CharSet="UTF-8";
	$mail->isSMTP();
	$mail->isHTML(true);
	$mail->Host = $host;
	$mail->SMTPAuth = true;
	$mail->Port = $port;
	$mail->Username = $username;
	$mail->Password = $password;

}

/**
 * @param $mail: wordpress phpmailer object
 */
function c37ConfigGmail($mail)
{
	$crypt = new C37_Crypt();

	$username = $crypt->decrypt( get_option( 'c37_form_gmail_username' ) );
	$password = $crypt->decrypt( get_option( 'c37_form_gmail_password' ) );

	date_default_timezone_set('Etc/UTC');
	$mail->CharSet="UTF-8";
	//Tell C37PHPMailer to use SMTP
	$mail->isSMTP();
	$mail->isHTML(true);

	//Enable SMTP debugging
	// 0 = off (for production use)
	// 1 = client messages
	// 2 = client and server messages
	$mail->SMTPDebug = 0;
	//Ask for HTML-friendly debug output
	$mail->Debugoutput = 'html';

	//Set the hostname of the mail server
	$mail->Host = 'smtp.gmail.com';
	// use
	// $mail->Host = gethostbyname('smtp.gmail.com');
	// if your network does not support SMTP over IPv6

	//Set the SMTP port number - 587 for authenticated TLS, a.k.a. RFC4409 SMTP submission
	$mail->Port = 587;

	//Set the encryption system to use - ssl (deprecated) or tls
	$mail->SMTPSecure = 'tls';

	//Whether to use SMTP authentication
	$mail->SMTPAuth = true;

	//Username to use for SMTP authentication - use full email address for gmail
	$mail->Username = $username;

	//Password to use for SMTP authentication
	$mail->Password = $password;
}



function core37SendMail($type,$username,$password,$host,$port,$fromEmail,
	$senderName,$toEmail,$receiverName,$title,$content, $replyToEmail){

//	return;


	if($type == 'GMAIL'){
		require_once 'gmailsender.php';
		return gmailSenderAPI($username, $password, $senderName, $toEmail, $receiverName, $title, $content, $replyToEmail);
	}
	if($type == 'SMTP'){
		require_once 'smtpsender.php';
		return smtpSenderAPI($username, $password, $host, $port, $fromEmail,
		 $senderName, $toEmail, $receiverName, $title, $content, $replyToEmail);
	}
	echo 'Protocol not support';
	return false;
} 
