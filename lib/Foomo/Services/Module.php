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

namespace Foomo\Services;

use Foomo\Frontend\ToolboxConfig\MenuEntry;
use Foomo\Frontend\ToolboxInterface;
use Foomo\Modules\MakeResult;
use Foomo\Modules\ModuleBase;
use Foomo\Modules\Resource;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  jan <jan@bestbytes.de>
 */
class Module extends ModuleBase implements ToolboxInterface
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	const VERSION = '0.4.2';
	const NAME    = 'Foomo.Services';

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
			Resource\Module::getResource('Foomo', '0.3.*')
		);
	}

	//---------------------------------------------------------------------------------------------
	// ~ Toolbox interface methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @internal
	 * @return MenuEntry[]
	 */
	public static function getMenu()
	{
		return array(
			MenuEntry::create('Root.Modules.Services', 'Services', self::NAME, 'Foomo.Services')
		);
	}

	/**
	 * @param string     $target
	 * @param MakeResult $result
	 */
	public static function make($target, MakeResult $result)
	{
		switch ($target) {
			case 'all':
				foreach (Utils::buildAllLocalServices() as $line) {
					$result->addEntry($line);
				}
				break;
			case 'clean':
				foreach (Utils::cleanAllLocalServices() as $line) {
					$result->addEntry($line);
				}
				break;
			default:
				$result->addEntry('nothing to make here for target ' . $target);
		}
	}
}
