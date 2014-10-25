<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Services\SOAP;

use Foomo\Services\Reflection\ServiceObjectType;

/**
 * generate a wsdl for a web service
 *
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  jan <jan@bestbytes.de>
 */
final class WSDLRenderer extends AbstractRenderer
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var \DOMDocument
	 */
	private $dom;
	/**
	 * @var \DOMElement
	 */
	private $opsElement;
	/**
	 * @var \DOMElement
	 */
	private $typesElement;
	/**
	 * @var \DOMElement
	 */
	private $rootElement;
	/**
	 * @var \DOMElement
	 */
	private $bindingElement;
	/**
	 * @var array
	 */
	private $types;
	/**
	 * @var array
	 */
	private $rawTypes;
	/**
	 * @var array
	 */
	private $typesAdded;
	/**
	 * @var array
	 */
	private $ops;
	/**
	 * @var string
	 */
	private $serviceName;

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	public function init($serviceName)
	{
		$this->serviceName = $serviceName = str_replace('\\', '', $serviceName);
		$this->ops = array();
		$this->types = array();
		$this->rawTypes = array();
		$this->typesAdded = array();
		$this->dom = new \DOMDocument('1.0', 'UTF-8');
		$rootComment = new \DOMComment('generated by ' . __CLASS__ . '  ' . date('Y-m-d H:i:s') . ' on ' . $_SERVER['HTTP_HOST'] . ' check this URL for more #endPointPlaceHolder#?explain');
		$this->dom->appendChild($rootComment);
		$this->rootElement = $this->dom->appendChild($this->dom->createElementNS('http://schemas.xmlsoap.org/wsdl/', 'definitions'));
		$this->addAttributes(
			array(
				'name'            => $serviceName,
				'targetNamespace' => 'urn:' . $serviceName,
				'xmlns:typens'    => 'urn:' . $serviceName,
				'xmlns:soap'      => 'http://schemas.xmlsoap.org/wsdl/soap/',
				'xmlns:xsd'       => 'http://www.w3.org/2001/XMLSchema',
				'xmlns:wsdl'      => 'http://schemas.xmlsoap.org/wsdl/',
				'xmlns:soapenc'   => 'http://schemas.xmlsoap.org/soap/encoding/'
			), $this->rootElement
		);

		$this->opsElement = $this->rootElement->appendChild(new \DOMElement('portType'));
		$this->addAttributes(
			array(
				'name' => $serviceName . 'Port'
			), $this->opsElement
		);
		$typeEl = $this->rootElement->appendChild(new \DOMElement('types'));
		$this->typesElement = $typeEl->appendChild($this->dom->createElement('xsd:schema'));
		$this->addAttributes(
			array('targetNamespace' => 'urn:' . $serviceName), $this->typesElement
		);

		$this->bindingElement = $this->dom->createElement('binding');
		$this->addAttributes(
			array(
				'name' => $serviceName . 'Binding',
				'type' => 'typens:' . $serviceName . 'Port'
			), $this->bindingElement
		);
		$soapBindingElement = $this->bindingElement->appendChild($this->dom->createElement('soap:binding'));
		$this->addAttributes(
			array(
				'style'     => 'rpc',
				'transport' => 'http://schemas.xmlsoap.org/soap/http'
			), $soapBindingElement
		);
	}

	/**
	 * render the service type itself
	 *
	 * @param \Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderServiceType(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
	}

	/**
	 * @param \Foomo\Services\Reflection\ServiceOperation $op
	 */
	public function renderOperation(\Foomo\Services\Reflection\ServiceOperation $op)
	{
		array_push($this->ops, $op);
	}

	/**
	 * @param \Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderType(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		if (!in_array($type, $this->types)) {
			array_push($this->types, $type);
		}
	}

	/**
	 * return the thing you rendered
	 *
	 * @return mixed
	 */
	public function output()
	{
		$typesAdded = array();
		foreach ($this->types as $type) {
			/* @var $type ServiceObjectType */
			$this->addType($type, $this->typesElement);
		}
		$messageElements = array();
		$operationElements = array();
		foreach ($this->ops as $op) {
			/* @var $op ServiceOperation */
			// inputMessage
			$messageElement = $this->dom->createElement('message');
			array_push($messageElements, $messageElement);
			$this->addAttributes(
				array('name' => $op->name . 'Request'), $messageElement
			);
			foreach ($op->parameters as $parameterName => $parameterType) {
				$partElement = $messageElement->appendChild(new \DOMElement('part'));
				$this->addAttributes(
					array(
						'name' => $parameterName,
						'type' => $this->mapType($parameterType)
					), $partElement
				);
			}
			// outputMessage
			$outputMessagElement = $this->dom->createElement('message');
			array_push($messageElements, $outputMessagElement);
			$this->addAttributes(
				array('name' => $op->name . 'Response'), $outputMessagElement
			);
			$partElement = $outputMessagElement->appendChild(new \DOMElement('part'));
			if (!isset($op->returnType)) {
				trigger_error('no return type set for operation ' . $op->name, E_USER_NOTICE);
			}
			$this->addAttributes(
				array(
					'name' => 'return',
					'type' => $this->mapType($op->returnType->type)
				), $partElement
			);
			// ops
			$opElement = $this->opsElement->appendChild(new \DOMElement('operation'));
			$this->addAttributes(
				array('name' => $op->name), $opElement
			);
			$docEl = $opElement->appendChild(new \DOMElement('documentation', $op->comment));
			$inputElement = $opElement->appendChild(new \DOMElement('input'));
			$this->addAttributes(
				array('message' => 'typens:' . $op->name . 'Request'), $inputElement
			);
			$outputElement = $opElement->appendChild(new \DOMElement('output'));
			$this->addAttributes(
				array('message' => 'typens:' . $op->name . 'Response'), $outputElement
			);
			// binding
			$opElement = $this->bindingElement->appendChild(new \DOMElement('operation'));
			$this->addAttributes(
				array('name' => $op->name), $opElement
			);
			$soapActionElement = $opElement->appendChild($this->dom->createElement('soap:operation'));
			$this->addAttributes(
				array(
					'soapAction' => 'urn:' . $this->serviceName . 'Action'
				), $soapActionElement
			);
			$toDoList = array('input', 'output');
			foreach ($toDoList as $toDo) {
				$el = $opElement->appendChild(new \DOMElement($toDo));
				$soapEl = $el->appendChild($this->dom->createElement('soap:body'));
				$this->addAttributes(
					array(
						'use'           => 'encoded',
						'namespace'     => 'urn:' . $this->serviceName,
						'encodingStyle' => 'http://schemas.xmlsoap.org/soap/encoding/'
					), $soapEl
				);
			}
		}
		// pump up xml
		foreach ($messageElements as $messageElement) {
			$this->rootElement->appendChild($messageElement);
		}
		$this->rootElement->appendChild($this->opsElement);
		$this->rootElement->appendChild($this->bindingElement);
		$portEl = $this->rootElement->appendChild(new \DOMElement('service'));
		$this->addAttributes(
			array(
				'name' => $this->serviceName . 'Service'
			), $portEl
		);
		$portPortEl = $portEl->appendChild(new \DOMElement('port'));
		$this->addAttributes(
			array(
				'name'    => $this->serviceName . 'Port',
				'binding' => 'typens:' . $this->serviceName . 'Binding'
			), $portPortEl
		);
		$soapAddressEl = $portPortEl->appendChild($this->dom->createElement('soap:address'));
		$this->addAttributes(
			array('location' => '#endPointPlaceHolder#'), //$this->endPoint),
			$soapAddressEl
		);
		return $this->dom->saveXML();
	}

	/**
	 * @return array
	 */
	public function getClassMap()
	{
		$classMap = array();
		foreach ($this->types as $type) {
			switch ($type->type) {
				case'unknown_type':
				case'array':
				case'mixed':
				case'integer':
				case'string':
				case'boolean':
				case'float':
					break;
				default:
					$classMap[$type->type] = $this->rawTypes[$type->type];
			}
		}
		return $classMap;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 * @param \Foomo\Services\Reflection\ServiceObjectType $type
	 * @param \DOMElement                                  $element
	 */
	private function addType(\Foomo\Services\Reflection\ServiceObjectType $type, \DOMElement $element)
	{
		$this->rawTypes[str_replace('\\', '', $type->type)] = $type->type;
		//$type->type = str_replace('\\', '', $type->type);
		if ($type->isArrayOf) {
			$fullType = $this->mapType($type->type) . 'Array';
			$isArrayOf = true;
		} else {
			$fullType = $this->mapType($type->type);
			$isArrayOf = false;
		}
		if (count($type->props) > 0) {
			$isComplex = true;
		} else {
			$isComplex = false;
		}
		if (!in_array($fullType, $this->typesAdded) && ($isArrayOf || $isComplex)) {
			// add the arrayBase if array
			//var_dump($type);
			array_push($this->typesAdded, $fullType);
			$complexTypeElement = $element->appendChild($this->dom->createElement('xsd:complexType'));
			$this->addAttributes(
				array('name' => $fullType), $complexTypeElement
			);

			if ($isComplex && !$isArrayOf) {
				// echo 'adding complex '.$type->type.PHP_EOL;
				$typesElement = $complexTypeElement->appendChild($this->dom->createElement('xsd:all'));
				foreach ($type->props as $propName => $propType) {
					/* @var $propType ServiceObjectType */
					if ($propType->isArrayOf) {
						$propTypeTranslated = $this->mapType($propType->type . '[]');
					} else {
						$propTypeTranslated = $this->mapType($propType->type);
					}
					// echo 'subtype '.$propName.' => '.$propType->type.' ==> '.$propTypeTranslated.' isArray '.var_export($propType->isArrayOf, true).' plainType '.$propType->plainType.PHP_EOL;
					$el = $typesElement->appendChild($this->dom->createElement('xsd:element'));
					$attr = new \DOMAttr('name', $propName);
					$el->appendChild($attr);
					$attr = new \DOMAttr('type', $propTypeTranslated);
					$el->appendChild($attr);
					if (count($propType->props) > 0 || $propType->isArrayOf) {
						$this->addType($propType, $this->typesElement);
					}
				}
			} else {
				// echo 'adding array of '.$type->type.PHP_EOL;
				array_push($this->typesAdded, $fullType);
				if ($isComplex) {
					// echo '-------------------------------->'.$type->type.'[][][]'.PHP_EOL;
					$clone = clone $type;
					$clone->isArrayOf = false;
					$this->addType($clone, $this->typesElement);
				}
				$complexContentEl = $complexTypeElement->appendChild($this->dom->createElement('xsd:complexContent'));
				$restrEl = $complexContentEl->appendChild($this->dom->createElement('xsd:restriction'));
				$this->addAttributes(
					array('base' => 'soapenc:Array'), $restrEl
				);
				$attrEl = $restrEl->appendChild($this->dom->createElement('xsd:attribute'));
				$this->addAttributes(
					array(
						'ref'            => 'soapenc:arrayType',
						'wsdl:arrayType' => $this->mapType($type->type) . '[]'
					), $attrEl
				);
			}
		}
	}

	/**
	 * @param array      $attrs
	 * @param \DOMElement $el
	 */
	private function addAttributes($attrs, \DOMElement $el)
	{
		foreach ($attrs as $name => $value) {
			$el->appendChild(new \DOMAttr($name, $value));
		}
	}

	/**
	 * @param string $type
	 * @return string
	 */
	private function mapType($type)
	{
		// echo 'typeToMap ' . $type . PHP_EOL;
		$type = str_replace('\\', '', $type);
		if (substr($type, strlen($type) - 2) == '[]') {
			$isArray = true;
			$type = substr($type, 0, strlen($type) - 2);
			return 'typens:' . $type .= 'Array';
		} else {
			switch ($type) {
				case'unknown_type':
				case'array':
				case'mixed':
					$propTypeTranslated = 'xsd:anyType';
					break;
				case'integer':
					$propTypeTranslated = 'xsd:int';
					break;
				case'string':
				case'boolean':
				case'float':
					$propTypeTranslated = 'xsd:' . $type;
					break;
				default:
					$propTypeTranslated = 'typens:' . $type;
			}
			return $propTypeTranslated;
		}
	}
}