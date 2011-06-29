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

use Foomo\Services\RPC;

/**
 * awesome mock service
 */
class Service
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	/**
	 * you have to give it a VERSION number
	 */
	const VERSION = 1.0;

	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	private $i;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	public function __construct()
	{
		$this->i = 0;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * add two numbers
	 *
	 * @param integer $numberOne
	 * @param integer $numberTwo
	 *
	 * @return integer the sum of the two numbers
	 */
	public function addNumbers($numberOne, $numberTwo)
	{
		// WTF where is my notice
		//\Foomo\Http\BrowserCache::tryBrowserCache()
		//\Foomo\Http\BrowserCache::setResourceData('application/json', 'tag-' . $numberOne .'-' . $numberTwo, time(), 3600);
		//\Foomo\Http\BrowserCache::sendHeaders();
		//trigger_error('FUPPED', E_USER_ERROR);
		return $numberOne + $numberTwo;
	}

	/**
	 * generate an exception
	 *
	 * @return boolean it will pop an exception anyways
	 * @throws Foomo\Services\Mock\Exception
	 */
	public function makeException()
	{
		RPC::addMessage('there we go');
		throw new Exception('expected exception', 0);
	}

	/**
	 * test a funky star
	 * also does not really do
	 * much stuff
	 *
	 * @serviceMessage Foomo\Services\Mock\Message a message
	 * @serviceMessage string another message
	 * @serviceStatus string a simple status update message
	 *
	 * @param Foomo\Services\Mock\FunkyStar $star
	 *
	 * @return Foomo\Services\Mock\FunkyStar[]
	 */
	public function getSomeFunkyStars($star)
	{
		$msg = new Message;
		$msg->id = 'id-123';
		RPC::addMessage($msg);
		RPC::addMessage('yeeha');
		$ret = array();
		$starSeeds = array('aaa here i am', 'b', 'ccccccc');
		foreach($starSeeds as $starSeed) {
			$retStar = new FunkyStar();
			$retStar->test = $starSeed;
			$ret[] = $retStar;
			RPC::addStatusUpdate('added another star ' . count($ret) . ' / ' . count($starSeeds) );
		}
		return $ret;
	}

	/**
	 * test that damn array
	 * 
	 * @return Foomo\Services\Mock\DamnArray
	 */
	public function getThatDamnArrayArray()
	{
		return new \Foomo\Services\Mock\DamnArray();
	}
	/**
	 * get a funky star
	 * 
	 * @return Foomo\Services\Mock\FunkyStar
	 */
	public function getAFunkyStar()
	{
		return new FunkyStar();
	}
	/**
	 * session test
	 *
	 * @return integer
	 */
	public function iPlusPlus()
	{
		return $this->i++;
	}

	/**
	 * a method, that will not be exposed
	 *
	 * @serviceGen ignore
	 *
	 * @return null
	 */
	public function noExpose() {}

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * this method will not be exposed, because it is static
	 *
	 * @return null
	 */
	public static function fooStatic() {}
}