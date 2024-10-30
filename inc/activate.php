<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 9/3/16
 * Time: 8:31 PM
 */
include_once 'c37-form-db.php';
include_once 'c37-form-subscribers-manager.php';
function core37_form_activate()
{
	C37FormDB::createSessionTable();
	C37FormDB::createDetailsTable();
}