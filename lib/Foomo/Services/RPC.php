<?php
namespace Foomo\Services;

use Foomo\Services\RPC\Serializer\SerializerInterface;
use Foomo\Services\RPC\Serializer\PHP;
use Foomo\Services\RPC\Serializer\AMF;

/**
 * Me so restless today ...
 *
 * serve shit good old ugly rpc style
 * currently the proxies are bound to serializers - that might change in the future
 */
class RPC
{
	//---------------------------------------------------------------------------------------------
	// ~ Static variables
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 * @var messages for the current call
	 */
	public static $messages = array();

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * serve a class as a service
	 *
	 * @param stdClass $serviceClassInstance object that provides the services functionality
	 * @param Foomo\Services\RPC\Serializer\SerializerInterface $serializer name of the class that handles the service wrapping, marshalling ...
	 * @param string $package package of a proxy
	 */
	public static function serveClass($serviceClassInstance, SerializerInterface $serializer, $package=null)
	{
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
	 * @todo what's that supposed to do?
	 *
	 * @param type $statusUpdate
	 */
	public static function addStatusUpdate($statusUpdate)
	{

	}
}