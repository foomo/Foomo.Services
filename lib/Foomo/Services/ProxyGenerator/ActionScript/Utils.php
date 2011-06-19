<?php

namespace Foomo\Services\ProxyGenerator\ActionScript;

class Utils
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * PHP type => ActionScript tpye
	 *
	 * @var string[]
	 */
	private static $getStandarTypes = array(
		'int' => 'int',
		'integer' => 'int',
		'bool' => 'Boolean',
		'boolean' => 'Boolean',
		'string' => 'String',
		'float' => 'Number',
		'double' => 'Number',
		'mixed' => 'Object',
	);

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * Indent lines by given value
	 *
	 * @param string $lines
	 * @param int $indent
	 * @return string
	 */
	public static function indentLines($lines, $indent)
	{
		$output = array();
		$lines = explode(PHP_EOL, $lines);
		foreach($lines as $line) $output[] = str_repeat(chr(9), $indent) . $line;
		return implode(PHP_EOL, $output);
	}

	/**
	 * render a comment
	 *
	 * @param string $comment multi line comment text
	 * @param integer $indent number of tabs to indent
	 * @return string
	 */
	public static function renderComment($comment)
	{
		$lines = explode(PHP_EOL, $comment);
		$ret = '/**' . PHP_EOL;
		foreach($lines as $line) $ret .= ' * ' . $line . PHP_EOL;
		$ret .= ' */';
		return $ret;
	}

	/**
	 * name => type
	 *
	 * @param string[] $params
	 * @param boolean $includeType
	 * @param boolean $includeThis
	 * @return string
	 */
	public static function renderParameters($params, $includeType=true, $includeThis=false)
	{
		$output = array();
		foreach($params as $name => $type) {
			if ($includeType) {
				$output[] = $name . ':' . Utils::getASType($type);
			} else if (!$includeType && !$includeThis) {
				$output[] = $name;
			} else {
				$output[] = 'this.' . $name;
			}
		}
		return implode(', ', $output);
	}

	/**
	 * name => ServiceObjectType
	 *
	 * @param $props[] $params
	 * @param boolean $includeType
	 * @param boolean $includeThis
	 * @return string
	 */
	public static function renderProperties($props, $includeType=true, $includeThis=false)
	{
		$output = array();
		foreach($props as $name => $type) {
			if ($includeType) {
				$output[] = $name . ':' . Utils::getASType($type->type);
			} else if (!$includeType && !$includeThis) {
				$output[] = $name;
			} else {
				$output[] = 'this.' . $name;
			}
		}
		return implode(', ', $output);
	}

	/**
	 * name => type
	 *
	 * @param string[] $params
	 * @param boolean $includeType
	 * @return string
	 */
	public static function renderParametersAsClassVariables($params, $includeType=true)
	{
		$output = array();
		foreach($params as $name => $type) $output[] = ($includeType) ? $name . ':' . Utils::getASType($type) : $name;
		return implode(', ', $output);
	}

	/**
	 * name => value
	 *
	 * @param string[] $constants
	 * @return string
	 */
	public static function renderConstants($constants)
	{
		if (is_null($constants) || count($constants) == 0) return '';

		$output = array();
		foreach($constants as  $name => $value) {
			switch(gettype($value)) {
				case 'bool':
				case 'boolean':
					$type = 'Boolean';
					$value = ($value) ? 'true' : 'false';
					break;
				case 'int':
				case 'integer':
					$type = 'int';
					break;
				case 'float':
				case 'double':
					$type = 'Number';
					break;
				default:
					$type = 'String';
					$value = "'" . $value . "'";
					break;
			}
			$output[] = '		public static const ' . $name . ':' . $type . ' = ' . $value . ';';

		}
		return implode(PHP_EOL, $output);
	}

	/**
	 * @param string $type
	 * @return bool
	 */
	public static function isASStandardType($type)
	{
		return isset(self::$getStandarTypes[\strtolower($type)]);
	}

	/**
	 * @param string $type
	 * @return bool
	 */
	public static function getASStandardType($type)
	{
		return self::$getStandarTypes[\strtolower($type)];
	}

	/**
	 * map a type between php and ActionScript
	 *
	 * @param string $type php class name | type
	 * @return string ActionScript class name | type
	 */
	public static function getASType($type)
	{
		$asType = '*';
		# check if it's a typed array
		$isArray = (substr($type, strlen($type) - 2) == '[]');
		if ($isArray) $type = \substr($type, 0, strlen($type) - 2);

		if (Utils::isASStandardType($type)) {
			$asType = Utils::getASStandardType($type);
		} else {
			$serviceObjectType = new \Foomo\Services\Reflection\ServiceObjectType($type);
			if ('' != $remoteClass = $serviceObjectType->getRemoteClass()) {
				$remoteClass = basename(str_replace('.', DIRECTORY_SEPARATOR, $remoteClass));
				$asType = $remoteClass;
			} else {
				if (strpos($type, '\\') !== false) {
					$parts = explode('\\', $type);
					$asType = $parts[count($parts) - 1];
				} else {
					$asType = $type;
				}
			}
		}
		return $asType;
	}

	/**
	 * Returns the default value for the given type
	 *
	 * @param string $type php class name | type
	 * @return string ActionScript defaults
	 */
	public static function getASTypeDefaultValue($type)
	{
		$value = 'null';
		$type = self::getASType($type);
		switch ($type) {
			case 'Boolean':
				$value = 'false';
				break;
			case 'int':
			case 'uint':
			case 'Number':
				$value = '0';
				break;
			case 'String':
				$value = "''";
				break;
		}
		return $value;
	}
}