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
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	public static function initializeModule() 
	{
	}
	
	public static function getDescription()
	{
		return 'Services in many flavours, but without pain';
	}
	
	public static function getResources()
	{
		return array(
			\Foomo\Modules\Resource\Module::getResource('Foomo.Flash', self::VERSION),
			//\Foomo\Modules\Resource\Module::getResource('Zugspitze', self::VERSION),
			\Foomo\Modules\Resource\PhpModule::getResource('amf')
		);
	}
}