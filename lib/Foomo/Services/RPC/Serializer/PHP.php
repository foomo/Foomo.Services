<?php

namespace Foomo\Services\RPC\Serializer;

class PHP implements SerializerInterface {
	/**
	 * serialize
	 *
	 * @param mixed $var
	 * 
	 * @return string serialized data
	 */
	public function serialize($call)
	{
		return \serialize($call);
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
	    $ret = @\unserialize($serialized);
	    if($ret === false) {
		   	trigger_error(__METHOD__ . ' could not unserialize data >>>' . $serialized . '<<<', \E_USER_WARNING);
	    }
		return $ret;
	}
	public function getContentMime()
	{
		//@todo check, if this is right
		return 'text/plain';
	}
	public function supportsTypes()
	{
		return true;
	}
}