<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 9/1/16
 * Time: 2:35 PM
 */

include_once 'temp-message.php';
add_shortcode('core37_form', 'core37_form_display');

function core37_form_display($atts)
{
	shortcode_atts(array(
		'id' => '',
		'elementsActions' => '',
		'errorMessage' => ''
	), $atts);


	$formCode = '';
	$formCode .=C37FormManager::loadFormHTML($atts['id']);

	return $formCode;
}

//include the styles of forms in header

add_action('wp_head', 'c37_show_custom_css');

function c37_show_custom_css()
{

	$thePost = get_post(get_the_ID());

	if (!is_object($thePost))
		return;


	$postContent = $thePost->post_content;
	$matchedShortcodes = array();

	preg_match_all("/\\[core37_form.*[0-9]{1,10}\\]/", $postContent, $matchedShortcodes);

	$style = '';
	//print the custom css
	foreach($matchedShortcodes as $shortcode)
	{
		if (count($shortcode) == 0)
			continue;

		$formID = str_replace("[core37_form id=", "", $shortcode[0]);
		$formID = str_replace("]", "", $formID);

		$style .= C37FormManager::getFormCustomCSS($formID);
	}


	echo '<style id="c37-css">' . $style . '</style>';

}