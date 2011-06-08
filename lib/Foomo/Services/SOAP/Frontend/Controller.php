<?php


namespace Foomo\Services\SOAP\Frontend;

class Controller {
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
}