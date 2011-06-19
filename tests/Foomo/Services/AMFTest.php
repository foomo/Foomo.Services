<?php

namespace Foomo\Services;

class AMFTest extends \PHPUnit_Framework_TestCase
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var AMFSerializer
	 */
	public $serializer;

	//---------------------------------------------------------------------------------------------
	// ~ Initialization
	//---------------------------------------------------------------------------------------------

	public function setUp()
	{
		$this->serializer = new RPC\Serializer\AMF;
   	}

	//---------------------------------------------------------------------------------------------
	// ~ Test methods
	//---------------------------------------------------------------------------------------------

	public function testObject()
	{
		$obj = (object) array('a' => 1);
		$this->assertEquals($obj, $this->serializer->unserialize($this->serializer->serialize($obj)));
   	}
}
