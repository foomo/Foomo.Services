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

class ClientTest extends \PHPUnit_Framework_TestCase
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var Foomo\Services\Mock\ServiceProxy
	 */
	protected $proxy;

	//---------------------------------------------------------------------------------------------
	// ~ Initialization
	//---------------------------------------------------------------------------------------------

	public function setUp()
	{
		if(!\Foomo\Session::getEnabled()) {
			$this->markTestSkipped('session not enabled');
		} else {
			$this->proxy = new \Foomo\Services\Mock\ServiceProxy();
		}
	}

	//---------------------------------------------------------------------------------------------
	// ~ Test methods
	//---------------------------------------------------------------------------------------------

	public function testSessionPersistence()
	{
		$this->assertEquals(3, $this->iPlusPlus($this->proxy));
		$this->proxy->deleteCookies();
		$this->assertEquals(3, $this->iPlusPlus($this->proxy));
	}

	public function testCookiePassing()
	{
		$proxyOne = new \Foomo\Services\Mock\ServiceProxy();
		$this->iPlusPlus($proxyOne);
		$proxyTwo = new \Foomo\Services\Mock\ServiceProxy();
		$proxyTwo->setCookies($proxyOne->getCookies());
		$this->assertEquals(7, $this->iPlusPlus($proxyTwo));
	}

	public function testCookiePassingMultiServerInstance()
	{
		$endpointTemplate = \Foomo\Utils::getServerUrl() . \Foomo\ROOT_HTTP . '/modules/services/services/mockServicePhp%id%.php/Foomo.Services.RPC/serve';

		// those two ones have the same session instance on the server
		$proxyA = new \Foomo\Services\Mock\ServiceProxy(\str_replace('%id%', 'A', $endpointTemplate));
		$proxyAA = new \Foomo\Services\Mock\ServiceProxy(\str_replace('%id%', 'AA', $endpointTemplate));

		// this one has a different session object instance within the same session
		$proxyB = new \Foomo\Services\Mock\ServiceProxy(\str_replace('%id%', 'B', $endpointTemplate));

		$actualA = $this->iPlusPlus($proxyA);

		// share the session
		$proxyB->setCookies($proxyA->getCookies());
		$proxyAA->setCookies($proxyA->getCookies());


		$actualAA = $this->iPlusPlus($proxyAA);
		$actualB = $this->iPlusPlus($proxyB);
		//var_dump($actualA, $actualAA, $actualB);
		$this->assertEquals($actualA + 4, $actualAA);
		$this->assertEquals(3, $actualA);
	}

	public function testParallelCall()
	{
		$methodCalls = array();
		$parallelity = 4;
		for($i=0;$i<$parallelity;$i++) {
			$call = new RPC\Protocol\Call\MethodCall();
			$call->method = 'addNumbers';
			$call->id = 'testCall-' . $i;
			$call->arguments = array(rand(0,100), rand(0,100));
			$methodCalls[] = $call;
		}
		$methodReplies = $this->proxy->parallelCall($methodCalls);
		for($i=0;$i<$parallelity;$i++) {
			$call = $methodCalls[$i];
			$reply = $methodReplies[$i];
			$this->assertEquals($call->arguments[0] + $call->arguments[1], $reply->value);
		}
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	private function iPlusPlus($proxy)
	{
		$proxy->iPlusPlus();
		$proxy->iPlusPlus();
		$proxy->iPlusPlus();
		return $proxy->iPlusPlus();
	}
}