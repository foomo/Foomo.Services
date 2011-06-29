<?php


namespace Foomo\Services\RPC;

class Frontend extends \Foomo\MVC\AbstractApp
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var Foomo\Services\RPC\Frontend\Model
	 */
	public $model;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * @param stdClass $serviceClassInstance
	 * @param Foomo\Services\RPC\Serializer\SerializerInterface $serializer
	 * @param string $package
	 */
	public function __construct($serviceClassInstance, \Foomo\Services\RPC\Serializer\SerializerInterface $serializer, $package)
	{
		parent::__construct(__CLASS__);

		$this->model->serviceClassInstance = $serviceClassInstance;
		$this->model->serviceClassName = get_class($serviceClassInstance);
		$this->model->serializer = $serializer;
		$this->model->package = $package;
	}
}