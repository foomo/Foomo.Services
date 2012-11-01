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

use Foomo\Services\RPC\Serializer\SerializerInterface;

/**
 * currently the proxies are bound to serializers - that might change in the future
 *
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class RPC
{
	/**
	 * @var string
	 */
	private $authDomain;
	/**
	 * @var string
	 */
	private $authDomainDev;
	/**
	 * me service class instance
	 *
	 * @var stdClass
	 */
	private $serviceInstance;
	/**
	 * @var string
	 */
	private $namespace;
	/**
	 *
	 * @var SerializerInterface
	 */
	private $serializer;

	//---------------------------------------------------------------------------------------------
	// ~ Static variables
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 * @var mixed[] for the current call
	 */
	public static $messages = array();

	private function __construct($serviceInstance)
	{
		$this->serviceInstance = $serviceInstance;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * serve a class as a service
	 *
	 * @deprecated user RPC::create()
	 * @param stdClass $serviceClassInstance object that provides the services functionality
	 * @param \Foomo\Services\RPC\Serializer\SerializerInterface $serializer name of the class that handles the service wrapping, marshalling ...
	 * @param string $package package of a proxy
	 */
	public static function serveClass($serviceClassInstance, SerializerInterface $serializer, $package=null)
	{
		trigger_error('Please refactor the service\'s enpoint.php for ' . get_class($serviceClassInstance), E_USER_DEPRECATED);
		echo \Foomo\MVC::run(new RPC\Frontend($serviceClassInstance, $serializer, $package));
	}

	/**
	 * @param mixed $message
	 */
	public static function addMessage($message)
	{
		self::$messages[] = $message;
	}

	/**
	 * create a RPC service
	 *
	 * @param stdClass $serviceInstance service object
	 *
	 * @return \Foomo\Services\RPC
	 */
	public static function create($serviceInstance)
	{
		return new self($serviceInstance);
	}
	/**
	 * what namespace to use on the client side
	 *
	 * @param string $namespace client namespace
	 *
	 * @return \Foomo\Services\RPC
	 */
	public function clientNamespace($namespace)
	{
		$this->namespace = $namespace;
		return $this;
	}
	/**
	 * what to serialize with - this currently also serves as the base
	 * to select a client code generator
	 *
	 * @param \Foomo\Services\RPC\Serializer\SerializerInterface $serializer
	 *
	 * @return \Foomo\Services\RPC
	 */
	public function serializeWith(SerializerInterface $serializer)
	{
		$this->serializer = $serializer;
		return $this;
	}

	/**
	 * protect service with Foomo\BasicAuth
	 *
	 * @param string $authDomain
	 *
	 * @return \Foomo\Services\RPC
	 */
	public function requestAuth($authDomain='default')
	{
		$this->authDomain = $authDomain;
		return $this;
	}
	/**
	 * protect service docs / proxy generation with Foomo\BasicAuth
	 *
	 * @param string $authDomain
	 *
	 * @return \Foomo\Services\RPC
	 */
	public function requestAuthForDev($authDomain='default')
	{
		$this->authDomainDev = $authDomain;
		return $this;
	}



	/**
	 * run it
	 */
	public function run()
	{
		$frontend = new RPC\Frontend(
			$this->serviceInstance,
			$this->serializer,
			$this->namespace,
			$this->authDomain,
			$this->authDomainDev
		);
		echo \Foomo\MVC::run($frontend);
	}
}