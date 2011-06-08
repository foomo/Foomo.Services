<?php

namespace Foomo\Services\Types;

/**
 * documents operation success
 *
 */
class OperationSuccess {
	/**
	 * general success code
	 *
	 */
	const CODE_SUCCESS = 0;
	/**
	 * general success message key
	 */
	const MESSAGE_KEY_SUCCESS = 'success';
	/**
	 * general success message
	 */
	const MESSAGE_SUCCESS = 'operation succeeded';
	/**
	 * general failure code
	 *
	 */
	const CODE_FAILURE = 1;
	/**
	 * general failure message key
	 */
	const MESSAGE_KEY_FAILURE = 'failure';
	/**
	 * general failure message
	 */
	const MESSAGE_FAILURE = 'operation failed';
	/**
	 * unix style error code - 0 means success
	 * 
	 * @var integer
	 */
	public $code;
	/**
	 * a programmer firendly message
	 *
	 * @var string
	 */
	public $message;
	/**
	 * a property name for I18n
	 *
	 * @var string
	 */
	public $messageKey;
	/**
	 * cut the crap, thats all I want to know
	 *
	 * @var boolean
	 */
	public $success;
}