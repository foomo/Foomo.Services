<?php

namespace Foomo\Services\ProxyGenerator\ActionScript;

use Foomo\Services\Reflection\ServiceObjectType;

class SOAP extends AbstractGenerator {
	/**
	 * soap wsdl endpoint the url of the wsdl file
	 *
	 * @var string
	 */
	public $wsdl;
	/**
	 * where the specific templates are
	 *
	 * @var string
	 */
	public $templateFolder = 'soap';
	/**
	 * which folders are needed in this package
	 *
	 * @var array
	 */
	public $packageFolders = array('model', 'model/mxml', 'vo', 'events', 'commands', 'responders');
	public function init($serviceName) 
	{
		$service = new ServiceSoap($this->serviceName);
		$this->wsdl = $service->getEndPoint().'?wsdl';
		parent::init($serviceName);
	}
	/**
	 * this code renders the type translation code
	 *
	 * @param string $code the code to append to
	 * @param ServiceObjectType $type type description
	 * @param integer $indent the indentation level
	 */
	public function renderTranslationCode(&$code, ServiceObjectType $type, $indent)
	{
		// echo PHP_EOL . implode(', ', array_keys($this->complexTypes)) . PHP_EOL;
		// $code .= $this->indent('// translating ' . $type->type . PHP_EOL, $indent);
		if(isset($this->complexTypes[$type->type])) {
			// echo PHP_EOL . '// type' . $type->type . PHP_EOL;
			foreach($type->props as $propName => $propType) {
				// echo PHP_EOL .'// type is ' . $propType->type .PHP_EOL;
				if(!isset($this->complexTypes[$propType->type])) {
					if($propType->isArrayOf) {
						$code .= $this->indent('if(wsResult.' . $propName .' is ArrayCollection) {', $indent) . PHP_EOL;
						$indent ++;
						$code .= $this->indent('target.' . $propName . ' = wsResult.' . $propName .'.toArray();', $indent) . PHP_EOL;
						$indent --;
						$code .= $this->indent('}', $indent)  . PHP_EOL;
					} else {
						$code .= $this->indent('target.' . $propName . ' = wsResult.' . $propName .';', $indent) . PHP_EOL;
					}
				} else {
					if($propType->isArrayOf) {
						$code .= $this->indent('target.' . $propName . ' = new Array;' , $indent). PHP_EOL;
						$code .= $this->indent('this.helpWith'.$propType->type.'(wsResult.' . $propName . ', target.' . $propName . ', this.proxy.' . $this->complexTypeToImplClassName($propType) . ');', $indent) . PHP_EOL;
					} else {
						$code .= $this->indent('if(wsResult.' . $propName.') {', $indent)  . PHP_EOL;
						$indent ++;
						$code .= $this->indent('target.' . $propName . ' = new ' . $propType->type . ';', $indent)  . PHP_EOL;
						$code .= $this->indent('this.helpWith'.$propType->type.'(wsResult.' . $propName . ', target.' . $propName . ', this.proxy.' . $this->complexTypeToImplClassName($propType) . ');', $indent) . PHP_EOL;
						$indent --;
						$code .= $this->indent('}', $indent)  . PHP_EOL;
					}
				}
			}
		} else {
			$code .= $this->indent('target = wsResult;', $indent);
		}
	}
	public function output()
	{
		// internal helper
		$view = $this->getView('WebServiceHelper.tpl');
		$this->classFiles['model/WebServiceHelper'] = $view->render();
		return parent::output();
	}
}