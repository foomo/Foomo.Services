<?php

namespace Foomo\Services\RPC\Protocol\Reply;

/**
 * reply to a method call
 */
class MethodReply {
	/**
	 * id of the method call
	 *
	 * @var string
	 */
	public $id;
	/**
	 * return value
	 *
	 * @var mixed
	 */
	public $value;
	/**
	 * server side exception
	 *
	 * @var mixed
	 */
	public $exception;
	/**
	 * messages from the server
	 * 
	 *   possibly many of them
	 *   possibly many types
	 * 
	 * @var array
	 */
	public $messages;
}