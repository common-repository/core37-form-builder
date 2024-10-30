<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 9/3/16
 * Time: 1:01 PM
 */

/**
 * This file handle POST request for forms. There are three scenario
 * 1. Request send by ajax. The script will do:
 *  - validate the posted data
 *  - save the posted data if valid
 *  - perform tasks such as send email notification, send auto reply to subscribers
 *  - send response to caller and let the caller handle subsequent tasks (such as redirect to other URL)
 *
 * 2. Request by normal form submit (js disabled, has attachment)
 *  - validate the posted data
 *  - save the posted data if valid
 *  - perform tasks such as send email notification, send auto reply to subscribers, send data to another URL
 *  - if requested, redirect the subscribers to requested URL
 */


include_once 'c37-submit.php';
include_once 'lib/gump.class.php';
include_once 'encrypt.php';
include_once 'mailapi.php';

/**
 * validate input (again) and insert to db if validation OK
 */
include_once 'c37-form-subscribers-manager.php';


add_action('wp', 'core37_process_post_request');
function core37_process_post_request()
{

	//only process the data if it is sent by core37 form
	if (isset($_POST['form_type'])  && $_POST['form_type'] == 'core37-form')
	{
		//send file date to save_form_submit
		try
		{
			core37_save_form_submit($_FILES, $_POST);
		} catch (Exception $e)
		{
			echo json_encode(array(
				'error' => 1,
				'message' => $e->getMessage()
			));

			die();
		}

	}
}


/**
 * Filter the mail content type. Make the file sending in HTML format
 */
function core37_set_html_mail_content_type() {
	return 'text/html';
}

/**
 * @param $templatePost: The mail template stored in db, one for webmaster, one for subscribers
 * @param $dataArray: the form data submitted by subscribers
 * @return array of subject and content
 */
function replaceActualFormData($templatePost, $dataArray)
{
	if ($templatePost == null)
		return null;

	//the content of the template were rawurlencoded, need to decode first
	$mailSubject = rawurldecode($templatePost->post_title);
	$mailContent = rawurldecode($templatePost->post_content);

	foreach($dataArray as $key=>$value)
	{
		$mailSubject = str_replace('[['.$key.']]', $value, $mailSubject);
		$mailContent = str_replace('[['.$key.']]', $value, $mailContent);
	}

	return array(
		'mailSubject' => $mailSubject,
		'mailContent' => $mailContent
	);
}


