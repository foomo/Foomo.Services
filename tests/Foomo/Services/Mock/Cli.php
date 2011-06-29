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

namespace Foomo\Services\Mock;

/**
 * a mock class to test arg parsing
 */
class Cli
{
	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * something to reflect upon
	 *
	 * @param string $string a string
	 * @param integer $integer an int
	 * @param floar $float a float
	 * @param string[] $array an array
	 * @param array $hash a hash
	 * @param stdClass $object an object
	 */
	public function cliArgs($string, $integer, $float, array $array, array $hash, stdClass $object)
	{

	}
}
