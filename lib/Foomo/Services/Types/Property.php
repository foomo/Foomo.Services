<?php

namespace Foomo\Services\Types;

/**
 * type to describe generic properties
 *
 * @Foomo\Services\Reflection\RemoteClass(package='org.foomo.services.sharedVo')
 */
class Property {
	/**
	 * name of the property
	 *
	 * @var string
	 */
	public $name;
	/**
	 * value of the item
	 *
	 * @var mixed
	 */
	public $value;
	/**
	 * helper function to translate a hash
	 *
	 * @param array $sourceHash
	 *
	 * @return Foomo\Services\Types\Property[]
	 */
	public static function castHashToServicePropertyArray($sourceHash)
	{
		$ret = array();
		foreach($sourceHash as $key => $value) {
			$prop = new self();
			$prop->name = $key;
			$prop->value = $value;
			$ret[] = $prop;
		}
		return $ret;
	}
	/**
	 * transform back to a hash
	 *
	 * @param Foomo\Services\Types\Property[] $servicePropertyArray
	 * @return array a regular hash
	 */
	public static function castToHash($servicePropertyArray)
	{
		$ret = array();
		foreach($servicePropertyArray as $serviceProp) {
			$ret[$serviceProp->name] = $serviceProp->value;
		}
		return $ret;
	}
}