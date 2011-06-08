<?php

namespace Foomo\Services\Frontend;

/**
 * services toolbox
 */
class Controller {
	/**
	 * @var Foomo\Services\Frontend\Model
	 */
	public $model;
	public function actionDefault()
	{
	}
	public function actionGetAvailableServices()
	{
		ob_clean();
		echo serialize(\Foomo\Services\Utils::getAllServices());
		exit;
	}
}