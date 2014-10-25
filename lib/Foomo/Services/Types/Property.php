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

namespace Foomo\Services\Types;

/**
 * type to describe generic properties
 *
 * @Foomo\Services\Reflection\RemoteClass(package='org.foomo.services.sharedVo')
 *
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  jan <jan@bestbytes.de>
 */
class Property
{
	// --------------------------------------------------------------------------------------------
	// ~ Variables
	// --------------------------------------------------------------------------------------------

	/**
	 * name of the property
	 *
	 * @var string
	 */
	public $name;
	/**
	 * value of the item
	 *
	 * @var mixed
	 */
	public $value;

	// --------------------------------------------------------------------------------------------
	// ~ Public static methods
	// --------------------------------------------------------------------------------------------

	/**
	 * helper function to translate a hash
	 *
	 * @param array $sourceHash
	 * @return Property[]
	 */
	public static function castHashToServicePropertyArray($sourceHash)
	{
		$ret = array();
		foreach ($sourceHash as $key => $value) {
			$prop = new self();
			$prop->name = $key;
			$prop->value = $value;
			$ret[] = $prop;
		}
		return $ret;
	}

	/**
	 * transform back to a hash
	 *
	 * @param Property[] $servicePropertyArray
	 * @return array a regular hash
	 */
	public static function castToHash($servicePropertyArray)
	{
		$ret = array();
		foreach ($servicePropertyArray as $serviceProp) {
			$ret[$serviceProp->name] = $serviceProp->value;
		}
		return $ret;
	}
}