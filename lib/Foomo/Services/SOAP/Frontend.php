<?php


namespace Foomo\Services\SOAP;

use Foomo\MVC\AbstractApp;

class Frontend extends AbstractApp {
	public function __construct($serviceInstance)
	{
		parent::__construct(__CLASS__);
		$this->model->setServiceInstance($serviceInstance);
	}
}