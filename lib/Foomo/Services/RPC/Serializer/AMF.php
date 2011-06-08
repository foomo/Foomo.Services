<?php

namespace Foomo\Services\RPC\Serializer;

/**
 * AMF (un)serializer
 *
 */
class AMF implements SerializerInterface {
	const BACK_END_AMF_EXT = 0;
	const BACK_END_ZEND_AMF = 1;
    private $encodingFlags;
    private $backEnd;
    public function __construct()
    {
      $amf3 = false;
      $this->encodingFlags = (pack("d", 1) ?2:0) | ($amf3 ? 1:0);
      if(!function_exists('amf_encode')) {
      	$this->backEnd = self::BACK_END_ZEND_AMF;
      } else {
      	$this->backEnd = self::BACK_END_AMF_EXT;
      }
      
    }
	/**
	 * serialize
	 *
	 * @param mixed $var
	 * 
	 * @return string serialized data
	 */
	public function serialize($call)
	{
		
		if($this->backEnd == self::BACK_END_AMF_EXT) {
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
	 * 
	 * @return mixed unserialized call
	 */
	public function unserialize($serialized)
	{
		if($this->backEnd == self::BACK_END_AMF_EXT) {
			return amf_decode($serialized, $this->encodingFlags, 0, __CLASS__ . '::callBackDecode');
		} else {
			$inputStream = new \Zend_Amf_Parse_InputStream($serialized);
			$unSer = new \Zend_Amf_Parse_Amf0_Deserializer($inputStream);
			return $unSer->readTypeMarker();
		}
	}
	public static function callBackEncode($arg, $event)
	{
		$args = func_get_args();
		//trigger_error(__METHOD__ . var_export($args, true));
		switch($event) {
			case 1: // AMFE_MAP
				if(is_object($arg) && strpos(get_class($arg), '\\') !== false) {
					$classname = str_replace('\\', '.', get_class($arg));
					return array(
						$arg,      // value
						3,         // AMFC_TYPEDOBJECT type
						$classname // classname
					);
				}
		}
	}
	public static function callBackDecode($event, $arg)
	{
		switch($event) {
			case 1: // AMFE_MAP
				if(strpos($arg, '.') !== false) {
					//trigger_error(__FUNCTION__ . $event . ' ========> ' . $arg);
					$ret = str_replace('.', '\\', $arg);
					if(class_exists($ret)) {
						return new $ret;
					}
				}
				break;
			default:
				// trigger_error($event . ' => ' . $arg);
		}
	}
	public function getContentMime()
	{
		return 'application/x-amf';
	}
	public function supportsTypes()
	{
		return true;
	}
}