function core37_save_form_submit($fileUpload, $data)
{

	$formSettingsString = get_post_meta($data['form_id'], C37FormManager::C37_FORM_META_SETTINGS, true);
	$formSettings = json_decode($formSettingsString, true);

	//the core37submit purpose is to convey the error message in non-ajax form submit
	$submit = Core37Submit::getInstance();
	$submit->setFormID($data['form_id']);
	$gump = new C37FormGump();


	//check re-captcha
	if (isset($data['g-recaptcha-response']))
	{
		$captchaValidationResult= validateReCaptcha($data['g-recaptcha-response']);

		if (!$captchaValidationResult)
		{
			if (isset($data['by_ajax']))
			{
				echo json_encode(array(
					'error' => 1,
					'message' => 'Your DID NOT solve the captcha'
				));

				die();
			} else
			{
				$submit->addErrorMessages($gump->get_readable_errors(false));
				return;
			}

		}
	}

	//if the validation object is set (new version only) proceed with gump validation
	if(isset($formSettings['GUMPString']))
	{
		$gumpRules = $formSettings['GUMPString'];

		$data= $gump->sanitize($data);

		$gump->validation_rules($gumpRules);


		$validatedData = $gump->run($data, true);


		if ($validatedData === false)
		{
			//if the form is submitted by ajax, print the error out and stop script execution
			if (isset($data['by_ajax']))
			{
				echo json_encode(array(
					"error" => 1,
					"message" => $gump->get_readable_errors(true)
				));

				die();
			}

			//else, add the error to Submit array and display on site
			$submit->addErrorMessages($gump->get_readable_errors(false));

			return;
		}

	}


	$filesPath = array();
	$uploadFieldName = "";
	/**
	 * HANDLE FILE UPLOAD
	 * There are cases when user doesn't upload a file and if the file is not required, that's OK to
	 * continue.
	 *
	 * If the file is required, then throw and error and ask the user to upload the file
	 *
	 */


	if (count($fileUpload) > 0)
	{
		/**
		there are two cases here, if there is one file uploaded, the data would be like this:
		array(1) {
		 ["attachment"]=> array(5) {
		      ["name"]=> string(36) "2a6d1ad4e27183d072f586e44ff54db2.jpg"
		      ["type"]=> string(10) "image/jpeg"
		      ["tmp_name"]=> string(36) "/Applications/MAMP/tmp/php/phpfp9a1k"
		      ["error"]=> int(0)
		      ["size"]=> int(41946)
		      }
		 }

		if there are more than 1 files, the data would be like this
		array(1) {
		 ["attachment"]=> array(5) {
		      ["name"]=> array(2) { [0]=> string(36) "f1.jpg" [1]=> string(38) "f2.png" }
		      ["type"]=> array(2) { [0]=> string(10) "image/jpeg" [1]=> string(9) "image/png" }
		      ["tmp_name"]=> array(2) { [0]=> string(36) "/Applications/MAMP/tmp/php/phpT9ES3X" [1]=> string(36) "/Applications/MAMP/tmp/php/phpr1IMVm" }
		      ["error"]=> array(2) { [0]=> int(0) [1]=> int(0) }
		      ["size"]=> array(2) { [0]=> int(41946) [1]=> int(50057) }
		 }
		 }
		 */


		if ( ! function_exists( 'wp_handle_upload' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}
		$uploadOverrides = array( 'test_form' => false );

		/**
		 * These two array store the path and error messages (if any when saving files to WP)
		 */
		$uploadError = array();


		$uploadFieldName = key($fileUpload);
		$uploadData = reset($fileUpload);

		//if single file upload
		if (!is_array($uploadData['name']))
		{
			$singleFile = array(
				'name' => $uploadData['name'],
				'type' => $uploadData['type'],
				'tmp_name' => $uploadData['tmp_name'],
				'error' => $uploadData['error'],
				'size' => $uploadData['size']
			);

			$movefile = wp_handle_upload( $singleFile , $uploadOverrides );


			if ( $movefile && !isset( $movefile['error'] ) ) {
				$filesPath[] = $movefile['url'];
			} else {

				/**
				 * don't add the error message (missing file) if:
				 * 1. There is no rule
				 * 2. There is no rule for the file
				 * 3. The file is not required
				 */

				if ($singleFile['error'] == 4)
				{
					//do nothing
				} else
				{
					$uploadError[] = array(
						'name' => $uploadData['name'],
						'message' => $movefile['error']
					);
				}
			}
		} else //in case of multiple file uploads
		{
			//check if any error available
			for ($i = 0; $i < count($uploadData['error']); $i++)
			{
				if ($uploadData['error'][$i] != 0)
					$submit->addErrorMessages(array(
						$uploadFieldName => $uploadData['name'][$i] .': '. $uploadData['error'][$i]
					));
			}

			//if there are error while uploading, stop the script
			if (count($submit->getMessages()) > 0)
				return;

			//if file upload OK, proceed to moving to upload folder
			for ($i = 0; $i < count($uploadData['name']); $i++)
			{
				$singleFile = array(
					'name' => $uploadData['name'][$i],
					'type' => $uploadData['type'][$i],
					'tmp_name' => $uploadData['tmp_name'][$i],
					'error' => $uploadData['error'][$i],
					'size' => $uploadData['size'][$i]

				);


				//move file to upload folder using php built-in function
				$movefile = wp_handle_upload( $singleFile , $uploadOverrides );

				if ( $movefile && ! isset( $movefile['error'] ) ) {
					$filesPath[] = $movefile['url'];
				} else if ($movefile['error']){
					/**
					 * don't add the error message (missing file) if:
					 * 1. There is no rule
					 * 2. There is no rule for the file
					 * 3. The file is not required
					 */
					if ($singleFile['error'] == 4)
					{
						//do nothing
					} else
					{
						$uploadError[$uploadData['name'][$i]] =  $movefile['error'];
					}


				}
			}
		}

		//if file upload error, send the error report and return
		if (count($uploadError) > 0)
		{
			$submit->addErrorMessages($uploadError);
			return;
		}

	}

	//if everything is OK, continue to process
	global $wpdb;

	$sessionID = C37FormSubscribersManager::insertSingleSession($data['form_id'], $wpdb);

	//when post data is sent, other non-valuable-to-user data will be sent too
	//list of parameters will be omitted when saving to db
	$omitKeys = array('undefined', 'action', 'by_ajax', 'form_id', 'form_type', 'pott', 'g-recaptcha-response', 'acceptance');


	//this is the body of the message sent to admin
	$notificationMessage = '';

	foreach ( $data as $key => $value )
	{
		if (in_array($key, $omitKeys))
			continue;

		if (is_array($value))
			$value = json_encode($value);

		$notificationMessage .= "<strong>".$key . " :</strong> " . $value . "\r\n<br>";

		C37FormSubscribersManager::insertSingleDetail($data['form_id'], $sessionID, $key, $value, $wpdb);
	}

	//if file upload exists, save URL to form data
	if(count($filesPath) > 0)
	{
		C37FormSubscribersManager::insertSingleDetail($data['form_id'], $sessionID, $uploadFieldName, json_encode($filesPath), $wpdb);

		$fileHTML = ''; //to use in email
		foreach ($filesPath as $f)
		{
			$fileHTML .= '<li>'.$f.'</li>';
		}
		$data[$uploadFieldName] = '<ul>'.$fileHTML.'</ul>';
	}

	$subjectInput = '';

	$replyToEmail = '';

	if (isset($formSettings['subjectField']) && ($formSettings['subjectField'] != "") && isset($data[$formSettings['subjectField']]))
		$subjectInput = $data[$formSettings['subjectField']];

	if (isset($formSettings['replyToField']) && ($formSettings['replyToField'] != "") && isset($data[$formSettings['replyToField']]))
		$replyToEmail = $data[$formSettings['replyToField']];


	/**
	 * now parse the form setting and perform the tasks required
	 * 1. send notification (if user sets so)
	 * 2. send auto reply (only available in pro version)
	 * 3. send data to another URL (only available in pro version)
	 *
	 */


	/**
	 * If form submit by ajax, return the result as a JSON string
	 * If form submit normally (js disabled or have file to upload, reload the URL with notification)
	 */


	if ($formSettings['afterSubmitMessage'] == null)
	{
		$formSettings['afterSubmitMessage'] = 'Your form was successfully submitted!';
	}

	/**
	 * Send notification to user if it is set to be true
	 */
	$webmasterEmail = getWebmasterEmail();
	if ($formSettings['sendNotification'])
	{
		//get mail template if any
		$toYouTemplate = C37FormManager::getForYouMailTemplate($data['form_id']);

		if ($toYouTemplate != null)
		{
			$toYouMailData = replaceActualFormData($toYouTemplate, $data);
			$subject = $toYouMailData['mailSubject'];
			$message = $toYouMailData['mailContent'];

		} else
		{
			if ($subjectInput != "")
				$subject = $subjectInput;
			else
				$subject = '['.get_bloginfo('name').']A visitor has submitted a form';

			$message = "Hello, \r\n <br>"
			           ."A visitor has submitted a form on your site. Here are the details: \r\n <br>"
			           .$notificationMessage . "\r\n <br><br>"
			           . "Thanks for using core37 form builder!";
		}

		sendEmail( $webmasterEmail, $subject, $message, $replyToEmail );
	}

	if ($formSettings['sendAutoReply'])
	{
		$autoreplyMailTemplate = C37FormManager::getForSubscribersMailTemplate($data['form_id']);

		$autoreplyMailData = replaceActualFormData($autoreplyMailTemplate, $data);
		if ($replyToEmail != '' && $autoreplyMailTemplate != null)
		{
			//$replyToEmail is subscriber's email
			sendEmail($replyToEmail, $autoreplyMailData['mailSubject'], $autoreplyMailData['mailContent'], $webmasterEmail);
		}
	}

	//check if the form is sent via ajax or normal post
	if (isset($data['by_ajax']))
	{
		echo json_encode(array(
			'url'=> $formSettings['afterSubmitURL'],
			'message' => $formSettings['afterSubmitMessage'],
			'error' => 0
		));

		die();

	} else
	{

		if ($formSettings['afterSubmitURL'] != '')
			header('Location: '. $formSettings['afterSubmitURL']);
	}

}


/**
 * @param $toEmail
 * @param $subject
 * @param $message
 * @param $replyToEmail
 *
 * @throws Exception
 */
function sendEmail( $toEmail, $subject, $message, $replyToEmail ) {

	add_filter( 'wp_mail_content_type', 'core37_set_html_mail_content_type' );

	$result = "";

	if ( get_option( 'c37_form_mail_sender' ) == 'gmail' ) {

		add_action('phpmailer_init', 'c37ConfigGmail', 100000, 1);
		add_filter( 'wp_mail_from', 'setGmailSenderEmail' );
		add_filter( 'wp_mail_from_name', 'setGmailSenderName' );

		$result = wp_mail(
			$toEmail,
			$subject,
			$message,
			array( 'Reply-To: ' . $replyToEmail . ' <' . $replyToEmail . '>' ),
			array()
		);

		//remove the filters and action
		remove_filter('wp_mail_from', 'setGmailSenderEmail');
		remove_filter('wp_mail_from_name', 'setGmailSenderName');
		remove_action('phpmailer_init', 'c37ConfigGmail');

	} else if ( get_option( 'c37_form_mail_sender' ) == 'smtp' ) {

		add_action('phpmailer_init', 'c37ConfigSMTP', 100000, 1);

		add_filter( 'wp_mail_from', 'setSMTPSenderEmail' );
		add_filter( 'wp_mail_from_name', 'setSMTPSenderName' );

		$result = wp_mail(
			$toEmail,
			$subject,
			$message,
			array( 'Reply-To: ' . $replyToEmail . ' <' . $replyToEmail . '>' ),
			array()
		);


		remove_filter('wp_mail_from', 'setSMTPSenderEmail');
		remove_filter('wp_mail_from_name', 'setSMTPSenderName');
		remove_action('phpmailer_init', 'c37ConfigSMTP');

	} else
	{
		$result = wp_mail(
			$toEmail,
			$subject,
			$message,
			array( 'Reply-To: ' . $replyToEmail . ' <' . $replyToEmail . '>' ),
			array()
		);
	}


	remove_filter( 'wp_mail_content_type', 'core37_set_html_mail_content_type' );

	return $result;
}

/**
 * @return string email of the webmaster (default or registed to receive email when there is submission)
 */
function getWebmasterEmail() {
	$toEmail = get_option( 'c37_receiving_email' );
	if ( $toEmail == false )
		$toEmail = get_option( 'admin_email' );

	return $toEmail;
}

function validateReCaptcha($recaptchaResponse)
{
	//check if recaptcha available
	$recaptchaSecret = get_option('c37_recaptcha_secret_key');

	$serverURL = "https://www.google.com/recaptcha/api/siteverify";

	$captchaData = array(
		'secret' => $recaptchaSecret,
		'response' => $recaptchaResponse
	);

	$options = array(
		'http' => array(
			'header' => "Content-type: application/x-www-form-urlencoded\r\n",
			'method' => 'POST',
			'content' => http_build_query($captchaData)
		)
	);

	$context = stream_context_create($options);

	$result =  json_decode(file_get_contents($serverURL, false, $context));
	return $result->success;

}