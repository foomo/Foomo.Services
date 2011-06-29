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

class CLITest extends \PHPUnit_Framework_TestCase
{
	//---------------------------------------------------------------------------------------------
	// ~ Test methods
	//---------------------------------------------------------------------------------------------

	public function testParseArgs()
	{
		$rawArgs = array('stringVal', '111', '1.234', '[1,2,3,"hans", "peter"]', '{"a":1,"b":"lalala"}', '{"prop":"value"}');
		$args = \Foomo\Services\Cli::parseArgs('Foomo\\Services\\Mock\\Cli', 'cliArgs', $rawArgs);
		$this->assertInternalType('string', $args[0]);
		$this->assertInternalType('int', $args[1]);
		$this->assertInternalType('float', $args[2]);
		$this->assertInternalType('array', $args[3]);
		$this->assertInternalType('array', $args[4]);
		$this->assertInstanceOf('stdClass', $args[5]);
	}
}
