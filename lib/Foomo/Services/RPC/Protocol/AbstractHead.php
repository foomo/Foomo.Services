<?php

namespace Foomo\Services\RPC\Protocol;

abstract class AbstractHead {
	/**
	 * id of the call
	 *
	 * @var string
	 */
	public $callId;
	/**
	 * sessionId
	 *
	 * @var string
	 */
	public $sessionId;
}