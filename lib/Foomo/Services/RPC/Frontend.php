<?php


namespace Foomo\Services\RPC;

use Foomo\Services\RPC\Serializer\SerializerInterface;
use Foomo\MVC\AbstractApp;
/**
 * 
 */
class Frontend extends AbstractApp {
	/**
	 * @var Foomo\Services\RPC\Frontend\Model
	 */
	public $model;
	public function __construct($serviceClassInstance, SerializerInterface $serializer, $asPackage, $asSrcDir)
	{
		parent::__construct(__CLASS__);
		$this->model->serviceClassInstance = $serviceClassInstance;
		$this->model->serviceClassName = get_class($serviceClassInstance);
		$this->model->serializer = $serializer;
		$this->model->package = $asPackage;
		$this->model->srcDir = $asSrcDir;
	}
}