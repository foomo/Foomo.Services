<?php

namespace Foomo\Services\Renderer;

use Foomo\Services\Reflection\ServiceObjectType;
use Foomo\Services\Reflection\ServiceOperation;
/**
 * plain text docs rendering
 */
final class PlainDocs extends AbstractRenderer {
	private $indent = 0;
	private $out = '';
	private $typesOut = '';
	private $opsOut;
	public function init($serviceName)
	{
		$this->out = $serviceName . ' ('. date('Y-m-d H:i:s') .') : ' . PHP_EOL . PHP_EOL;
		$this->indent = 0;
	}
	/**
	 * render the service type itself
	 * 
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderServiceType(ServiceObjectType $type)
	{
		$this->out .= $type->phpDocEntry->comment . PHP_EOL;
	}
	public function renderType(ServiceObjectType $type, $nested = false)
	{
		static $renderedTypes = array();
		if(in_array($type->type,$renderedTypes) && $nested || $nested && count($type->props) > 0) {
			$this->typesOut .= 'see type - '.$type->type.PHP_EOL;
			return;
		} elseif (count($type->props) > 0) {
			array_push($renderedTypes, $type->type);
		}
		/*
		if($type->isArrayOf) {
			$this->typesOut .= 'ArrayOf ';
		}
		*/
		if(count($type->props) > 0) {
			$this->typesOut .= 'Type '.$type->type;
			$this->typesOut .= '(complex) : ';
			if(isset($type->phpDocEntry) && !empty($type->phpDocEntry->comment)) {
				$this->typesOut .= $type->phpDocEntry->comment;
			}
			$this->typesOut .= PHP_EOL;
			$this->indent ++;
			foreach ($type->props as $propName => $propValue) {
				$this->typesOut .= str_repeat('  ', ($this->indent+1)).$propName . ' : ';
				$this->renderType($propValue, true);
			}
			$this->typesOut .= PHP_EOL;
			$this->indent --;
		} elseif($nested) {
			$this->typesOut .= $type->type;
			if(!empty($type->phpDocEntry->comment)) {
				$this->typesOut .= ' - ' . $type->phpDocEntry->comment;
			}
			$this->typesOut .= PHP_EOL;
		}
	}
	public function renderOperation(ServiceOperation $op)
	{
		$this->opsOut .= str_repeat('	', $this->indent).'operation '.$op->name.' ('.$op->comment.') '.PHP_EOL;
		foreach ($op->parameters	as $parmName => $parmType) {
			$this->opsOut .= str_repeat('  ', ($this->indent+1)).$parmName.' => '.$parmType.PHP_EOL;
		}
		if($op->returnType) {
			$this->opsOut .= '	returns ' . $op->returnType->type ;
			if(!empty($op->returnType->comment)) {
				$this->opsOut .=  ' - ' . $op->returnType->comment;
			}
			$this->opsOut .= PHP_EOL;
		}
		if(count($op->throwsTypes) > 0) {
			$this->opsOut .= '	throws ' . PHP_EOL;
			foreach($op->throwsTypes as $throwsType) {
				$this->opsOut .= '		' . $throwsType->type . PHP_EOL;
			}
		}
		if(count($op->messageTypes) > 0) {
			$this->opsOut .= '	messages' . PHP_EOL;
			foreach($op->messageTypes as $messageType) {
				$this->opsOut .= '		' . $messageType->type . PHP_EOL;
			}
		}
		$this->opsOut .= PHP_EOL;

	}
	public function output()
	{
		return 
			$this->out . PHP_EOL . 
			'TYPES:' . PHP_EOL . PHP_EOL . 
			$this->typesOut . PHP_EOL . 
			'OPERATIONS:' . PHP_EOL . PHP_EOL . 
			$this->opsOut . PHP_EOL
		;
	}
}
