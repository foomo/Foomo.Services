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
class RPC {
	/**
	 * serve a class as a service
	 *
	 * @param stdClass $serviceClassInstance object that provides the services functionality
	 * @param Foomo\Services\RPC\Serializer\SerializerInterface $serializer name of the class that handles the service wrapping, marshalling ...
	 * @param string $package package of a proxy
	 * @param string $srcDir where to compile to
	 */
	public static function serveClass(
		$serviceClassInstance, 
		SerializerInterface $serializer = null,
		$package = null,
		$srcDir = null
	)
	{
		if(is_null($serializer)) {
			$serializer = new PHP();
		}
		// default AS3 namespace
		if(($serializer instanceof AMF) && is_null($package)) {
			$package = 'com.bestbytes.zugspitze.services.namespaces.php';
		}
		\Foomo\HTMLDocument::getInstance()->addStylesheets(array(
			\Foomo\ROOT_HTTP . '/css/module.css',
			\Foomo\ROOT_HTTP . '/modules/' . Module::NAME . '/css/module.css',
		));

		echo \Foomo\MVC::run(
			new RPC\Frontend(
				$serviceClassInstance,
				$serializer,
				$package,
				$srcDir
			)
		);
	}
	/**
	 *
	 * @var messages for the current call
	 */
	public static $messages = array();
	/**
	 *
	 * @param mixed $message 
	 */
	public static function addMessage($message)
	{
		self::$messages[] = $message;
	}
	public static function addStatusUpdate($statusUpdate)
	{
		
	}
}