<?php

namespace Foomo\Services\SOAP;

class Frontend extends \Foomo\MVC\AbstractApp
{
	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * @param stdClass $serviceInstance
	 */
	public function __construct($serviceInstance)
	{
		parent::__construct(__CLASS__);
		$this->model->setServiceInstance($serviceInstance);
	}
}