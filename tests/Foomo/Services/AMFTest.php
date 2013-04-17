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

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class AMFTest extends \PHPUnit_Framework_TestCase
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var AMFSerializer
	 */
	public $serializer;

	//---------------------------------------------------------------------------------------------
	// ~ Initialization
	//---------------------------------------------------------------------------------------------

	public function setUp()
	{
		if(!function_exists('amf_encode') && !class_exists('Zend_Amf_Parse_InputStream')) {
			$this->markTestSkipped('nothing to amf encode');
		} else {
			$this->serializer = new RPC\Serializer\AMF;
		}
   	}

	//---------------------------------------------------------------------------------------------
	// ~ Test methods
	//---------------------------------------------------------------------------------------------

	public function testObject()
	{
		$obj = (object) array('a' => 1);
		$this->assertEquals($obj, $this->serializer->unserialize($this->serializer->serialize($obj)));
   	}
}
