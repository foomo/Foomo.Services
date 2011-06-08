<?php

namespace Foomo\Services\Renderer;

use Foomo\Services\Reflection\ServiceObjectType;
use Foomo\Services\Reflection\ServiceOperation;

/**
 * renders a html documentation for a service
 */
final class HtmlDocs extends AbstractRenderer {
	/**
	 * @private
	 */
	const TEMPLATE_FOLDER = 'serviceHtmlDocs';
	private $indent = 0;
	public $opsHtmlToc = '';
	public $opsHtml = '';
	public $typesHtml = '';
	public $serviceName;
	public $serviceClassDocs = '';
	private $renderedTypes;
	public $types = array();
	public $baseTypes = array('float', 'integer', 'boolean', 'string', 'mixed'); 
	public function init($serviceName)
	{
		$this->serviceName = $serviceName;
		$this->renderedTypes = array();
	}
	/**
	 * render the service type itself
	 * 
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderServiceType(ServiceObjectType $type)
	{
		//$this->out = 'aaaaaaaaaaaaaaa';
		$this->serviceClassDocs = $type->phpDocEntry->comment;
	}
	public function renderType(ServiceObjectType $type, $level = 0, $propName = null)
	{
		if(!($this->isBaseType($type->type) && $level < 1)) {
			if(!isset($this->types[$type->type])) {
				$this->types[$type->type] = $type;
			}
			$level ++;
			if($this->typeIsInRecursion($type) && $level == 1) {
				// skipping double type docs on top level
				return;
			} else {
				$view = \Foomo\Services\Module::getView($this, self::TEMPLATE_FOLDER . DIRECTORY_SEPARATOR . 'type', array('renderer' => $this, 'type' => $type, 'level' => $level, 'propName' => $propName));
				$this->typesHtml .= $view->render();
				if(!$this->typeIsInRecursion($type) && $level == 1) {
					if (count($type->props) > 0) {
						array_push($this->renderedTypes, $type->type);
					}
					if(count($type->props) > 0) {
						$this->indent ++;
						foreach ($type->props as $propName => $propValue) {
							$this->renderType($propValue, $level, $propName);
						}
					}
				}
				$level --;
			}
		}		
	}
	public function typeIsInRecursion(ServiceObjectType $type)
	{
		if(in_array($type->type,$this->renderedTypes)) {
			return true;
		} else {
			return false;
		}
	}
	public function isBaseType($type)
	{
		$type = str_replace('[]', '', $type);
		if(in_array($type, $this->baseTypes)) {
			return true;
		} else {
			return false;
		}
	}
	
	public function renderOperation(ServiceOperation $op)
	{
		$this->opsHtmlToc .= \Foomo\Services\Module::getView($this, self::TEMPLATE_FOLDER . DIRECTORY_SEPARATOR . 'operationTocEntry', array('op' => $op, 'renderer' => $this));
		$this->opsHtml .= \Foomo\Services\Module::getView($this, self::TEMPLATE_FOLDER . DIRECTORY_SEPARATOR . 'operation', array('op' => $op, 'renderer' => $this))->render();
	}
	public function output()
	{	
		$view = \Foomo\Services\Module::getView($this, self::TEMPLATE_FOLDER . DIRECTORY_SEPARATOR . 'service', $this) ;
		return $view->render();
	}
	public function typeLink($type)
	{
		return str_replace('[]', '', $type->type);
	}
}

