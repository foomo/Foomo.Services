<?php

namespace Foomo\Services;

/**
 * A soap server
 *
 * @todo workaround wsdl.cache_path, check caching in general
 */
class SOAP {

	public static function serveClass($serviceClassInstance, $asPackage = 'org.foomo.zugspitze.services.namespaces.php', $asSrcDir = null)
	{

		\Foomo\HTMLDocument::getInstance()->addStylesheets(array(
			\Foomo\ROOT_HTTP . '/css/module.css',
			\Foomo\ROOT_HTTP . '/modules/' . Module::NAME . '/css/module.css',
		));

		echo \Foomo\MVC::run(
			new SOAP\Frontend(
				$serviceClassInstance,
				$asPackage,
				$asSrcDir
			)
		);

	}























}
