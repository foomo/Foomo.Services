<?php

namespace Foomo\Services\Types;

class Exception extends \Exception
{
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
	/**
	 * xdebug messages
	 *
	 * @var string
	 */
	public $xdebug_message;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $msg [optional]
	 * @param integer $code [optional]
	 */
	public function __construct($msg, $code)
	{
		parent::__construct($msg, $code);
		$this->code = $this->getCode();
		$this->message = $this->getMessage();
	}
}