<?php
/*
	Plugin Name: Core37 Form Builder
	Plugin URI: http://www.core37.com/
	Description: Create forms easily with drag, drop form builder. Unlimited responsive forms with beautiful designs
	Author: core37, codingpuss
	Version: 1.2.27
	Author URI: http://www.core37.com/
	Text Domain: core37-form-builder
*/
include_once 'functions.php';

register_activation_hook(__FILE__, 'core37_form_activate');