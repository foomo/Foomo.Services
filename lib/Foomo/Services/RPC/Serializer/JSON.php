<?php

namespace Foomo\Services\RPC\Serializer;

class JSON implements SerializerInterface {
	public function serialize($call)
	{
		return json_encode($call);
	}
	public function unserialize($serialized)
	{
		return json_decode($serialized);
	}
	public function getContentMime()
	{
		return 'application/x-json';
	}
	public function supportsTypes()
	{
		return false;
	}
}