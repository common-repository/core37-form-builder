<?php
/**
 * Created by luis.
 * User: luis
 * Date: 1/4/17
 * Time: 12:15 AM
 */

class C37FormMailTemplate
{
	const C37_MAIL_TEMPLATE_POST_TYPE = 'c37_f_mail_template';

	public static function registerPostType()
	{
		register_post_type(
			self::C37_MAIL_TEMPLATE_POST_TYPE,
			array(
				'labels' => array(
					'name' => __('C37 Form Mail Templates', 'core37-form-mail-template'),
					'singular_name' => __('C37 Form Mail Templates', 'core37-form-mail-template')
				),
				'public' => false,
				'has_archive' => true,
				'show_ui' => false,
			)

		);
	}

	public static function saveMailTemplate($content)
	{
		//insert post to dp
		$mailID = wp_insert_post(
			array(
				'ID' => $content['mailID'],
				'post_content' => $content['mailContent'],
				'post_type' => self::C37_MAIL_TEMPLATE_POST_TYPE,
				'post_name' => $content['mailTitle'],
				'post_title' => $content['mailTitle'],
				'post_status' => 'publish'
			)
		);

		return $mailID;
	}

	public static function deleteMailTemplate($mailID)
	{
		wp_delete_post($mailID, true);
	}


}

