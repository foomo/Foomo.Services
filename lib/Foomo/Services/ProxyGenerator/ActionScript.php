<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Services\ProxyGenerator;

use Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  jan <jan@bestbytes.de>
 */
class ActionScript
{
	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * compile and report
	 *
	 * @param string            $service service class name
	 * @param AbstractGenerator $generator
	 * @return \Foomo\Services\ProxyGenerator\ActionScript\Report
	 */
	public static function generateSrc($service, AbstractGenerator $generator)
	{
		$report = new \Foomo\Services\ProxyGenerator\ActionScript\Report();
		$report->generator = $generator;
		$report->swcFilename = $generator->getSWCFilename();
		$report->tgzFilename = $generator->getTGZFilename();
		try {
			$report->report = $generator->render($service, $generator);
			$report->report = 'Source generation success : ' . PHP_EOL . PHP_EOL . $report->report . PHP_EOL;
			$report->success = true;
		} catch (\Exception $e) {
			$report->success = false;
			$report->report .= 'Source generation failed:' . PHP_EOL;
			$report->report .= '----------- ' . $e->getMessage() . ' ----------' . PHP_EOL . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
		}
		return $report;
	}

	/**
	 * @param string            $service service class name
	 * @param AbstractGenerator $generator
	 * @return \Foomo\Services\ProxyGenerator\ActionScript\Report
	 */
	public static function packSrc($service, AbstractGenerator $generator)
	{
		$report = self::generateSrc($service, $generator);
		if ($report->success) {
			try {
				$packingReport = $report->generator->packTgz();
				$report->report .= 'Source packaging success : ' . PHP_EOL . $packingReport . PHP_EOL;
				$report->success = true;
			} catch (\Exception $e) {
				$report->success = false;
				$report->report .= 'Source packaging failure : ' . PHP_EOL;
				$report->report .= '----------- ' . $e->getMessage() . ' ----------' . PHP_EOL . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
			}
		}
		return $report;
	}

	/**
	 * @param string            $service  service class name
	 * @param AbstractGenerator $generator
	 * @param string            $configId Flex config entry to use
	 * @return \Foomo\Services\ProxyGenerator\ActionScript\Report
	 */
	public static function compileSrc($service, AbstractGenerator $generator, $configId)
	{
		$report = self::generateSrc($service, $generator);
		if ($report->success) {
			try {
				$compilationReport = $report->generator->compile($configId);
				$report->report = 'COMPILATION SUCCESS : ' . PHP_EOL . PHP_EOL . $compilationReport . PHP_EOL;
				$report->success = true;
			} catch (\Exception $e) {
				$report->success = false;
				$report->report .= 'COMPILATION FAILURE :' . PHP_EOL;
				$report->report .= '----------- ' . $e->getMessage() . ' ----------' . PHP_EOL . PHP_EOL . $e->getTraceAsString() . PHP_EOL;
			}
		}
		return $report;
	}
}