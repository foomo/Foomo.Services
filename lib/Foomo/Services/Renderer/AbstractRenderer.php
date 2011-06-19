<?php

namespace Foomo\Services\Renderer;

use Foomo\Services\Reflection\ServiceObjectType;
use Foomo\Services\Reflection\ServiceOperation;

/**
 * extend, if you want to render services as a pizza or somthing else useful
 */
abstract class AbstractRenderer
{
	//---------------------------------------------------------------------------------------------
	// ~ Abstract methods
	//---------------------------------------------------------------------------------------------

	/**
	 * prepare your assets
	 *
	 * @param string $serviceName name of the service class
	 */
	abstract public function init($serviceName);

	/**
	 * render the service type itself
	 *
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 */
	abstract public function renderServiceType(ServiceObjectType $type);

	/**
	 * render an operation / method of the services class
	 *
	 * @param Foomo\Services\Reflection\ServiceOperation $op
	 */
	abstract public function renderOperation(\Foomo\Services\Reflection\ServiceOperation $op);

	/**
	 * render a Type
	 *
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 */
	abstract public function renderType(ServiceObjectType $type);

	/**
	 * return the thing you rendered
	 *
	 * @return mixed
	 */
	abstract public function output();

	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $className
	 * @param AbstractRenderer $renderer
	 * @return mixed
	 */
	public static function render($className, AbstractRenderer $renderer = null)
	{
		$reflection = new \Foomo\Services\Reflection($className);

		$calledClassName = \get_called_class();

		if (is_null($renderer)) $renderer = new $calledClassName;

		$renderer->init($reflection->getClassName());
		$renderer->renderServiceType($reflection->getServiceType());
		$types = $reflection->getTypes();
		$renderedTypes = array();
		foreach ($types as $type) {
			$fullType = $type->type;
			if ($type->isArrayOf) {
				$fullType .= 'Array';
			}
			if (in_array($fullType, $renderedTypes)) {
				continue;
			}
			$renderedTypes[] = $fullType;
			/* @var $type Foomo\Services\Reflection\ServiceObjectType */
			$renderer->renderType($type);
		}

		$ops = $reflection->getOperations();
		foreach ($ops as $op) {
			$renderer->renderOperation($op);
		}

		return $renderer->output();
	}
}