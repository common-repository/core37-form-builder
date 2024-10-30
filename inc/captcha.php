<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 9/27/16
 * Time: 4:19 PM
 */

add_action('wp_head', 'c37_form_include_recaptcha');
add_action('admin_head', 'c37_form_include_recaptcha');

function c37_form_include_recaptcha()
{
	echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
}

