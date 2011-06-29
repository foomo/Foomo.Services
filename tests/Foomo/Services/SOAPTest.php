<?php

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