<?php

namespace Foomo\Services;

use Foomo\Services\RPC\Serializer\PHP;
use Foomo\Services\RPC\Protocol\Call\MethodCall;
use Foomo\Services\RPC\Server;
use Foomo\Services\Mock\FunkyStar;

class HandlerTest extends \PHPUnit_Framework_TestCase {
	/**
	 * my mock
	 *
	 * @var RPCServiceHandlerMockService
	 */
	private $mockService;
	public function setUp()
	{
		$this->mockService = new \Foomo\Services\Mock\Service();
	}
	/**
	 * prepare call(s)
	 *
	 * @param  RPCCallMethodCall[] $calls
	 *
	 * @return RPCCall
	 */
	private function makeMockCall($calls)
	{
		static $i = 0;
		$request = new RPC\Protocol\Call();
		$request->head = new RPC\Protocol\Call\Head();
		$request->head->classVersion = 1;
		$request->head->callId = $i ++;
		$request->head->className = get_class($this->mockService);
		$request->calls = $calls;
		return $request;
	}
	public function testFuncWithTwoIntegers()
	{
		$methodCalls = array();
		for($i = 0;$i<100;$i++) {
			$methodCall = new MethodCall();
			$methodCall->id = 'call-' . $i;
			$methodCall->arguments = array(2, $i);
			$methodCall->method = 'addNumbers';
			$methodCalls[] = $methodCall;
		}

		$serializer = new PHP();

		$mockCall = $this->makeMockCall($methodCalls);
		$input = $serializer->serialize($mockCall);
		$result = $serializer->unserialize(Server::run($this->mockService, $serializer, $input));
		/* @var $result RPCCallReply */
		$this->assertEquals($result->head->callId, $mockCall->head->callId, 'call and reply must have the same head callId');
		for($i = 0; $i<100;$i++) {
			$methodCall = $methodCalls[$i];
			$methodCallResult = $result->methodReplies[$i];
			/* @var $methodCallResult RPCCallMethodReply */
			$this->assertEquals($methodCallResult->value, $methodCall->arguments[0] + $methodCall->arguments[1]);
			$this->assertEquals($methodCallResult->id, $methodCall->id, 'method id and reply id must match');
		}

	}
	public function testCallException()
	{
		$methodCall = new MethodCall();
		$methodCall->id = 'call-1';
		$methodCall->arguments = array();
		$methodCall->method = 'makeException';

		$methodCalls = array();
		$methodCalls[] = $methodCall;

		$serializer = new PHP();

		$mockCall = $this->makeMockCall($methodCalls);
		$input = $serializer->serialize($mockCall);
		$success = false;
		$result = $serializer->unserialize(Server::run($this->mockService, $serializer, $input));
		/* @var $result RPCCallReply */
		$this->assertEquals($result->methodReplies[0]->exception->message, 'expected exception');
	}
	public function testCallMessages()
	{
		$methodCall = new MethodCall();
		$methodCall->id = 'call-1';
		$methodCall->arguments = array(new FunkyStar());
		$methodCall->method = 'getSomeFunkyStars';

		$methodCalls = array();
		$methodCalls[] = $methodCall;

		$serializer = new PHP();

		$mockCall = $this->makeMockCall($methodCalls);
		$input = $serializer->serialize($mockCall);
		$result = $serializer->unserialize(Server::run($this->mockService, $serializer, $input));
		/* $methodReply RPCCallMethodReply */
		$methodReply = $result->methodReplies[0];
		$this->assertType('Foomo\Services\Mock\Message', $methodReply->messages[0]);
		$this->assertType('string', $methodReply->messages[1]);
	}
}