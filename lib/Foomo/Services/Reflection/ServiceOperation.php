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

namespace Foomo\Services\Reflection;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  jan <jan@bestbytes.de>
 */
class ServiceOperation
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	const ERROR_PARAMETER_USED = 1;

	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 * @var string
	 */
	public $name;
	/**
	 * hash name => type
	 *
	 * @var array
	 */
	public $parameters = array();
	/**
	 * docs for every parameter
	 *
	 * @var \Foomo\Reflection\PhpDocArg[]
	 */
	public $parameterDocs = array();
	/**
	 * what will be returned from the operation
	 *
	 * @var \Foomo\Reflection\PhpDocArg
	 */
	public $returnType;
	/**
	 * what exceptions will be thrown
	 *
	 * @var \Foomo\Reflection\PhpDocArg[]
	 */
	public $throwsTypes;
	/**
	 * messages that can be send from this method
	 *
	 * @var \Foomo\Reflection\PhpDocArg[]
	 */
	public $messageTypes;
	/**
	 * comment comment
	 *
	 * @var string
	 */
	public $comment;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string     $name
	 * @param \Foomo\Reflection\PhpDocArg $returnType
	 * @param \Foomo\Reflection\PhpDocArg[] $throwsTypes
	 * @param \Foomo\Reflection\PhpDocArg[] $messageTypes
	 * @param string $comment
	 */
	public function __construct($name, $returnType = null, $throwsTypes = null, $messageTypes = null, $comment = null)
	{
		$this->name = $name;
		$this->comment = $comment;
		$this->messageTypes = $messageTypes;
		$this->returnType = $returnType;
		$this->throwsTypes = $throwsTypes;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * add a parameter
	 *
	 * @param string $name name of the parameter
	 * @param string $type type of the parameter
	 * @throws \Exception
	 */
	public function addParameter($name, $type)
	{
		if (!isset($this->parameters[$name])) {
			$this->parameters[$name] = $type;
		} else {
			throw new \Exception('parameter "' . $name . '":' . $type . ' was already used', self::ERROR_PARAMETER_USED);
		}
	}

	/**
	 * add parameter docs
	 *
	 * @param string                      $name name of the parameter
	 * @param \Foomo\Reflection\PhpDocArg $type info on the parameter
	 * @throws \Exception
	 */
	public function addParameterDocs($name, \Foomo\Reflection\PhpDocArg $type)
	{
		if (!isset($this->parameterDocs[$name])) {
			$this->parameterDocs[$name] = $type;
		} else {
			throw new \Exception('parameter ' . $name . 'was already used', self::ERROR_PARAMETER_USED);
		}
	}
}
