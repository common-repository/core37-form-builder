<?php
include_once 'inc/c37-form-manager.php';
include_once 'inc/c37-mail-template.php';
include_once 'inc/captcha.php';
include_once 'inc/ajax.php';
include_once 'inc/shortcode.php';
include_once 'inc/activate.php';
include_once 'inc/process.php';

define('C37_FORM_MENU_SLUG', 'core37-form-builder');
define('c37FormDevMode', false);
define('c37FormPro', false);

add_filter('widget_text', 'do_shortcode');
add_action('admin_menu', 'core37_form_add_menu');


add_action('init', 'core37_form_create_post_type');

function core37_form_create_post_type()
{
	C37FormManager::registerPostType();
	C37FormMailTemplate::registerPostType();
}

function c37_form_register_backend_scripts()
{
	wp_register_script('c37-form-editor-script', plugins_url('js/min/backend.min.js', __FILE__), array('jquery','underscore', 'backbone', 'jquery-ui-core', 'jquery-ui-accordion', 'jquery-ui-draggable', 'jquery-ui-droppable', 'jquery-ui-sortable', 'jquery-ui-tabs', 'jquery-ui-autocomplete'));
	wp_register_script('c37-form-settings-page', plugins_url('js/min/settings.min.js', __FILE__), array('jquery', 'jquery-ui-core','jquery-effects-core','jquery-ui-accordion','jquery-ui-tabs'));
	wp_register_script('c37-form-subscribers-script', plugins_url('js/min/subscribers.min.js', __FILE__), array('jquery', 'underscore'));
	wp_register_script('c37-form-mail-script', plugins_url('js/pro/mail-templates.min.js', __FILE__), array('jquery', 'underscore', 'backbone', 'jquery-ui-core', 'jquery-effects-core','jquery-ui-tabs'), false, true);
}


function core37_form_add_menu()
{
	add_menu_page('Core37 Form Builder', 'C37 Form Builder', 'edit_posts', 'core37-form-builder', 'core37_form_ui_main');
	add_submenu_page(C37_FORM_MENU_SLUG, 'Make Form', 'Make Form', 'edit_posts', C37_FORM_MENU_SLUG . '-make', 'core37_form_ui_make_form');
	add_submenu_page(C37_FORM_MENU_SLUG, 'Subscribers', 'Subscribers', 'edit_posts', C37_FORM_MENU_SLUG . '-subscribers', 'core37_form_ui_subscribers');
	if (c37FormPro)
		add_submenu_page(C37_FORM_MENU_SLUG, 'Mail templates', 'Mail templates', 'edit_posts', C37_FORM_MENU_SLUG . '-mail-templates', 'core37_form_ui_mail_templates');
}

function core37_form_enqueue_editor_styles()
{
	wp_enqueue_style('editor-styles', plugins_url('css/editor-styles.min.css', __FILE__));
}

function core37_form_enqueue_subscribers_styles()
{
	wp_enqueue_style('subscribers-styles', plugins_url('css/subscribers.min.css', __FILE__));
}
function core37_form_enqueue_subscribers_script()
{
	if (c37FormDevMode)
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('underscore');
		wp_enqueue_script('messages-script', plugins_url('js/back/messages.js', __FILE__));
		wp_enqueue_script('toastr-script', plugins_url('js/back/toastr.min.js', __FILE__));
		wp_enqueue_script('swal-script', plugins_url('js/back/sweetalert.min.js', __FILE__));
		wp_enqueue_script('subscribers-script', plugins_url('js/back/subscribers.js', __FILE__));
	} else
	{
		wp_enqueue_script('c37-form-subscribers-script');
	}


}

function core37_form_enqueue_mail_templates_styles()
{
	wp_enqueue_style('c37-mail-templates-styles', plugins_url('css/mail-templates.min.css', __FILE__));
}

function core37_form_enqueue_mail_templates_scripts()
{
	if (c37FormDevMode)
	{
		wp_enqueue_script('jquery');
		wp_enqueue_script('underscore');
		wp_enqueue_script('backbone');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-effects-core');
		wp_enqueue_script('jquery-ui-tabs');

		wp_enqueue_script('messages-script', plugins_url('js/back/messages.js', __FILE__));
		wp_enqueue_script('toastr-script', plugins_url('js/back/toastr.min.js', __FILE__));
		wp_enqueue_script('swal-script', plugins_url('js/back/sweetalert.min.js', __FILE__));
		wp_enqueue_script('global-x-script', plugins_url('js/back/global.js', __FILE__), array('underscore', 'backbone'));
		wp_enqueue_script('common-script', plugins_url('js/back/common.js', __FILE__));
		wp_enqueue_script('mail-templates-script', plugins_url('js/back/mail-templates.js', __FILE__));
	} else
	{
		wp_enqueue_script('c37-form-mail-script');
	}
}

function core37_form_enqueue_settings_page_styles()
{

	wp_enqueue_style('settings-page-styles', plugins_url('css/settings.css', __FILE__));
}
function core37_form_enqueue_settings_page_script()
{

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-effects-core');;
	wp_enqueue_script('jquery-ui-accordion');
	wp_enqueue_script('jquery-ui-tabs');

	if (c37FormDevMode)
	{
		wp_enqueue_script('messages-script', plugins_url('js/back/messages.js', __FILE__));
		wp_enqueue_script('toastr-script', plugins_url('js/back/toastr.min.js', __FILE__));
		wp_enqueue_script('settings-page-script0', plugins_url('js/back/settings.js', __FILE__));
	} else
	{
		wp_enqueue_script('c37-form-settings-page');
	}

}

