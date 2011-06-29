<?php

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