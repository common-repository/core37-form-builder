<?php
//register a custom post type to preview the forms, this is for testing only
include_once 'encrypt.php';


add_action('wp_ajax_core37_get_post_content', 'core37_get_post_content');

function core37_get_post_content()
{
	$data = array();
	parse_str(file_get_contents("php://input"), $data);

	$post = get_post($data['postID']);

	echo json_encode(array(
		'title' => $post->post_title,
		'content' => $post->post_content
	));

	die();
}

add_action('wp_ajax_core37_save_form', 'core37_save_form_callback');

function core37_save_form_callback()
{
	$content = array();
	parse_str(file_get_contents("php://input"), $content);

	//pass the form ID to the editor
	echo C37FormManager::saveForm($content);
	die();
}

/**
 * Get all the form
 */
add_action('wp_ajax_core37_list_forms', 'core_37_list_forms');

function core_37_list_forms()
{
	$forms = C37FormManager::getAllForms();

	echo json_encode($forms);

	die();
}

/**
 * load a form based on form ID
 */

add_action('wp_ajax_core37_load_form', 'core37_load_form');

function core37_load_form()
{
	$data = array();
	parse_str(file_get_contents('php://input'), $data);

	echo json_encode(
		C37FormManager::loadSingleFormForEditing($data['formID'])
	);

	die();
}

/**
 * Delete a form based on its ID
 *
 */

add_action('wp_ajax_core37_delete_form', 'core37_delete_form');


function core37_delete_form()
{
	$data = array();
	parse_str(file_get_contents('php://input'), $data);

	C37FormManager::deleteForm($data['formID']);
	echo 'done';

	die();

}

//get subscribers
add_action('wp_ajax_core37_get_subscribers', 'core37_get_subscribers');

function core37_get_subscribers()
{
	global $wpdb;
	$data = array();
	parse_str(file_get_contents('php://input'), $data);

	//first, get the list of session ID from latest to oldest
	$sessions = C37FormSubscribersManager::getListOfSessions($data['formID'], 'DESC' , $wpdb);

	//$data['start'], $data['limit'],



	//this array stores all the data and pass to the caller
	$details = array();

	for ($i = $data['start']; $i < $data['start'] + $data['limit']; $i++)
	{
		$details[] = array(
			"sessionID" => $sessions[$i][0],
			"info" => C37FormSubscribersManager::getSingleSubscribers(intval($sessions[$i][0]), $wpdb)
		);
	}



	$keysList = C37FormSubscribersManager::getListOfKeys($data['formID'], $wpdb);

	echo json_encode(array(
		'keys' => $keysList,
		'details' => $details
	));


	die();

}

//reset subscribers data
add_action('wp_ajax_c37_clear_forms_subscribers', 'c37_clear_forms_subscribers');

function c37_clear_forms_subscribers()
{
	$data = array();
	parse_str(file_get_contents('php://input'), $data);
	global $wpdb;
	C37FormSubscribersManager::clearAllSubscribers($data['formID'], $wpdb);
}


add_action('wp_ajax_core37_form_admin_save_settings', 'core37_form_admin_save_settings');

function core37_form_admin_save_settings()
{
	//this function accepts data from post content. There is one variable from post array : type is
	//the key to decide what to do next

	$data = array();
	parse_str(file_get_contents("php://input"), $data);

	switch($data['type'])
	{
		case 'recaptcha' :
			update_option('c37_recaptcha_site_key', $data['recaptcha_site_key']);
			update_option('c37_recaptcha_secret_key', $data['recaptcha_secret_key']);
			echo 'success';
			break;

		case 'receiving-email' :
			update_option('c37_receiving_email', $data['email']);
			break;
		case 'gmail':

			$crypt = new C37_Crypt();
			update_option('c37_form_gmail_username', $crypt->encrypt($data['username']));
			update_option('c37_form_gmail_password', $crypt->encrypt($data['password'])); //password encrypted
			update_option('c37_form_mail_sender', 'gmail');
			update_option('c37_form_gmail_sender_name', $data['senderName']);

			break;
		case 'smtp' :
			$crypt = new C37_Crypt();

			update_option('c37_form_smtp_username', $crypt->encrypt($data['username']));
			update_option('c37_form_smtp_password', $crypt->encrypt($data['password']));
			update_option('c37_form_smtp_host', $data['host']);
			update_option('c37_form_smtp_port', $data['port']);
			update_option('c37_form_mail_sender', 'smtp');
			update_option('c37_form_smtp_sender_name', $data['senderName']);
			break;
		case 'use-default-mail' :
			delete_option('c37_form_mail_sender');
			break;

		default:
			break;
	}


	die();




}

//list of form styles
add_action('wp_ajax_core37_get_form_styles', 'core37_get_form_styles');

