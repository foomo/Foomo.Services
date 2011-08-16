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

namespace Foomo\Services\ProxyGenerator\JS;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class JQuery extends \Foomo\Services\Renderer\AbstractRenderer
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var Foomo\Services\Reflection\ServiceObjectType
	 */
	public $serviceType;
	/**
	 * @var Foomo\Services\Reflection\ServiceObjectType[]
	 */
	public $types = array();
	/**
	 * @var Foomo\Services\Reflection\ServiceOperation[]
	 */
	public $operations = array();
	/**
	 * @var string
	 */
	public $endPoint = '';
	/**
	 * @var string
	 */
	public $package = null;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $endPoint
	 * @param string $package
	 */
	public function __construct($endPoint, $package=null)
	{
		$this->endPoint = $endPoint;
		$this->package = $package;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * prepare your assets
	 *
	 * @param string $serviceName name of the service class
	 */
	public function init($serviceName)
	{
		$this->name = $serviceName;
	}

	/**
	 * render the service type itself
	 *
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderServiceType(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		$this->serviceType = $type;
	}

	/**
	 * render an operation / method of the services class
	 *
	 * @param Foomo\Services\Reflection\ServiceOperation $op
	 */
	public function renderOperation(\Foomo\Services\Reflection\ServiceOperation $op)
	{
		$this->operations[] = $op;
	}

	/**
	 * render a Type
	 *
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderType(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		if(class_exists($type->type)) {
			$this->types[$type->type] = $type;
		}
	}

	/**
	 * return the thing you rendered
	 *
	 * @return mixed
	 */
	public function output()
	{
		return \Foomo\Services\Module::getView($this, 'jQuery', $this)->render();
	}

	/**
	 * @param Foomo\Services\Reflection\ServiceOperation $op
	 * @return array
	 */
	public function getArgNames(\Foomo\Services\Reflection\ServiceOperation $op)
	{
		$ret = array();
		foreach($op->parameters as $name => $type) $ret[] = $name;
		return $ret;
	}

	/**
	 * @param \Foomo\Services\Reflection\ServiceOperation $op
	 * @return boolean
	 */
	public function opHasComplexArgs(\Foomo\Services\Reflection\ServiceOperation $op)
	{
		foreach($op->parameters as $name => $type) {
			switch($type) {
				case 'string':
				case 'int':
				case 'integer':
				case 'float':
				case 'bool':
				case 'boolean':
				case 'double':
					continue;
					break;
				default:
					return true;
			}
		}
		return false;
	}

	/**
	 * @return string
	 */
	public function getProxyName()
	{
		if(is_null($this->package)) {
			return lcfirst(str_replace('\\', '', $this->serviceType->type)).'Proxy';
		} else {
			return $this->package;
		}
	}

	/**
	 * @param \Foomo\Services\Reflection\ServiceObjectType $type
	 * @return string
	 */
	public function getJsTypeName(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		if(class_exists($type->type)) {
			if(strpos($this->serviceType->namespace, $type->namespace) === 0) {
				$name = substr($type->type, strlen($this->serviceType->namespace));
			} else {
				$name = $type->type;
			}
			return lcfirst(str_replace('\\', '', $name));
		} else {
			return $type->type;
		}
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $className
	 * @param string $endPoint
	 * @param string $package
	 * @return string
	 */
	public static function renderJS($className, $endPoint, $package)
	{
		$renderer = new self($endPoint, $package);
		return parent::render($className, $renderer);
	}
}