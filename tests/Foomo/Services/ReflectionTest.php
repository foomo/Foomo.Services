<?php
namespace Foomo\Services;
/**
 * 
 */
class ReflectionTest extends \PHPUnit_Framework_TestCase {
	/**
	 * my reader
	 *
	 * @var ServiceReader
	 */
	private $reader;
	public function setUp()
	{
		$this->reader = new Reflection('Foomo\Services\Mock\Service');
	}
	public function testOperationsNoStatic()
	{
		foreach($this->reader->getOperations() as $op) {
			/* @var $op ServiceOperation */
			if($op->name == 'fooStatic') {
				$this->fail('fooStatic should not have been exposed, because it is static');
			}
		}
	}
	public function testServigenIgnore()
	{
		foreach($this->reader->getOperations() as $op) {
			/* @var $op ServiceOperation */
			if($op->name == 'noExpose') {
				$this->fail('noExpose was annotated with @serviceGen ignore and should not have been in the operations');
			}
		}
	}
	public function testTypes()
	{
		$expectedTypes = array(
			'integer',
			'boolean',
			'Foomo\Services\Mock\Exception',
			'Foomo\Services\Mock\FunkyStar',
			'Foomo\Services\Mock\Message'
		);
		$types = array();
		foreach($this->reader->getTypes() as $type) {
			/* @var $type ServiceObjectType */
			$types[] = $type->type;
		}
		$types = array_unique($types);
		foreach($expectedTypes as $expectedType) {
			if(!in_array($expectedType, $types)) {
				$this->fail('missing type ' . $expectedType);
			}
		}
	}
	public function testMessages()
	{
		$expectedMessageTypes = array('string', 'Foomo\Services\Mock\Message');
		$messageTypes = array();
		foreach($this->reader->getOperations() as $op) {
			/* @var $op ServiceOperation */
			if($op->name == 'getSomeFunkyStars') {
				foreach ($op->messageTypes as $type) {
					$messageTypes[] = $type->type;
				}
				break;
			}
		}
		sort($messageTypes);
		sort($expectedMessageTypes);
		$this->assertEquals($expectedMessageTypes, $messageTypes, 'expected and returned message types did not match');
	}
	public function testExceptions()
	{
		$expectedExceptionTypes = array('Foomo\Services\Mock\Exception');
		$exceptionTypes = array();
		foreach($this->reader->getOperations() as $op) {
			/* @var $op ServiceOperation */
			if($op->name == 'makeException') {
				foreach ($op->throwsTypes as $type) {
					$exceptionTypes[] = $type->type;
				}
				break;
			}
		}
		sort($exceptionTypes);
		sort($expectedExceptionTypes);
		$this->assertEquals($expectedExceptionTypes, $exceptionTypes, 'expected and returned exception types did not match');
	}
	public function testOperations()
	{
		$expectedOps = array('iPlusPlus', 'getAFunkyStar', 'getSomeFunkyStars', 'getThatDamnArrayArray', 'makeException', 'addNumbers');
		$ops = array();
		foreach($this->reader->getOperations() as $op) {
			/* @var $op ServiceOperation */
			$ops[] = $op->name;
		}
		sort($expectedOps);
		sort($ops);
		$this->assertEquals($expectedOps, $ops);
	}
}