function core37_form_enqueue_editor_scripts()
{


	wp_enqueue_media();


	if (c37FormDevMode)
	{
		//in dev mode
		wp_enqueue_script('jquery');
		wp_enqueue_script('underscore');
		wp_enqueue_script('backbone');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-effects-core');
		wp_enqueue_script('jquery-ui-accordion');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-droppable');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery-ui-tabs');
		wp_enqueue_script('jquery-ui-autocomplete');

		wp_enqueue_script('editor-script1000', plugins_url('js/back/toastr.min.js', __FILE__));
		wp_enqueue_script('editor-script11231', plugins_url('js/lib/star-rating/jquery.barrating.js', __FILE__));
		wp_enqueue_script('editor-script1', plugins_url('js/back/messages.js', __FILE__));
		wp_enqueue_script('editor-script2', plugins_url('js/back/sweetalert.min.js', __FILE__));
		wp_enqueue_script('editor-script3', plugins_url('js/back/global.js', __FILE__), array('jquery', 'backbone'));
		wp_enqueue_script('editor-script4s', plugins_url('js/back/validation.js', __FILE__));
		wp_enqueue_script('editor-script4', plugins_url('js/back/common.js', __FILE__));
		wp_enqueue_script('editor-script5', plugins_url('js/back/edit-forms.js', __FILE__));
		wp_enqueue_script('editor-script26', plugins_url('js/back/elements-views.js', __FILE__));
		wp_enqueue_script('editor-script6', plugins_url('js/back/elements-edit-views.js', __FILE__));
		wp_enqueue_script('editor-script7ds', plugins_url('js/back/editor.js', __FILE__));
		wp_enqueue_script('editor-script7s', plugins_url('js/back/edit-menus.js', __FILE__));
		wp_enqueue_script('editor-script7', plugins_url('js/back/save-form.js', __FILE__));
	} else
	{
		//in production mode
		wp_enqueue_script('c37-form-editor-script');
	}

}

add_action('wp_enqueue_scripts', 'core37_form_load_frontend_scripts');

function core37_form_load_frontend_scripts()
{
	wp_enqueue_style('c37-front-styles', plugins_url('css/front-styles.min.css', __FILE__));


	if (c37FormDevMode)
	{
		//dev mode
		wp_enqueue_script('jquery');
		wp_enqueue_script('underscore');
		wp_enqueue_script('backbone');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-datepicker');

		wp_enqueue_script('front-script1dd231', plugins_url('js/lib/star-rating/jquery.barrating.js', __FILE__));
		wp_enqueue_script('front-script1xlx', plugins_url('js/back/messages.js', __FILE__));
		wp_enqueue_script('front-script1dxlx', plugins_url('js/back/common.js', __FILE__));
		wp_enqueue_script('front-script1', plugins_url('js/back/toastr.min.js', __FILE__));
		wp_enqueue_script('front-script2', plugins_url('js/front/global.js', __FILE__));
		wp_enqueue_script('front-script3', plugins_url('js/front/validator-parsley.js', __FILE__));
		wp_enqueue_script('front-script11', plugins_url('js/front/modernizr-datepicker.js', __FILE__));
		wp_enqueue_script('front-script33', plugins_url('js/front/pikaday.js', __FILE__));
		wp_enqueue_script('front-script12', plugins_url('js/front/pikaday.jq.js', __FILE__));
		wp_enqueue_script('front-script1231', plugins_url('js/front/actions.js', __FILE__));
		wp_enqueue_script('front-script12121', plugins_url('js/front/process.js', __FILE__));

	} else
	{
		//production mode
		wp_register_script('c37-form-front-script', plugins_url('js/min/frontend.min.js', __FILE__), array('jquery',  'jquery-ui-core','jquery-ui-datepicker', 'underscore', 'backbone' ));
		wp_enqueue_script('c37-form-front-script');
	}

}

add_action('admin_enqueue_scripts', 'core37_form_load_backend_scripts');

function core37_form_load_backend_scripts()
{
	$currentScreen = get_current_screen();
	c37_form_register_backend_scripts();

	if (stripos($currentScreen->base, 'core37-form-builder-make') !== false)
	{
		core37_form_enqueue_editor_styles();
		core37_form_enqueue_editor_scripts();
	} else if(stripos($currentScreen->base, 'core37-form-builder-subscribers') !== false)
	{
		core37_form_enqueue_subscribers_styles();
		core37_form_enqueue_subscribers_script();
	} else if (stripos($currentScreen->base, 'toplevel_page_core37-form-builder') !== false)
	{
		core37_form_enqueue_settings_page_styles();
		core37_form_enqueue_settings_page_script();
	} else if (stripos($currentScreen->base, 'core37-form-builder-mail-templates') !== false)
	{
		core37_form_enqueue_mail_templates_styles();
		core37_form_enqueue_mail_templates_scripts();
	}

}

/*
 * function load the main page of plugin
 */
function core37_form_ui_main()
{
	include_once(plugin_dir_path(__FILE__) .'pages/main-page.php');
}

function core37_form_ui_make_form()
{
	include_once(plugin_dir_path(__FILE__) .'pages/make-form.php');

}

function core37_form_ui_subscribers()
{
	include_once(plugin_dir_path(__FILE__) .'pages/subscribers.php');

}

function core37_form_ui_mail_templates()
{
	include_once(plugin_dir_path(__FILE__) .'pages/mail-templates.php');
}