<?php

namespace Foomo\Services;

/**
 * A soap server
 *
 * @todo workaround wsdl.cache_path, check caching in general
 */
class SOAP
{
	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	public static function serveClass($serviceClassInstance)
	{
		echo \Foomo\MVC::run(new SOAP\Frontend($serviceClassInstance));
	}
}