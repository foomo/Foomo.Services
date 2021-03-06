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

use Exception;
use Foomo\Config;
use Foomo\Reflection\PhpDocEntry;
use Foomo\Reflection\PhpDocProperty;
use ReflectionAnnotatedClass;
use Foomo\AutoLoader;

/**
 * This class is used to reflect Types that will be exposed for usage in services
 *
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
class ServiceObjectType
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	/**
	 * the phpDoc comments are not ok
	 */
	const ERROR_DOCS_SUCK = 1;

	//---------------------------------------------------------------------------------------------
	// ~ Static variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @internal
	 * @var bool
	 */
	static $seriouslyCacheInDevAndTestMode = false;

	/**
	 * type => ServiceObjectType
	 *
	 * @var ServiceObjectType[]
	 */
	private static $cache = array();

	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * possibly many Annotations
	 *
	 * @var Annotation[]
	 */
	public $annotations = array();
	/**
	 * php => as
	 *
	 * @var string[]
	 */
	public $standarTypes = array(
		'int' => 'int',
		'integer' => 'int',
		'bool' => 'Boolean',
		'boolean' => 'Boolean',
		'string' => 'String',
		'float' => 'Number',
		'double' => 'Number',
		'mixed' => 'Object',
	);
	/**
	 * namespace of the class
	 *
	 * @var string
	 */
	public $namespace = '\\';
	/**
	 * type of the object
	 *
	 * @var string
	 */
	public $type;
	/**
	 * array of class properties
	 *
	 * @var \Foomo\Services\Reflection\ServiceObjectType[]
	 */
	public $props = array();
	/**
	 * array of class constants unfortunately just a hash name => value - no comments are available
	 *
	 * @var array
	 */
	public $constants = array();
	/**
	 * the reflected
	 *
	 * @var boolean
	 */
	public $isArrayOf = false;
	/**
	 * is it a subclass of Exception
	 *
	 * @var bool
	 */
	public $isException = false;
	/**
	 * @var \Foomo\Reflection\PhpDocEntry
	 */
	public $phpDocEntry;
	/**
	 * if it is a non complex type it will be in here like integer | string | boolean
	 *
	 * @var string
	 */
	public $plainType = '';

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $type
	 */
	public function __construct($type)
	{
		$this->plainType = $type;
		if (substr($type, strlen($type)-2) == '[]') {
			$type = substr($type,0,strlen($type)-2);
			$this->isArrayOf = true;
		}
		$this->type = $type;
		if (strpos($this->type, '\\') !== false) {
			$parts = explode('\\', trim($this->type, '\\'));
			$this->namespace = implode('\\', array_slice($parts, 0, count($parts)-1) );
		}
		if (\class_exists($type) || interface_exists($type)) {
			$this->readClass($this->type);
		} else {
			$this->type = $type;
		}
	}
	/**
	 * load reflection from here if you need it very often in one call
	 *
	 * @param string $type
	 *
	 * @return self
	 */
	public static function getCachedType($type)
	{
		static $cache = array();
		if(!isset($cache[$type])) {
			if(Config::isProductionMode() || self::$seriouslyCacheInDevAndTestMode) {
				$cache[$type] = \Foomo\Cache\Proxy::call(__CLASS__, 'getSeriouslyCachedType', array($type));
			} else {
				$cache[$type] = self::getSeriouslyCachedType($type);
			}
		}
		return $cache[$type];
	}

	/**
	 * @param string $type
	 *
	 * @return ServiceObjectType
	 *
	 * @Foomo\Cache\CacheResourceDescription("dependencies"="Foomo\AutoLoader::cachedGetClassMap")
	 */
	public static function getSeriouslyCachedType($type)
	{
		return new self($type);
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * get the remote class name if annotation set
	 *
	 * @return string
	 */
	public function getRemoteClass()
	{
		foreach($this->annotations as $annotation) {
			if($annotation instanceof RemoteClass && !empty($annotation->name)) {
				return $annotation->name;
			}
		}
	}

	/**
	 * get the remote class name if annotation set
	 *
	 * @return string
	 */
	public function getRemotePackage()
	{
		foreach($this->annotations as $annotation) {
			if ($annotation instanceof RemoteClass && !empty($annotation->package)) {
				return $annotation->package;
			}
		}
		// php namespaces
		if (!\array_key_exists($this->type, $this->standarTypes)) {
			$parts = ($this->namespace != '\\') ? explode('\\', $this->namespace) : array();
			// @todo: think of better way to define default package
			$package = array('org', 'foomo', 'zugspitze', 'services', 'namespaces', 'php');
			foreach($parts as $part) {
				$package[] = lcfirst($part);
			}
			return implode('.', $package);
		}
	}

	/**
	 * @return string
	 */
	public function getRemotePackagePath()
	{
		return str_replace('.', DIRECTORY_SEPARATOR, $this->getRemotePackage());
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $propName
	 * @param string $propType
	 * @param PhpDocEntry $phpDocEntry
	 *
	 * @throws \Exception
	 */
	private function setProp($propName, $propType = 'unknown', PhpDocEntry $phpDocEntry)
	{
		static $waitingList = array();
		if(!isset($waitingList[$propType])) {
			$waitingList[$propType] = array();
			$type = new ServiceObjectType($propType);
			$type->phpDocEntry = $phpDocEntry;
			$this->props[$propName] = $type;
			foreach($waitingList[$propType] as $waitingListItem) {
				$clone = clone $type;
				$clone->phpDocEntry = $waitingListItem['phpDocEntry'];
				$waitingListItem['object']->props[$waitingListItem['propName']] = $clone;
			}
			unset($waitingList[$propType]);
		} else {
			$waitingList[$propType][] = array(
				'object' => $this,
				'propName' => $propName,
				'phpDocEntry' => $phpDocEntry
			);
		}
	}
	/**
	 * @return bool
	 */
	public function isComplex()
	{
		return class_exists(substr($this->plainType, 0, $this->isArrayOf ? -2: strlen($this->plainType)));
	}
	/**
	 * @param string $className
	 */
	private function readClass($className)
	{
		try {
			$ref = new ReflectionAnnotatedClass($className);
			$this->isException = $ref->isSubclassOf('Exception');
			$this->annotations = $ref->getAnnotations();

			$classComment = $ref->getDocComment();
			if(is_null($classComment)) {
				$classComment = '';
			}
			$this->phpDocEntry = new PhpDocEntry($classComment, $ref->getNamespaceName());
			//

			foreach ($this->phpDocEntry->properties as $propDoc) {
				/* @var $propDoc PhpDocProperty */
				if($propDoc->read) {
					$this->setProp($propDoc->name, $propDoc->type);
				}
			}
			$this->constants = $ref->getConstants();

			$props = $ref->getProperties();
			foreach($props as $prop) {
				/* @var $prop \ReflectionProperty */
				if($prop->isPublic() && !$prop->isStatic()) {
					$phpDoc = new PhpDocEntry($prop->getDocComment(), $prop->getDeclaringClass()->getNamespaceName());
					if($phpDoc->wsdlGen == 'ignore' || $phpDoc->serviceGen == 'ignore') {
						continue;
					}
					if(!$phpDoc->var->type) {
						//throw new Exception('check your phpDocs in ' . $className . ' I do not understand it - sorry', self::ERROR_DOCS_SUCK );
						trigger_error('check your phpDocs in ' . $className . ' for property "' . $prop->getName() . '" I do not understand it - sorry', E_USER_WARNING );
						$phpDoc->var->type = 'stdClass';
					}
					$newPropName = str_replace('_doctProp_', '',$prop->getName());
					// careful dude, that is a hidden recursion !
					$this->setProp($newPropName, $phpDoc->var->type, $phpDoc);
				}
			}
		} catch(Exception $e) {
			trigger_error("could not parse annotations for $className: '{$e->getMessage()}'", E_USER_WARNING);
		}
	}
}
