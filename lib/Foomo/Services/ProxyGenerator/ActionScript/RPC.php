<?php

namespace Foomo\Services\ProxyGenerator\ActionScript;

use Foomo\Services\RPC as RPCService;
use Foomo\Services\Reflection\ServiceObjectType;
use Foomo\Services\Reader;
use Exception;
use Foomo\Services\Reflection;

/**
 * renders AS RPC Proxy clients rocking Zugspitze
 */
class RPC extends AbstractGenerator
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	public $templateFolder = 'amf';
	/**
	 * @var string[]
	 */
	public $packageFolders = array('calls', 'operations', 'events', 'commands');

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * compile and report
	 *
	 * @param stdClass $serviceClassInstance
	 * @param string $targetPackage
	 * @param string $targetSrcDir
	 *
	 * @return Foomo\Services\ProxyGenerator\ActionScript\Report
	 */
	public static function generateSrc($serviceClassInstance, $targetPackage, $targetSrcDir)
	{
		$generator = self::getGenerator($serviceClassInstance, $targetPackage, $targetSrcDir);
		$report = new Report();
		$report->generator = $generator;
		$report->swcFilename = $generator->getSWCFilename();
		$report->tgzFilename = $generator->getTGZFilename();
		try {
			$report->report = self::render(get_class($serviceClassInstance), $generator);
			$report->report = 'Source generation success : ' . PHP_EOL . PHP_EOL . $report->report . PHP_EOL;
			$report->success = true;
		} catch (Exception $e) {
			$report->success = false;
			$report->report .= 'Source generation failed:' . PHP_EOL;
			$report->report .= '----------- ' . $e->getMessage() . ' ----------' . PHP_EOL . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
		}
		return $report;
	}

	/**
	 * @param stdClass $serviceClassInstance
	 * @param string $targetPackage
	 * @param string $targetSrcDir
	 * @return Foomo\Services\ProxyGenerator\ActionScript\Report
	 */
	public static function packSrc($serviceClassInstance, $targetPackage, $targetSrcDir)
	{
		$report = self::generateSrc($serviceClassInstance, $targetPackage, $targetSrcDir);
		if ($report->success) {
			try {
				$packingReport = $report->generator->packTgz();
				$report->report .= 'Source packaging success : ' . PHP_EOL . $packingReport . PHP_EOL;
				$report->success = true;
			} catch (Exception $e) {
				$report->success = false;
				$report->report .= 'Source packaging failure : ' . PHP_EOL;
				$report->report .= '----------- ' . $e->getMessage() . ' ----------' . PHP_EOL . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
			}
		}
		return $report;
	}

	/**
	 * @param stdClass $serviceClassInstance
	 * @param string $targetPackage
	 * @param string $targetSrcDir
	 * @param string $configId Flex config entry to use
	 * @return Foomo\Services\ProxyGenerator\ActionScript\Report
	 */
	public static function compileSrc($serviceClassInstance, $targetPackage, $targetSrcDir, $configId)
	{
		$report = self::generateSrc($serviceClassInstance, $targetPackage, $targetSrcDir);
		if ($report->success) {
			try {
				$compilationReport = $report->generator->compile($configId);
				$report->report = 'COMPILATION SUCCESS : ' . PHP_EOL . PHP_EOL . $compilationReport . PHP_EOL;
				$report->success = true;
			} catch (Exception $e) {
				$report->success = false;
				$report->report .= 'COMPILATION FAILURE :' . PHP_EOL;
				$report->report .= '----------- ' . $e->getMessage() . ' ----------' . PHP_EOL . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
			}
		}
		return $report;
	}

	/**
	 * @param stdClass $serviceClassInstance
	 * @param string $targetPackage
	 * @param string $targetSrcDir
	 *
	 * @return Foomo\Services\ProxyGenerator\ActionScript\RPC
	 */
	public static function getGenerator($serviceClassInstance, $targetPackage, $targetSrcDir)
	{
		$renderer = new self();
		$renderer->targetPackage = $targetPackage;
		$renderer->targetSrcDir = $targetSrcDir;
		return $renderer;
	}
}