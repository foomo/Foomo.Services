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

namespace Foomo\Services\RPC\Serializer;

/**
 * AMF (un)serializer
 *
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  jan <jan@bestbytes.de>
 */
class AMF implements SerializerInterface
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	const TYPE              = 'serviceTypeRpcAmf';
	const BACK_END_AMF_EXT  = 0;
	const BACK_END_ZEND_AMF = 1;

	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 * @var string
	 */
	private $encodingFlags;
	/**
	 * @var int
	 */
	private $backEnd;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function __construct()
	{
		$amf3 = false;
		$this->encodingFlags = (pack("d", 1) ? 2 : 0) | ($amf3 ? 1 : 0);
		if (!function_exists('amf_encode')) {
			$this->backEnd = self::BACK_END_ZEND_AMF;
		} else {
			$this->backEnd = self::BACK_END_AMF_EXT;
		}
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @return string
	 */
	public function getType()
	{
		return self::TYPE;
	}

	/**
	 * serialize
	 *
	 * @param mixed $call
	 * @return string serialized data
	 */
	public function serialize($call)
	{

		if ($this->backEnd == self::BACK_END_AMF_EXT) {
			return amf_encode($call, $this->encodingFlags, __CLASS__ . '::callBackEncode');
		} else {
			$outStream = new \Zend_Amf_Parse_OutputStream();
			$ser = new \Zend_Amf_Parse_Amf0_Serializer($outStream);
			$ser->writeTypeMarker($call);
			return $outStream->getStream();
		}
	}

	/**
	 * unserialize
	 *
	 * @param string $serialized
	 * @return mixed unserialized call
	 */
	public function unserialize($serialized)
	{
		if ($this->backEnd == self::BACK_END_AMF_EXT) {
			return amf_decode($serialized, $this->encodingFlags, 0, __CLASS__ . '::callBackDecode');
		} else {
			$inputStream = new \Zend_Amf_Parse_InputStream($serialized);
			$unSer = new \Zend_Amf_Parse_Amf0_Deserializer($inputStream);
			return $unSer->readTypeMarker();
		}
	}

	/**
	 *
	 * @param int    $arg
	 * @param string $event
	 * @return array
	 */
	public static function callBackEncode($arg, $event)
	{
		$args = func_get_args();
		//trigger_error(__METHOD__ . var_export($args, true));
		switch ($event) {
			case 1: // AMFE_MAP
				if (is_object($arg) && strpos(get_class($arg), '\\') !== false) {
					$classname = str_replace('\\', '.', get_class($arg));
					return array(
						$arg,      // value
						3,         // AMFC_TYPEDOBJECT type
						$classname // classname
					);
				}
		}
	}

	/**
	 *
	 * @param int    $event
	 * @param string $arg
	 * @return stdClass
	 */
	public static function callBackDecode($event, $arg)
	{
		switch ($event) {
			case 1: // AMFE_MAP
				if (strpos($arg, '.') !== false) {
					//trigger_error(__FUNCTION__ . $event . ' ========> ' . $arg);
					$ret = str_replace('.', '\\', $arg);
					if (class_exists($ret)) {
						return new $ret;
					}
				}
				break;
			default:
				// trigger_error($event . ' => ' . $arg);
				break;
		}
	}

	/**
	 * @return string
	 */
	public function getContentMime()
	{
		return 'application/x-amf';
	}

	/**
	 * @return boolean
	 */
	public function supportsTypes()
	{
		return true;
	}
}
