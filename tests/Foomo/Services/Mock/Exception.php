<?php

namespace Foomo\Services\Mock {
	/**
	 * a mock exception
	 */
	class Exception extends \Exception {
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
		public function __construct($msg)
		{
			parent::__construct($msg);
			$this->code = $this->getCode();
			$this->message = $this->getMessage();
		}
	}
}