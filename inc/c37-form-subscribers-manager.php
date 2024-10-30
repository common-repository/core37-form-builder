<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 9/4/16
 * Time: 6:33 AM
 */



class C37FormSubscribersManager
{
	public static function insertSingleDetail($formID, $sessionID, $key, $value, $wpdb)
	{
		$wpdb->insert(
			C37FormDB::TABLE_DETAILS_NAME,
			array(
				C37FormDB::TABLE_DETAILS_COL_FORM_ID     => $formID,
				C37FormDB::TABLE_DETAILS_COL_SESSION_ID  => $sessionID,
				C37FormDB::TABLE_DETAILS_COL_FIELD_KEY   => $key,
				C37FormDB::TABLE_DETAILS_COL_FIELD_VALUE => $value
			)
		);

		return $wpdb->insert_id;
	}

	/**
	 * Insert a session to C37FormDB when a visitor subscribes to forms
	 * @param $formID
	 * @return int, sessionID
	 */
	public static function insertSingleSession($formID, $wpdb)
	{
		$wpdb->insert(
			C37FormDB::TABLE_SESSIONS_NAME,
			array(
				C37FormDB::TABLE_SESSIONS_FORM_ID => $formID
		));

		return $wpdb->insert_id;
	}

	//get all session ID from the session table
	public static function getListOfSessions($formID,$order, $wpdb)
	{
		$query = "SELECT id FROM " . C37FormDB::TABLE_SESSIONS_NAME
		         ." WHERE " . C37FormDB::TABLE_SESSIONS_FORM_ID . "='" . $formID . "'"
		         . ' ORDER BY ' . C37FormDB::TABLE_SESSIONS_DATE_CREATED . ' ' . $order;


		return $wpdb->get_results($query, ARRAY_N);

	}

	public static function getSingleSubscribers($sessionID, $wpdb)
	{
		$query = "SELECT " . C37FormDB::TABLE_DETAILS_COL_FIELD_KEY . "," . C37FormDB::TABLE_DETAILS_COL_FIELD_VALUE . " FROM "
		         . C37FormDB::TABLE_DETAILS_NAME
		         . " WHERE " . C37FormDB::TABLE_DETAILS_COL_SESSION_ID . "=" . $sessionID;

		$data = $wpdb->get_results($query);

		$result = array();
		foreach($data as $d)
		{
			$result[$d->field_key] = $d->field_value;
		}

		return $result;
	}

	public static function getListOfKeys($formID, $wpdb)
	{
		$query = "SELECT DISTINCT " . C37FormDB::TABLE_DETAILS_COL_FIELD_KEY
		         . " FROM " . C37FormDB::TABLE_DETAILS_NAME
				 . " WHERE " . C37FormDB::TABLE_DETAILS_COL_FORM_ID . "=" . $formID;

		return $wpdb->get_results($query, ARRAY_N);
	}

	public static function clearAllSubscribers($formID, $wpdb)
	{
		$query = "DELETE FROM " . C37FormDB::TABLE_DETAILS_NAME
		         . " WHERE " .C37FormDB::TABLE_DETAILS_COL_FORM_ID . "='$formID'";

		$query2 = "DELETE FROM " . C37FormDB::TABLE_SESSIONS_NAME
		         . " WHERE " .C37FormDB::TABLE_SESSIONS_FORM_ID . "='$formID'";

		$wpdb->query($query);
		$wpdb->query($query2);


		return;
	}

}