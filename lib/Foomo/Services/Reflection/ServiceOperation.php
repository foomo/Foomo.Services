<?php

namespace Foomo\Services\Reflection;

use Foomo\Reflection\PhpDocArg;
use Exception;

/**
 *
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
	 * @var Foomo\Reflection\PhpDocArg[]
	 */
	public $parameterDocs = array();
	/**
	 * what will be returned from the operation
	 *
	 * @var Foomo\Reflection\PhpDocArg
	 */
	public $returnType;
	/**
	 * what exceptions will be thrown
	 *
	 * @var Foomo\Reflection\PhpDocArg[]
	 */
	public $throwsTypes;
	/**
	 * messages that can be send from this method
	 *
	 * @var Foomo\Reflection\PhpDocArg[]
	 */
	public $messageTypes;
	/**
	 * comment comment
	 *
	 * @var sring
	 */
	public $comment;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

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
	 */
	public function addParameter($name, $type)
	{
		if(!isset($this->parameters[$name])) {
			$this->parameters[$name] = $type;
		} else {
			throw new Exception('parameter "'.$name.'":'.$type.' was already used', self::ERROR_PARAMETER_USED);
		}
	}
	
	/**
	 * add parameter docs
	 *
	 * @param string $name name of the parameter
	 * @param Foomo\Reflection\PhpDocArg $type info on the parameter
	 */
	public function addParameterDocs($name, PhpDocArg $type)
	{
		if(!isset($this->parameterDocs[$name])) {
			$this->parameterDocs[$name] = $type;
		} else {
			throw new Exception('parameter '.$name.'was already used', self::ERROR_PARAMETER_USED);
		}
	}
}
