<?php
namespace Foomo\Services;

use Foomo\Modules\ModuleBase;

/**
 * services module
 */
class Module extends ModuleBase
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	const NAME = 'Foomo.Services';

	//---------------------------------------------------------------------------------------------
	// ~ Overriden methods
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public static function initializeModule()
	{
	}

	/**
	 * @return string
	 */
	public static function getDescription()
	{
		return 'Services in many flavours, but without pain';
	}

	/**
	 * @return array
	 */
	public static function getResources()
	{
		return array(
			\Foomo\Modules\Resource\Module::getResource('Foomo', self::VERSION),
			\Foomo\Modules\Resource\PhpModule::getResource('amf')
		);
	}
}