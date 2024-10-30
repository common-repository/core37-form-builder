<?php

include_once 'c37-submit.php';
class C37FormManager
{
	const C37_FORM_POST_TYPE = 'core37_form';
	const C37_FORM_META_ELEMENT_ACTIONS = 'elements_actions';
	const C37_FORM_META_SETTINGS = 'c37_form_settings';
	const C37_FORM_META_VALIDATION = 'c37_form_validation';
	const C37_FORM_META_CUSTOM_CSS = 'c37_form_css_code';//this contains pure code, use in front
	const C37_FORM_META_CUSTOM_CSS_OBJECT = 'c37_form_css_object';//this meta stores a JS object, use to edit later in editor
	const C37_FORM_TO_YOU_MAIL_ID = 'c37_form_to_you_mail_id';
	const C37_FORM_TO_SUBSCRIBERS_MAIL_ID = 'c37_form_to_subscribers_mail_id';


	public static function registerPostType()
	{
		register_post_type(
			self::C37_FORM_POST_TYPE,
			array(
				'labels' => array(
					'name' => __('C37 Form', 'core37-form-builder'),
					'singular_name' => __('C37 Form', 'core37-form-builder')
				),
				'public' => false,
				'has_archive' => true,
				'show_ui' => false,
			)

		);
	}

	/**
	 * Load a form to display to end user, mainly use in shortcode
	 * @param $formID
	 *
	 * @return string
	 */

	public static function loadFormHTML($formID)
	{
		//check if post exists
		if (get_post($formID) == null)
			return "";

		$formContent = get_post($formID, ARRAY_A, 'raw');
		$formSettingsString = get_post_meta($formID, self::C37_FORM_META_SETTINGS, true);

		if ($formSettingsString == "")
			return "";

		$formSettings = json_decode($formSettingsString);



		$width = '';

		if (!($formSettings->action== ""))
			$action = 'action="'.$formSettings->action.'"';
		else
		{
//			$action = 'action="'.admin_url('admin-post.php').'"';
			$action = 'action=""';
		}


		if (is_numeric($formSettings->width))
			$width .= 'style="width: '.$formSettings->width.'px;"';

		$formCode = '<form '.
		            'id="'.$formSettings->cssID.'"'.
		            'name="'.$formSettings->cssID.'"'.
		            $width.' method="'.
		            $formSettings->method.'"'. $action.
		            ' class="c37-form c37-container '.$formSettings->presetCSSStyle.' " '.
		            'enctype="multipart/form-data">'.
			rawurldecode($formContent['post_content']).
			'<input type="hidden" name="form_id" value="'.$formID.'"/>'.
			'<input type="hidden" name="form_type" value="core37-form"/>'.
			'<input class="pott" name="pott" type="text" />'.
			'<input name="action" value="core37_save_form_submit" type="hidden" />';


		//if the form was posted using normal method, include the post success message or
		//error message to it

		//get error message if any
		$errorMessages = '';
		$inBodyStyle = '';//in body style sheet
		if ( Core37Submit::getInstance()->getMessages() != null && $formID == $_POST['form_id'])
		{
			foreach (Core37Submit::getInstance()->getMessages() as $message)
			{
				$inBodyStyle .= '#'.$formSettings->cssID.' input[name='.$message['name'].'] {
					background:  #ffcccc;
				}';
				$errorMessages .= '<li><i class="fa fa-close"></i> '.$message['message'] . '</li>';
			}

			$formCode .= '<ul class="c37-error-message">'.$errorMessages.'</ul> <style>'.$inBodyStyle.'</style>';

		}


		else if (isset($_POST['form_type']) && $_POST['form_type'] == 'core37-form')
		{
			if ($formSettings->afterSubmitMessage == null)
			{
				$formSettings->afterSubmitMessage = 'Your form was successfully submitted!';
			}
			//else print the success message comes with the if form posted
			$formCode = $formCode .'<div class="c37-success-message">'.$formSettings->afterSubmitMessage.'</div>';
		}

		$formCode .= self::getFormValidationRules($formID);
		$formCode .= self::getElementsActions($formID);
		//also, print the action object out



