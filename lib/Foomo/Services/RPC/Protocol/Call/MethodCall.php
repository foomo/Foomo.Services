<?php

namespace Foomo\Services\RPC\Protocol\Call;

/**
 * serializing a method call
 *
 */
class MethodCall {
	/**
	 * id of the method call
	 *
	 * @var string
	 */
	public $id;
	/**
	 * name of the method to be called
	 *
	 * @var string
	 */
	public $method;
	/**
	 * the method call arguments
	 *
	 * @var Foomo\Services\RPC\Protocol\Call\MethodArgument[]
	 */
	public $arguments;
}