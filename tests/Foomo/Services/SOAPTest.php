<?php

namespace Foomo\Services;

use SoapClient;
use Foomo\Services\SOAP\Client\DomainConfig as SoapConfig;

class SOAPTest extends \PHPUnit_Framework_TestCase {
	public function setUp()
	{
		if(!\Foomo\Session::getEnabled()) {
			$this->markTestSkipped('session not enabled');
		}
	}
	/**
	 * @return SoapClient;
	 */
	private function getClient()
	{
		$domainConfig = new SoapConfig;
		
		$domainConfig->wsdlUrl = 
			\Foomo\Utils::getServerUrl() . \Foomo\ROOT_HTTP . '/modules/' . 
			\Foomo\Services\Module::NAME . #
			'/services/mockServiceSOAP.php/Foomo.Services.SOAP/wsdl'
		;
		$domainConfig->classMap = array(
			'FoomoServicesMockFunkyStar' => 'Foomo\\Services\\Mock\\FunkyStar'
		);
		return $domainConfig->getSoapClient();
	}
	public function testSimpleCall()
	{
		$this->assertEquals(7, $this->getClient()->addNumbers(3,4));
	}
	public function testComplexCall()
	{
		var_dump(
			$this->getClient()->getSomeFunkyStars(new Mock\FunkyStar()),
			$this->getClient()->getAFunkyStar()
		);
	}
	
}