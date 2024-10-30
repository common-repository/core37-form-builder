<?php
/**
 * Created by PhpStorm.
 * User: luis
 * Date: 9/8/16
 * Time: 5:19 PM
 */

class Core37Submit
{
	private static $instance;
	private $messages = array();
	private $formID = 0;

	/**
	 * add a single error message
	 * @param $error
	 */
	public function addErrorMessage($error)
	{
		$this->messages[] = $error;
	}

	/**
	 * add array of error messages
	 * @param $messages
	 */
	public function addErrorMessages($messages)
	{
		$this->messages = array_merge($this->messages, $messages);
	}

	public function getMessages()
	{
		return $this->messages;
	}

	public function getFormID()
	{
		return $this->formID;
	}

	public function setFormID($formID)
	{
		$this->formID = $formID;
	}


	private function __construct() {
	}

	public static function getInstance()
	{
		if (empty(self::$instance))
		{
			self::$instance = new self;
		}

		return self::$instance;
	}
}