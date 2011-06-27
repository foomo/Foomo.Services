<?php

namespace Foomo\Services\ProxyGenerator;

class ActionScript
{
	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * compile and report
	 *
	 * @param string $service service class name
	 * @param Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator $generator
	 * @return Foomo\Services\ProxyGenerator\ActionScript\Report
	 */
	public static function generateSrc($service, \Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator $generator)
	{
		$report = new \Foomo\Services\ProxyGenerator\ActionScript\Report();
		$report->generator = $generator;
		$report->swcFilename = $generator->getSWCFilename();
		$report->tgzFilename = $generator->getTGZFilename();
		try {
			$report->report = $generator->render($service, $generator);
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
	 * @param string $service service class name
	 * @param Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator $generator
	 * @return Foomo\Services\ProxyGenerator\ActionScript\Report
	 */
	public static function packSrc($service, $generator)
	{
		$report = self::generateSrc($service, $generator);
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
	 * @param string $service service class name
	 * @param Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator $generator
	 * @param string $configId Flex config entry to use
	 * @return Foomo\Services\ProxyGenerator\ActionScript\Report
	 */
	public static function compileSrc($service, $generator, $configId)
	{
		$report = self::generateSrc($service, $generator);
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
}