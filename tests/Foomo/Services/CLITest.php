<?php

namespace Foomo\Services;

class CLITest extends \PHPUnit_Framework_TestCase
{
	//---------------------------------------------------------------------------------------------
	// ~ Test methods
	//---------------------------------------------------------------------------------------------

	public function testParseArgs()
	{
		$rawArgs = array('stringVal', '111', '1.234', '[1,2,3,"hans", "peter"]', '{"a":1,"b":"lalala"}', '{"prop":"value"}');
		$args = \Foomo\Services\Cli::parseArgs('Foomo\Services\Mock\Cli', 'cliArgs', $rawArgs);
		$this->assertType('string', $args[0]);
		$this->assertType('int', $args[1]);
		$this->assertType('float', $args[2]);
		$this->assertType('array', $args[3]);
		$this->assertType('array', $args[4]);
		$this->assertType('object', $args[5]);
	}
}