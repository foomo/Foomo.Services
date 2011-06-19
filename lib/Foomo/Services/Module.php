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

	/**
	 * @return string
	 */
	public static function getTmpDir()
	{
		$filename = 'tmp' . DIRECTORY_SEPARATOR . self::NAME;
		self::validateResourceDir($filename);
		return \Foomo\Config::getVarDir() . DIRECTORY_SEPARATOR . $filename;
	}

	/**
	 * @param string $filename
	 */
	public static function validateResourceDir($filename)
	{
		$resource = \Foomo\Modules\Resource\Fs::getVarResource(\Foomo\Modules\Resource\Fs::TYPE_FOLDER, $filename);
		if (!$resource->resourceValid()) $resource->tryCreate();
		if (!\file_exists(\Foomo\Config::getVarDir() . DIRECTORY_SEPARATOR . $filename)) throw new \Exception('Resource ' . $filename . ' does not exits');
	}
}