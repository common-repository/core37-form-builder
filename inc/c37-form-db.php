<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 9/3/16
 * Time: 2:56 PM
 */

class C37FormDB
{

	const TABLE_SESSIONS_NAME = 'core37_form_sessions';
	const TABLE_SESSIONS_COL_ID = 'id';
	const TABLE_SESSIONS_FORM_ID = 'form_id';
	const TABLE_SESSIONS_DATE_CREATED = 'date_created';

	const TABLE_DETAILS_NAME = 'core37_form_kv';
	const TABLE_DETAILS_COL_FORM_ID = 'form_id';
	const TABLE_DETAILS_COL_SESSION_ID = 'session_id';
	const TABLE_DETAILS_COL_FIELD_KEY = 'field_key';
	const TABLE_DETAILS_COL_FIELD_VALUE = 'field_value';



	public static function createSessionTable()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$createQuery = "CREATE TABLE IF NOT EXISTS ".self::TABLE_SESSIONS_NAME."  (".
		                self::TABLE_SESSIONS_COL_ID. " int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, ".
		               self::TABLE_SESSIONS_FORM_ID. " int(11) NOT NULL, ".
		               self::TABLE_SESSIONS_DATE_CREATED. " TIMESTAMP DEFAULT CURRENT_TIMESTAMP ".
		               ") $charset_collate";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($createQuery);
	}


	/**
	 * Create table store details about form submit
	 */
	public static function createDetailsTable()
	{
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();
		$createQuery = "CREATE TABLE IF NOT EXISTS ".self::TABLE_DETAILS_NAME."  (".
		self::TABLE_DETAILS_COL_FORM_ID." int(11) NOT NULL, ".
		self::TABLE_DETAILS_COL_SESSION_ID." int(11) NOT NULL, ".
		self::TABLE_DETAILS_COL_FIELD_KEY." VARCHAR(255) NOT NULL, ".
		self::TABLE_DETAILS_COL_FIELD_VALUE." text NOT NULL ".
		") $charset_collate";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($createQuery);
	}


	public function insertRecord($formID)
	{

	}

	/**
	 * @param $formID
	 * @param $limit
	 * Get all users(subscribers) of a specific form, with a limit, default = 10
	 */
	public function getUser($formID, $limit = 10)
	{

	}


}