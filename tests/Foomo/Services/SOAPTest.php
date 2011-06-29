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

class SOAPTest extends \PHPUnit_Framework_TestCase
{
	//---------------------------------------------------------------------------------------------
	// ~ Hooks
	//---------------------------------------------------------------------------------------------

	public function setUp()
	{
		if(!\Foomo\Session::getEnabled() || php_sapi_name() == 'cli') {
			$this->markTestSkipped('session not enabled');
		}
	}

	//---------------------------------------------------------------------------------------------
	// ~ Test methods
	//---------------------------------------------------------------------------------------------

	public function testSimpleCall()
	{
		$this->assertEquals(7, $this->getClient()->addNumbers(3,4));
	}

	/*
	 * @todo: make real test out of it
	public function testComplexCall()
	{
		var_dump(
			$this->getClient()->getSomeFunkyStars(new Mock\FunkyStar()),
			$this->getClient()->getAFunkyStar()
		);
	}
	 */

	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @return SoapClient
	 */
	private function getClient()
	{
		$domainConfig = new \Foomo\Services\SOAP\Client\DomainConfig;
		$domainConfig->wsdlUrl = \Foomo\Utils::getServerUrl() . \Foomo\ROOT_HTTP . '/modules/' . \Foomo\Services\Module::NAME . '/services/mockServiceSOAP.php/Foomo.Services.SOAP/wsdl';
		$domainConfig->classMap = array('FoomoServicesMockFunkyStar' => 'Foomo\\Services\\Mock\\FunkyStar');
		return $domainConfig->getSoapClient();
	}
}