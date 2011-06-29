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
 * expose a command line "service"
 *
 * @author jan
 */
class Cli
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * the object to server
	 *
	 * @var stdClass
	 */
	private $serviceClassInstance;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	private function __construct($serviceClassName)
	{
		$this->serviceClassInstance = new $serviceClassName;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 * @param string $className
	 */
	public static function serveClass($className)
	{
		$server = new self($className);
		if(!isset($_SERVER['argv'])) {
			trigger_error('i am a command line tool', E_USER_ERROR);
		}
		if(count($_SERVER['argv']) < 2 || in_array($_SERVER['argv'][1], array('help', '--help', '-help'))) {
			$server->renderUsage();
			exit;
		}
		$method = $_SERVER['argv'][1];
		$args = self::parseArgs($className, $method, array_slice($_SERVER['argv'], 2));
		//echo 'executing command ' . $method . PHP_EOL;
		//echo 'parsed arguments : ' . PHP_EOL;
		$ret = call_user_func_array(array($server->serviceClassInstance, $method), $args);
		if(!is_scalar($ret)) {
			echo json_encode($ret) . PHP_EOL;
		} else {
			echo $ret;
		}
	}

	/**
	 * @internal
	 */
	public static function parseArgs($className, $method, $rawArgs)
	{
		$classRefl = new \ReflectionClass($className);
		$args = array();
		foreach($classRefl->getMethods() as $methodRefl) {
			/* @var $methodRefl ReflectionMethod */
			if(strtolower($methodRefl->getName()) == strtolower($method)) {
				$doc = new \Foomo\Reflection\PhpDocEntry($methodRefl->getDocComment());
				$i = 0;
				foreach($doc->parameters as $parameter) {
					$type = new \Foomo\Services\Reflection\ServiceObjectType($parameter->type);
					if(isset($rawArgs[$i])) {
						$rawValue = $rawArgs[$i];
					} else {
						$rawValue = null;
					}
					if(!$type->isArrayOf && in_array($type->type, array('string', 'integer', 'int', 'uint', 'long', 'double', 'float', 'boolean' ))) {
						switch($type->type) {
							case 'boolean':
								$args[] = (boolean) $rawValue;
								break;
							case 'float':
								$args[] = (float) $rawValue;
								break;
							case 'double':
								$args[] = (double) $rawValue;
								break;
							case 'integer':
							case 'int':
								$args[] = (integer) $rawValue;
								break;
							default:
								$args[] = $rawValue;
						}
					} else {
						if($type->isArrayOf || $type->type == 'array') {
							$args[] = (array) json_decode($rawValue);
						} else {
							$args[] = json_decode($rawValue);
						}
					}
					$i ++;
				}
			}
		}
		return $args;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 */
	private function renderUsage()
	{
		echo 'Usage : ' . $_SERVER['argv'][0] . ' operationName argument1 argument2 arrgument...' . PHP_EOL . PHP_EOL;
		echo ' this program is a cli wrapped model:' . PHP_EOL . PHP_EOL;
		echo \Foomo\Services\Renderer\PlainDocs::render(get_class($this->serviceClassInstance));
	}
}