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

namespace Foomo\Services\Renderer;

/**
 * renders a html documentation for a service
 *
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  jan <jan@bestbytes.de>
 */
final class HtmlDocs extends AbstractRenderer
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	/**
	 * @private
	 */
	const TEMPLATE_FOLDER = 'serviceHtmlDocs';

	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var integer
	 */
	private $indent = 0;
	/**
	 * @var string
	 */
	public $opsHtmlToc = '';
	/**
	 * @var string
	 */
	public $opsHtml = '';
	/**
	 * @var string
	 */
	public $typesHtml = '';
	/**
	 * @var string
	 */
	public $serviceName;
	/**
	 * @var string
	 */
	public $serviceClassDocs = '';
	/**
	 * @var array
	 */
	private $renderedTypes;
	/**
	 * @var array
	 */
	public $types = array();
	/**
	 * @var array
	 */
	public $baseTypes = array('float', 'integer', 'boolean', 'string', 'mixed', 'bool', 'int', 'double', 'array');

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $serviceName
	 */
	public function init($serviceName)
	{
		$this->serviceName = $serviceName;
		$this->renderedTypes = array();
	}

	/**
	 * render the service type itself
	 *
	 * @param \Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderServiceType(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		$this->serviceClassDocs = $type->phpDocEntry->comment;
	}

	/**
	 *
	 * @param \Foomo\Services\Reflection\ServiceObjectType $type
	 * @param integer                                      $level
	 * @param string                                       $propName
	 */
	public function renderType(\Foomo\Services\Reflection\ServiceObjectType $type, $level = 0, $propName = null)
	{
		if ($level == 0 && !$this->isBaseType($type->type) && !in_array($type->type, $this->renderedTypes)) {
			$this->renderedTypes[] = $type->type;
			$view = \Foomo\Services\Module::getView($this, self::TEMPLATE_FOLDER . DIRECTORY_SEPARATOR . 'type', array('renderer' => $this, 'type' => $type, 'level' => $level, 'propName' => $propName));
			$this->typesHtml .= $view->render();
		}
	}

	/**
	 * @param \Foomo\Services\Reflection\ServiceObjectType $type
	 * @return boolean
	 */
	public function typeIsInRecursion(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		return (in_array($type->type, $this->renderedTypes));
	}

	/**
	 * @param string $type
	 * @return boolean
	 */
	public function isBaseType($type)
	{
		return (in_array(str_replace('[]', '', $type), $this->baseTypes));
	}

	/**
	 * @param \Foomo\Services\Reflection\ServiceOperation $op
	 */
	public function renderOperation(\Foomo\Services\Reflection\ServiceOperation $op)
	{
		$this->opsHtmlToc .= \Foomo\Services\Module::getView($this, self::TEMPLATE_FOLDER . DIRECTORY_SEPARATOR . 'operationTocEntry', array('op' => $op, 'renderer' => $this));
		$this->opsHtml .= \Foomo\Services\Module::getView($this, self::TEMPLATE_FOLDER . DIRECTORY_SEPARATOR . 'operation', array('op' => $op, 'renderer' => $this))->render();
	}

	/**
	 * @return string
	 */
	public function output()
	{
		return \Foomo\Services\Module::getView($this, self::TEMPLATE_FOLDER . DIRECTORY_SEPARATOR . 'service', $this)->render();
	}

	/**
	 * @param mixed $type
	 * @return string
	 */
	public function typeLink($type)
	{
		return str_replace('[]', '', $type->type);
	}
}
