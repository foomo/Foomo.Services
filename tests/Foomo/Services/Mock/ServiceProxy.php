<?php

/**
 * DO NOT EDIT !!!
 *
 * the contents of this file were autogenerated on http://jan on 2010-11-26 20:29:56
 *
 */
// RPC protocol and value object classes


namespace Foomo\Services\Mock;

class ServiceProxy extends \Foomo\Services\RPC\Client {
	const VERSION = 1;
	/**
	 * construct a client
	 *
	 * @param RPCSerializerInterface $serializer
	 * @param string $targetClass name of the class to talk to
	 * @param string $endPoint uri of the service
	 */
	public function __construct()
	{
		$endPoint = \Foomo\Utils::getServerUrl() . \Foomo\ROOT_HTTP . '/modules/' . \Foomo\Services\Module::NAME . '/services/mockService.php/Foomo.Services.RPC/serve';
		$targetClass = 'Foomo\Services\Mock\Service';
		$serializer = new \Foomo\Services\RPC\Serializer\PHP();
		parent::__construct($serializer, $targetClass, $endPoint);
	}

	/**
	 * add two numbers
	 *
	 * @param integer $numberOne
	 * @param integer $numberTwo
	 *
	 * @return integer the sum of the two numbers
	 */
	public function addNumbers($numberOne, $numberTwo)
	{
		return $this->callServer(self::VERSION, 'addNumbers', array($numberOne, $numberTwo));
	}

	/**
	 * generate an exception
	 *
	 * @return boolean it will pop an exception anyways
	 */
	public function makeException()
	{
		return $this->callServer(self::VERSION, 'makeException', array());
	}

	/**
	 * test a funky star
	 *
	 * @param Foomo\Services\Mock\FunkyStar $star
	 *
	 * @return Foomo\Services\Mock\FunkyStar[]
	 */
	public function getSomeFunkyStars(Foomo\Services\Mock\FunkyStar $star)
	{
		return $this->callServer(self::VERSION, 'getSomeFunkyStars', array($star));
	}

	/**
	 * session test
	 *
	 * @return integer
	 */
	public function iPlusPlus()
	{
		return $this->callServer(self::VERSION, 'iPlusPlus', array());
	}

	public function __call($name, $arguments)
	{
		throw new Exception('function ' . $name . ' does not exist - maybe you need to recompile and update your client', 1);
	}

}