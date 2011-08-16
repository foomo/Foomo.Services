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
class ReflectionTest extends \PHPUnit_Framework_TestCase
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * my reader
	 *
	 * @var ServiceReader
	 */
	private $reader;

	//---------------------------------------------------------------------------------------------
	// ~ Initialization
	//---------------------------------------------------------------------------------------------

	public function setUp()
	{
		$this->reader = new Reflection('Foomo\Services\Mock\Service');
	}

	//---------------------------------------------------------------------------------------------
	// ~ Test methods
	//---------------------------------------------------------------------------------------------

	public function testOperationsNoStatic()
	{
		foreach($this->reader->getOperations() as $op) {
			/* @var $op ServiceOperation */
			if($op->name == 'fooStatic') {
				$this->fail('fooStatic should not have been exposed, because it is static');
			}
		}
	}

	public function testServigenIgnore()
	{
		foreach($this->reader->getOperations() as $op) {
			/* @var $op ServiceOperation */
			if($op->name == 'noExpose') {
				$this->fail('noExpose was annotated with @serviceGen ignore and should not have been in the operations');
			}
		}
	}

	public function testTypes()
	{
		$expectedTypes = array(
			'integer',
			'boolean',
			'Foomo\Services\Mock\Exception',
			'Foomo\Services\Mock\FunkyStar',
			'Foomo\Services\Mock\Message'
		);
		$types = array();
		foreach($this->reader->getTypes() as $type) {
			/* @var $type ServiceObjectType */
			$types[] = $type->type;
		}
		$types = array_unique($types);
		foreach($expectedTypes as $expectedType) {
			if(!in_array($expectedType, $types)) {
				$this->fail('missing type ' . $expectedType);
			}
		}
	}

	public function testMessages()
	{
		$expectedMessageTypes = array('string', 'Foomo\Services\Mock\Message');
		$messageTypes = array();
		foreach($this->reader->getOperations() as $op) {
			/* @var $op ServiceOperation */
			if($op->name == 'getSomeFunkyStars') {
				foreach ($op->messageTypes as $type) {
					$messageTypes[] = $type->type;
				}
				break;
			}
		}
		sort($messageTypes);
		sort($expectedMessageTypes);
		$this->assertEquals($expectedMessageTypes, $messageTypes, 'expected and returned message types did not match');
	}

	public function testExceptions()
	{
		$expectedExceptionTypes = array('Foomo\Services\Mock\Exception');
		$exceptionTypes = array();
		foreach($this->reader->getOperations() as $op) {
			/* @var $op ServiceOperation */
			if($op->name == 'makeException') {
				foreach ($op->throwsTypes as $type) {
					$exceptionTypes[] = $type->type;
				}
				break;
			}
		}
		sort($exceptionTypes);
		sort($expectedExceptionTypes);
		$this->assertEquals($expectedExceptionTypes, $exceptionTypes, 'expected and returned exception types did not match');
	}

	public function testOperations()
	{
		$expectedOps = array('iPlusPlus', 'getAFunkyStar', 'getSomeFunkyStars', 'getThatDamnArrayArray', 'makeException', 'addNumbers');
		$ops = array();
		foreach($this->reader->getOperations() as $op) {
			/* @var $op ServiceOperation */
			$ops[] = $op->name;
		}
		sort($expectedOps);
		sort($ops);
		$this->assertEquals($expectedOps, $ops);
	}
}