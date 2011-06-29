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

namespace Foomo\Services\SOAP\Frontend;

class Controller
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var Foomo\Services\Soap\Frontend\Model
	 */
	public $model;

	//---------------------------------------------------------------------------------------------
	// ~ Action methods
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 */
	public function actionDefault()
	{
		if (isset($_GET['explainMachine'])) {
			\Foomo\MVC::abort();
			\Foomo\Services\SOAP\Utils::explainMachine($this->model->serviceClassInstance);
			exit;
		}
	}

	/**
	 *
	 */
	public function actionServe()
	{
		$this->model->serve();
	}

	/**
	 *
	 */
	public function actionWsdl()
	{
		\Foomo\MVC::abort();
		$this->model->streamWsdl();
		exit;
	}

	/**
	 *
	 */
	public function actionGetASProxy()
	{
		\Foomo\MVC::abort();
		\Foomo\Services\SOAP\Utils::compileASProxy();
		exit;
	}

	/**
	 *
	 */
	public function actionRecompileWsdl()
	{
		$this->model->compileWsdl();
		$this->actionWsdl();
	}
}