function core37_get_form_styles()
{
	$styles = array(
		array(
			'class' => 'c37-form-style-1',
			'is_pro' => false
		),
		array(
			'class' => 'c37-form-style-2',
			'is_pro' => false
		),
		array(
			'class' => 'c37-form-style-3',
			'is_pro' => true
		),
		array(
			'class' => 'c37-form-style-4',
			'is_pro' => true
		),
		array(
			'class' => 'c37-form-style-5',
			'is_pro' => true
		),
		array(
			'class' => 'c37-form-style-6',
			'is_pro' => true
		)
	);

	echo json_encode($styles);
	die();
}


add_action('wp_ajax_core37_get_default_parameters', 'core37_get_default_parameters');
function core37_get_default_parameters()
{
	$data = array(
		'imagePlaceholder' => str_replace('/inc/', '',plugin_dir_url(__FILE__)) . '/css/images/image-placeholder.jpg'
	);

	echo json_encode($data);

	die();
}

/**
 * FOR MAIL TEMPLATE
 */
add_action('wp_ajax_core37_form_get_form_details', 'core37_form_get_form_details');

function core37_form_get_form_details()
{
	$data = array();
	parse_str(file_get_contents('php://input'), $data);

	$formSettings = C37FormManager::getFormSettingsObject($data['formID']);


	$returnData = array(
		'fieldNames' => $formSettings->fieldNames
	);

	//now get the to you and to subscribers mail templates
	$toYou = C37FormManager::getForYouMailTemplate($data['formID']);
	$toSubscribers = C37FormManager::getForSubscribersMailTemplate($data['formID']);

	$returnData['toYou'] = array(
		'mailTitle' => $toYou->post_title,
		'mailContent' => $toYou->post_content,
		'mailID' => $toYou->ID
	);

	$returnData['toSubscribers'] = array(
		'mailTitle' => $toSubscribers->post_title,
		'mailContent' => $toSubscribers->post_content,
		'mailID' => $toSubscribers->ID
	);

	echo json_encode($returnData);

	die();
}

add_action ('wp_ajax_update_with_c37', 'update_with_c37');

function update_with_c37()
{

	$data = array();
	parse_str(file_get_contents('php://input'), $data);
	$domain = get_home_url(null, '', 'http');

	$dataToServer = array(
		'email' => $data['email'],
		'transid' => $data['key'],
		'domain' => $domain
	);

	$response = wp_remote_post(base64_decode('aHR0cDovL2NvcmUzNy5jb20vd3Atc2VydmljZXMvYzM3LWZvcm0tYnVpbGRlci9yZWdpc3Rlcl9zZXJ2aWNlLnBocA=='),
		array(
			'method' => 'POST',
			'timeout' => 45,
			'redirection' => 5,
			'httpversion' => '1.0',
			'blocking' => true,
			'headers' => array(),
			'body' => $dataToServer,
			'cookies' => array()
		)
		);




	$toUser = array();
	if (is_wp_error($response))
	{
		$toUser['result'] = 0;
		$toUser['message'] = 'Activation failed! Unknown error. Please contact the developer!';
	} else
	{
		$data = json_decode($response['body']);

		$errorCode = $data->error_code;

		if ($errorCode == "000000")
		{
			$toUser['result'] = 1;
			$toUser['message'] = 'Activation successful!';

			update_option(base64_encode($domain), true);

		} else if ($errorCode == "000001")
		{
			$toUser['result'] = 0;
			$toUser['message'] = 'Activation failed! Wrong email or license key. Please contact the developer';
		}  else if ($errorCode == "000002")
		{
			$toUser['result'] = 0;
			$toUser['message'] = 'Activation failed! Your license was used to activate on a different domain';
		}  else
		{
			$toUser['result'] = 0;
			$toUser['message'] = 'Activation failed! Unknown error. Please contact the developer!';
		}
	}

	echo json_encode($toUser);

	die();
}


function c37_form_xac_nhan()
{
	return get_option(base64_encode(get_home_url(null, '', 'http')));
}

/**
 * Save the mail template and associate it with the form ID
 */
add_action('wp_ajax_core37_form_save_mail_template', 'core37_form_save_mail_template');

function core37_form_save_mail_template()
{

	if (!c37_form_xac_nhan())
	{
		echo json_encode(array(
			'mailID' => -1,
			'message' => 'Please activate the plugin first to use this feature'
		));
		die();
	}

	$data = array();
	parse_str(file_get_contents('php://input'), $data);

	try
	{
		$mailID = C37FormMailTemplate::saveMailTemplate($data);
	} catch (Exception $x)
	{
		echo json_encode(array(
			'mailID' => -1,
			'message' => $x->getMessage()
		));
		die();
	}


	if ($data['mailType'] == 'you')
		update_post_meta($data['formID'], C37FormManager::C37_FORM_TO_YOU_MAIL_ID, $mailID);
	else
		update_post_meta($data['formID'], C37FormManager::C37_FORM_TO_SUBSCRIBERS_MAIL_ID, $mailID);

	echo json_encode(array(
		'message' => 'Email saved!',
		'mailID' => $mailID
	));


	die();
}
