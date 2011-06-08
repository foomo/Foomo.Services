<?php

namespace Foomo\Services\RPC\Serializer;

/**
 * streamline RPC calls
 *
 */
interface SerializerInterface {
	/**
	 * serialize
	 *
	 * @param mixed $var
	 * 
	 * @return string serialized data
	 */
	public function serialize($call);
	/**
	 * unserialize
	 *
	 * @param string $serialized
	 * 
	 * @return mixed unserialized call
	 */
	public function unserialize($serialized);
	/**
	 * what is your mime type
	 */
	public function getContentMime();
	/**
	 * tells, if this serializer supports types or not
	 * 
	 * @return boolean
	 */
	public function supportsTypes();
}
