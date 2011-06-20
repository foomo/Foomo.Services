<?php

namespace Foomo\Services\Mock;

/**
 * a mock exception
 */
class Exception extends \Exception {
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * error code
	 *
	 * @var integer
	 */
	public $code;
	/**
	 * message
	 *
	 * @var string
	 */
	public $message;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $msg
	 */
	public function __construct($msg) {
		parent::__construct($msg);
		$this->code = $this->getCode();
		$this->message = $this->getMessage();
	}

}