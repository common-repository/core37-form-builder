<?php

class C37_Crypt {

	private $data;
	private $key = '3gppXkVq9b3WFzT4kO8';
	private $module;
	private $complexTypes = false;
	const HMAC_ALGORITHM = 'sha1';
	const DELIMITER = '#';
	const MCRYPT_MODULE = 'rijndael-192';
	const MCRYPT_MOD = 'cfb';
	const PREFIX = 'Crypt';
	const MINIMUM_KEY_LENGTH = 8;

	function __construct() {
		$this->checkEnvironment();
		$this->setModule(mcrypt_module_open(self::MCRYPT_MODULE, '', self::MCRYPT_MOD, ''));
	}

	private function checkEnvironment() {
		if ((!extension_loaded('mcrypt')) || (!function_exists('mcrypt_module_open'))) {
			throw new Exception('The PHP mcrypt extension must be installed for encryption', 1);
		}
		if (!in_array(self::MCRYPT_MODULE, mcrypt_list_algorithms())) {
			throw new Exception("The cipher used self::MCRYPT_MODULE does not appear to be supported by the installed version of libmcrypt", 1);
		}
	}

	public function setData($data) {
		$this->data = $data;
	}

	public function setKey($key) {
		if (strlen($key) < self::MINIMUM_KEY_LENGTH) {
			$message = sprintf('The secret key must be a minimum %s character long', self::MINIMUM_KEY_LENGTH);
			throw new Exception($message, 1);
		}
		$this->key = $key;
	}

	private function setModule($module) {
		$this->module = $module;
	}

	public function setComplexTypes($complexTypes) {
		$this->complexTypes = $complexTypes;
	}

	private function getData() {
		return $this->data;
	}

	private function getKey() {
		return $this->key;
	}

	/**
	 * Returns the mcrypt module resource
	 *
	 * @return resource
	 * @author Osman Üngür
	 */
	private function getModule() {
		return $this->module;
	}

	private function getComplexTypes() {
		return $this->complexTypes;
	}

	public function encrypt($rawString) {
		mt_srand();
		$init_vector = mcrypt_create_iv(mcrypt_enc_get_iv_size($this->getModule()), MCRYPT_RAND);
		$key = substr(sha1($this->getKey()), 0, mcrypt_enc_get_key_size($this->getModule()));
		mcrypt_generic_init($this->getModule(), $key, $init_vector);
		if ($this->getComplexTypes()) {
			$this->setData(serialize($rawString));
		}
		$cipher = mcrypt_generic($this->getModule(), $rawString);
		$hmac = hash_hmac(self::HMAC_ALGORITHM, $init_vector . self::DELIMITER . $cipher, $this->getKey());
		$encoded_init_vector = base64_encode($init_vector);
		$encoded_cipher = base64_encode($cipher);
		return self::PREFIX . self::DELIMITER . $encoded_init_vector . self::DELIMITER . $encoded_cipher . self::DELIMITER . $hmac;
	}

	public function decrypt($rawString) {
		if ($rawString == "" || $rawString == null || empty($rawString))
			return '';

		$elements = explode(self::DELIMITER, $rawString);
		if (count($elements) != 4 || $elements[0] != self::PREFIX) {
			$message = sprintf('The given data does not appear to be encrypted with %s', __CLASS__);
			throw new Exception($message, 1);
		}
		$init_vector = base64_decode($elements[1]);
		$cipher = base64_decode($elements[2]);
		$given_hmac = $elements[3];
		$hmac = hash_hmac(self::HMAC_ALGORITHM, $init_vector . self::DELIMITER . $cipher, $this->getKey());
		if ($given_hmac != $hmac) {
			return '';
//			throw new Exception('The given data appears tampered or corrupted', 1);
		}
		$key = substr(sha1($this->getKey()), 0, mcrypt_enc_get_key_size($this->getModule()));
		mcrypt_generic_init($this->getModule(), $key, $init_vector);
		$result = mdecrypt_generic($this->getModule(), $cipher);
		if ($this->getComplexTypes()) {
			return unserialize($result);
		}
		return $result;
	}

	public function __destruct() {
		@mcrypt_generic_deinit($this->getModule());
		mcrypt_module_close($this->getModule());
	}

}