		return $formCode .'<div id="c37-loading-icon"><img src="'.plugin_dir_url(__FILE__).'../css/images/loading-message.gif" /></div></form>';

	}

	/**
	 * Load single form for editing, this one is different from the @link loadSingleForm
	 * @param $formID
	 *
	 * @return array
	 */
	public static function loadSingleFormForEditing($formID)
	{

		$form = get_post($formID, ARRAY_A, 'raw');

		return array(
			"formData" => $form,
			"elementsActions" => get_post_meta($formID, self::C37_FORM_META_ELEMENT_ACTIONS),
			"formSettings" => get_post_meta($formID, self::C37_FORM_META_SETTINGS),
			"formValidation" => get_post_meta($formID, self::C37_FORM_META_VALIDATION),
			"formCSSObject" => get_post_meta($formID, self::C37_FORM_META_CUSTOM_CSS_OBJECT),
			"formCSSCode" => get_post_meta($formID, self::C37_FORM_META_CUSTOM_CSS)
		);

	}

	public static function getAllForms()
	{
		$data = array(
			'post_type' => self::C37_FORM_POST_TYPE,
			'orderby'          => 'date',
			'order'            => 'DESC',
			'posts_per_page' => -1,

		);

		$result = new WP_Query($data);

		$forms = array();

		foreach($result->posts as $post)
			$forms[] = array(
				'title' => $post->post_title,
				'id' =>$post->ID
			);

		return $forms;
	}

	/**
	 * Save form when user click on save form button
	 * @param $content
	 *
	 * @return mixed
	 */
	public static function saveForm($content)
	{

		//insert post to dp
		$formID = wp_insert_post(
			array(
				'ID' => $content['formID'],
				'post_content' => $content['formContent'],
				'post_type' => self::C37_FORM_POST_TYPE,
				'post_name' => $content['formName'],
				'post_title' => $content['formName'],
				'post_status' => 'publish',
			)
		);

		update_post_meta($formID, self::C37_FORM_META_ELEMENT_ACTIONS, $content['elementsActions']);
		update_post_meta($formID, self::C37_FORM_META_SETTINGS, $content['formSettings']);
		update_post_meta($formID, self::C37_FORM_META_VALIDATION, $content['formValidation']);
		update_post_meta($formID, self::C37_FORM_META_CUSTOM_CSS, $content['formCSSCode']);
		update_post_meta($formID, self::C37_FORM_META_CUSTOM_CSS_OBJECT, $content['formCSSObject']);

		return $formID;
	}

	public static function deleteForm($formID)
	{
		wp_delete_post($formID, true);
		delete_post_meta($formID, self::C37_FORM_META_SETTINGS);
		delete_post_meta($formID, self::C37_FORM_META_ELEMENT_ACTIONS);
		delete_post_meta($formID, self::C37_FORM_META_VALIDATION);
		delete_post_meta($formID, self::C37_FORM_META_CUSTOM_CSS);
		delete_post_meta($formID, self::C37_FORM_META_CUSTOM_CSS_OBJECT);
	}

	public static function getFormCustomCSS($formID)
	{
		return rawurldecode(get_post_meta($formID, self::C37_FORM_META_CUSTOM_CSS, true));
	}

	public static function getElementsActions($formID)
	{
		if ( get_post_meta($formID, self::C37_FORM_META_ELEMENT_ACTIONS, true) == "")
			return "";
		return '<script class="hidden">var elementsActions = elementsActions || {}; elementsActions['.$formID.'] = '.rawurldecode(get_post_meta($formID, self::C37_FORM_META_ELEMENT_ACTIONS, true)) . '</script>';
	}

	public static function getFormValidationRules($formID)
	{
		return '<script class="hidden">var formsValidation = formsValidation || {}; formsValidation['.$formID.'] = '.rawurldecode(get_post_meta($formID, self::C37_FORM_META_VALIDATION, true)) . '</script>';
	}

	//get the object that contains form settings (core37Form.formSettings) in string format
	public static function getFormSettingsString($formID)
	{
		return get_post_meta($formID, self::C37_FORM_META_SETTINGS, true);
	}

	//get the object that contains form settings (core37Form.formSettings) in object format
	public static function getFormSettingsObject($formID)
	{
		return json_decode(get_post_meta($formID, self::C37_FORM_META_SETTINGS, true));
	}

	public static function getForYouMailTemplate($formID)
	{
		$mailID = get_post_meta($formID, self::C37_FORM_TO_YOU_MAIL_ID, true);

		if ($mailID == "")
			return null;

		return get_post($mailID);
	}

	public static function getForSubscribersMailTemplate($formID)
	{
		$mailID = get_post_meta($formID, self::C37_FORM_TO_SUBSCRIBERS_MAIL_ID, true);

		if ($mailID == "")
			return null;

		return get_post($mailID);
	}
}