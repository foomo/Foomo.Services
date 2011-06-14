<?php


namespace Foomo\Services\SOAP\Frontend;

use Foomo\MVC;

class Controller {
	/**
	 * @var Model
	 */
	public $model;
	public function actionDefault() {}
	/*
		if(count($_GET) == 0) {
			$this->internalServeClass();
		} elseif(isset($_GET['explain'])) {
			Utils::explain($this);
		} elseif(isset($_GET['wsdl'])) {
			$this->wsdl();
		} elseif(isset($_GET['explainMachine']) ) {
			Utils::explainMachine($this);
		} elseif(in_array(\Foomo\Config::getMode(), array(\Foomo\Config::MODE_DEVELOPMENT, \Foomo\Config::MODE_TEST))) {
			if(isset($_GET['compileProxy'])) {
				Utils::compileProxy($this);
			} elseif(isset($_GET['compileServer'])) {
				Utils::compileServer($this);
			} elseif(isset($_GET['getASProxySWC'])) {
				Utils::getASProxy($this, true);
			} elseif(isset($_GET['getASProxy'])) {
				Utils::getASProxy($this);
			}
		}
	 */
	public function actionServe()
	{
		$this->model->serve();
	}
	public function actionWsdl()
	{
		MVC::abort();
		$this->model->streamWsdl();
		exit;
	}
	public function actionGetASProxy()
	{
		MVC::abort();
		\Foomo\Services\SOAP\Utils::compileASProxy();
		exit;
	}
	public function actionRecompileWsdl()
	{
		$this->model->compileWsdl();
		$this->actionWsdl();
	}
}