<?php

namespace Foomo\Services\RPC\Protocol;

class Call {
	/**
	 * envelope / header style data
	 *
	 * @var Foomo\Services\RPC\Protocol\Call\Head
	 */
	public $head;
	/**
	 * (multiple) method calls
	 *
	 * @var Foomo\Services\RPC\Protocol\Call\MethodCall[]
	 */
	public $calls = array();
}