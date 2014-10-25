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

namespace Foomo\Services\ProxyGenerator\PHP;

/**
 * renders php rpc clients
 *
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class RPC extends \Foomo\Services\Renderer\AbstractRenderer
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * which class is responsible for serializing
	 *
	 * @var string
	 */
	public $serializerClassName;
	/**
	 * name of the service
	 *
	 * @var string
	 */
	public $serviceName;
	/**
	 * docs on class level
	 *
	 * @var string
	 */
	public $serviceDocs;
	/**
	 * all the ops to be exposed
	 *
	 * @var \Foomo\Services\Reflection\ServiceOperation[]
	 */
	public $operations;
	/**
	 * default endpoint of the service
	 *
	 * @var string
	 */
	public $endPoint;
	/**
	 * all the vo classes that should be imported
	 *
	 * @var \Foomo\Services\Reflection\ServiceObjectType[]
	 */
	public $classesToImport = array();

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * get it up
	 *
	 * @param string $endPoint uri of the service endpoint
	 * @param string $serializerClassName name of the serializer used in this service
	 */
	public function __construct($endPoint, $serializerClassName)
	{
		$this->endPoint = $endPoint;
		$this->serializerClassName = $serializerClassName;
		foreach(
			array(
				'Foomo\\Services\\RPC\\Client',
				'Foomo\\Services\\RPC\\Protocol\\Call',
				'Foomo\\Services\\RPC\\Protocol\\Call\\Head',
				'Foomo\\Services\\RPC\\Protocol\\AbstractHead',
				'Foomo\\Services\\RPC\\Protocol\\Call\\MethodCall',
				'Foomo\\Services\\RPC\\Protocol\\Reply\\MethodReply',
				'Foomo\\Services\\RPC\\Protocol\\Reply\\Exception',
				'Foomo\\Services\\RPC\\Protocol\\Reply',
				'Foomo\\Services\\RPC\\Protocol\\Reply\\Head',
				'Foomo\\Services\\RPC\\Serializer\\PHP'
			) as $clientClass) {
			$this->classesToImport[] = $clientClass;
			$this->classesToImport = array_unique(array_merge($this->getRelatedClasses($clientClass), $this->classesToImport));
		}

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
		$this->operations = array();
		$this->serviceName = $serviceName;
	}

	/**
	 * render the service type itself
	 *
	 * @param \Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderServiceType(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
	}

	/**
	 * render an operateion / method of the services class
	 *
	 * @param \Foomo\Services\Reflection\ServiceOperation $op
	 */
	public function renderOperation(\Foomo\Services\Reflection\ServiceOperation $op)
	{
		$this->operations[] = $op;
	}

	/**
	 * render a Type
	 *
	 * @param \Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderType(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		if(class_exists($type->type)) {
			$this->classesToImport[] = $type->type;
			$this->classesToImport = array_merge($this->classesToImport, $this->getRelatedClasses($type->type));
		}
	}

	/**
	 * get a lisr of classes to import
	 *
	 * @return string[]
	 */
	public function getSortedClassesToImport()
	{
		$stack = $this->classesToImport;
		$found = array();
		$i = 0;
		while(count($stack)>0 && $i<100) {
			$i++;
			foreach($stack as $key => $className) {
				//var_dump($found);
				$ref = new \ReflectionClass($className);
				$parentClass = $ref->getParentClass();
				if($parentClass === false || (is_object($parentClass) && in_array($parentClass->name, $found))) {
					// echo 'good to add ' . $className . ' ' . PHP_EOL;
					$found[] = $className;
					//var_dump($stack);
					unset($stack[$key]);
					//var_dump($stack);
					continue;
				} else {
					// echo 'can not add ' . $className . ' because >' . $parentClass->name . '< ' . gettype($parentClass).  ' is not in ' . implode('-', $found) . '-' . PHP_EOL;
				}
			}
		}
		// var_dump('left', $stack);
		// var_dump('found', $found);exit;
		return $found;
	}

	/**
	 * return the thing you rendered
	 *
	 * @return mixed
	 */
	public function output()
	{
		return \Foomo\Services\Module::getView($this, 'PHPClient', $this)->render();
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $className
	 * @return array
	 */
	private function getRelatedClasses($className)
	{
		$ref = new \ReflectionClass($className);
		$ret = $ref->getInterfaceNames();
		while($ref->getParentClass() instanceof \ReflectionClass) {
			$ret[] = $ref->getParentClass()->getName();
			$ref = $ref->getParentClass();
		}
		return $ret;
	}
}
