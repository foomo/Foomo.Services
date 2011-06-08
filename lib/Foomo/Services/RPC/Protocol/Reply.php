<?php

namespace Foomo\Services\RPC\Protocol;

/**
 * reply for a rpc call
 */
class Reply {
	/**
	 * head of the call
	 *
	 * @var Foomo\Services\RPC\Protocol\Reply\Head
	 */
	public $head;
	/**
	 * all the calls
	 *
	 * @var Foomo\Services\RPC\Protocol\Reply\MethodReply[]
	 */
	public $methodReplies = array();
}