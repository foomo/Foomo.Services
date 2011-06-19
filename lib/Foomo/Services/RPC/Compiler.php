<?php

namespace Foomo\Services\RPC;

use Foomo\Services\RPC;
use Foomo\Config;
use Foomo\Flex\Package;
use Foomo\Services\RPC\Serializer\PHP as PHPSerializer;
use Foomo\Services\Renderer\Plain as PlainRenderer;
use Foomo\Services\Reader;

/**
 * tools for rpc client generation
 */
class Compiler {
	/**
	 * generate code for a client
	 *
	 * WARNING this will write over existing code !!!
	 *
	 * @param string $serviceClassName name of the service class
	 * @param string $package as package like 'org.foomo.test'
	 * @param string $srcDir src directory
	 *
	 * @return string
	 */
	public function generateClientCode($serviceClassName, $package = null, $srcDir = null)
	{
		$proxyRenderer = new ASProxyRenderer($this->getRpcService($serviceClassName, $package, $srcDir));
		$reader = new Reflection($serviceClassName, $proxyRenderer);
		$reader->render();
		return $proxyRenderer->output();
	}
	public function compileClient($serviceClassName, $package = null, $srcDir = null)
	{

	}
	/**
	 * get an rpc service
	 *
	 * @param string $serviceClassName
	 * @param string $package
	 * @param string $srcDir
	 *
	 * @return Foomo\Services\RPC
	 */
	private function getRpcService($serviceClassName, $package = null, $srcDir = null)
	{
		if(is_null($srcDir)) {
			$srcDir = Config::getTempDir();
		}
		if(is_null($package)) {
			$package = '';
		}
		$service = new RPC(new $serviceClassName, new PHPSerializer);
		$service->flexPackage = new Package();
		$service->flexPackage->name = $package;
		$service->flexPackage->srcPath = $srcDir;
		return $service;
	}
	/**
	 * get a service documentation, just like this one
	 *
	 * @param string $serviceClassName name of the service class
	 * @return string
	 */
	public function getDocs($serviceClassName)
	{
		$reader = new ServiceReader($serviceClassName, new PlainRenderer);
		return $reader->render();
	}
}