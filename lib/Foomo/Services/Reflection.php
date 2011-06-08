<?php
namespace Foomo\Services;

use Foomo\Services\Renderer\RendererInterface;
use ReflectionClass;
use Foomo\Reflection\PhpDocEntry;
use Foomo\Reflection\PhpDocArg;
use Foomo\Services\Reflection\ServiceObjectType;
use Foomo\Services\Reflection\ServiceOperation;


/**
 * reads a given class and all types referenced
 */
class Reflection {
	/**
	 * service type
	 * 
	 * @var Foomo\Services\Reflection\ServiceObjectType
	 */
	private $serviceType;
	private $types = array();
	private $operations = array();
	private $className;
	public function __construct($className)
	{
		$this->className = $className;
		$this->read($className);
	}
	public function getClassName()
	{
		return $this->className;
	}
	/**
	 * get all types used in service
	 *
	 * @return Foomo\Services\Reflection\ServiceObjectType[]
	 */
	public function getTypes()
	{
		return $this->types;
	}
	/**
	 * get the operations on this service
	 * 
	 * @return Foomo\Services\Reflection\ServiceOperation[]
	 */
	public function getOperations()
	{
		return $this->operations;
	}
	/**
	 * the service itself
	 *
	 * @return Foomo\Services\Reflection\ServiceObjectType
	 */
	public function getServiceType()
	{
		return $this->serviceType;
	}
	private function read($className)
	{
		$this->serviceType = new ServiceObjectType($className);
		$ref = new ReflectionClass($className);
		$methods = $ref->getMethods();
		foreach ($methods as $method) {
			/* @var $method ReflectionMethod */
			$methodName = $method->getName();
			$phpDoc = new PhpDocEntry($method->getDocComment());
			//echo $methodName . ' >' . $phpDoc->wsdlGen . '<, ';
			if($method->isStatic() || !$method->isPublic() || strpos($methodName, '__') === 0 || $phpDoc->wsdlGen === 'ignore' || $phpDoc->serviceGen === 'ignore') {
			  //echo 'skipping ' . $methodName;
				continue;
			} else {
				// docs
				$comment = $phpDoc->comment;
				// return
				if(isset($phpDoc->return)) {
					$returnType = $this->registerType($phpDoc->return);//, $methodName.'Return');
				} else {
					$returnType = null;
				}
				// throws
				$throwsTypes = array();
				foreach($phpDoc->throws as $throws) {
					$throwsTypes[] = $this->registerType($throws);
				}
				// messages
				$messageTypes = array();
				foreach($phpDoc->serviceMessage as $messageType) {
					$messageTypes[] = $this->registerType($messageType);
				}
				$this->operations[$methodName] = new ServiceOperation($methodName, $returnType, $throwsTypes, $messageTypes, $comment);
				// params
				foreach ($phpDoc->parameters as $parm) {
					$this->operations[$methodName]->addParameter($parm->name, $parm->type);
					$this->operations[$methodName]->addParameterDocs($parm->name, $parm);
					$this->registerType($parm);//, $parm->type);
				}
			}
		}
	}
	/**
	 * register and return atype
	 *
	 * @param Foomo\Reflection\PhpDocArg $arg
	 *
	 * @return Foomo\Reflection\PhpDocArg
	 */
	private function registerType(PhpDocArg $arg)
	{
		/* @var $parm Foomo\Reflection\PhpDocArg */
		if(!isset($this->types[$arg->type])) {
			$newType = new ServiceObjectType($arg->type);
			$this->types[$arg->type] = $newType;
			$this->scanForNestedTypes($newType);
		}
		return $arg;
	}
	private function scanForNestedTypes(ServiceObjectType $type)
	{
	  foreach($type->props as $subType) {
	    if(!isset($this->types[$subType->type])) {
	      $this->types[$subType->type] = $subType;
	      $this->scanForNestedTypes($subType);
	    }
	  }
	}
}
