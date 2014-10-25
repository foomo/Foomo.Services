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

namespace Foomo\Services\RPC;

/**
 * @link    www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author  jan <jan@bestbytes.de>
 */
class Frontend extends \Foomo\MVC\AbstractApp
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var \Foomo\Services\RPC\Frontend\Model
	 */
	public $model;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * @internal
	 *
	 * @param mixed                                              $serviceClassInstance
	 * @param \Foomo\Services\RPC\Serializer\SerializerInterface $serializer
	 * @param string                                             $package
	 * @param string                                             $authDomain
	 * @param string                                             $authDomainDev
	 */
	public function __construct(
		$serviceClassInstance,
		\Foomo\Services\RPC\Serializer\SerializerInterface $serializer,
		$package,
		$authDomain = null,
		$authDomainDev = null
	)
	{
		parent::__construct(__CLASS__);

		$this->model->serviceClassInstance = $serviceClassInstance;
		$this->model->serviceClassName = get_class($serviceClassInstance);
		$this->model->serializer = $serializer;
		$this->model->package = $package;
		$this->model->authDomain = $authDomain;
		$this->model->authDomainDev = $authDomainDev;
	}
}
