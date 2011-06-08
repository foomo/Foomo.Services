<?php

namespace Foomo\Services\RPC\Protocol\Call;

class Head extends \Foomo\Services\RPC\Protocol\AbstractHead {
	/**
	 * name of the user
	 *
	 * @var string
	 */
	public $username;
	/**
	 * password of the user
	 *
	 * @var string
	 */
	public $password;
	/**
	 * name of the class to be called
	 *
	 * @var string
	 */
	public $className;
	/**
	 * version of the class
	 *
	 * @var float
	 */
	public $classVersion;
}