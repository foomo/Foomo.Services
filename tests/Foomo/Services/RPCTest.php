<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Services;

use Foomo\Services\RPC\Serializer\PHP;
use Foomo\Services\RPC\Protocol\Call\MethodCall;
use Foomo\Services\RPC\Server;
use Foomo\Services\Mock\FunkyStar;

class RPCTest extends \PHPUnit_Framework_TestCase
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var Foomo\Services\Mock\Service
	 */
	private $mockService;

	//---------------------------------------------------------------------------------------------
	// ~ Initialization
	//---------------------------------------------------------------------------------------------

	public function setUp()
	{
		$this->mockService = new \Foomo\Services\Mock\Service();
	}

	//---------------------------------------------------------------------------------------------
	// ~ Test methods
	//---------------------------------------------------------------------------------------------

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
		$this->assertInstanceOf('Foomo\Services\Mock\Message', $methodReply->messages[0]);
		$this->assertInternalType('string', $methodReply->messages[1]);
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	/**
	 * prepare call(s)
	 *
	 * @param  RPCCallMethodCall[] $calls
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
}
