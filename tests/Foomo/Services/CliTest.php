<?php
namespace Foomo\Services\RPC;

use Foomo\Services\Cli;

class CliTest extends \PHPUnit_Framework_TestCase {
	public function testParseArgs()
	{
		$rawArgs = array('stringVal', '111', '1.234', '[1,2,3,"hans", "peter"]', '{"a":1,"b":"lalala"}', '{"prop":"value"}');
		$args = \Foomo\Services\Cli::parseArgs('Foomo\\Services\\Mock\\Cli', 'cliArgs', $rawArgs);
		$this->assertInternalType('string', $args[0]);
		$this->assertInternalType('int', $args[1]);
		$this->assertInternalType('float', $args[2]);
		$this->assertInternalType('array', $args[3]);
		$this->assertInternalType('array', $args[4]);
		$this->assertInstanceOf('stdClass', $args[5]);
	}
} 