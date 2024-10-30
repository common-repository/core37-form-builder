<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 9/11/16
 * Time: 4:17 PM
 */
//this is a class contains temporary message to pass between add_action since they don't support
//parameters

/**
 * Class Core37TempMessage
 * message could be anything
 */
class Core37TempMessage
{
	private $message = array();
	private static $instance;
	public static function getInstance()
	{
		if (self::$instance == null)
			self::$instance = new self;

		return self::$instance;
	}

	public function __construct() {
	}

	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function getMessage()
	{
		return $this->message;
	}

}