<?php

namespace Foomo\Services\RPC\Frontend;

use Foomo\Services\Renderer\RendererInterface;
use Foomo\Config;
use Foomo\MVC;

class Controller
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var Foomo\Services\RPC\Frontend\Model
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
			$this->actionExplainMachine();
		}
	}

	/**
	 *
	 */
	public function actionGetPHPClient()
	{
		MVC::abort();
		header('Content-Type: text/plain;charset=utf-8;');
		echo \Foomo\Services\ProxyGenerator\PHP\RPC::render(
				$this->model->serviceClassName, new \Foomo\Services\ProxyGenerator\PHP\RPC(MVC::getCurrentUrlHandler()->renderMethodUrl('serve'), 'Foomo\\Services\\RPC\\Serializer\\PHP')
		);
		exit;
	}

	/**
	 * serve
	 *
	 * @param string $method
	 */
	public function actionServe($method = null)
	{
		$args = array();
		if (!empty($method)) {
			$foundMethod = false;
			foreach (\Foomo\MVC\URLHandler::$rawCurrentCallData as $rawCallEntry) {
				if (!$foundMethod) {
					if ($rawCallEntry == $method) {
						$foundMethod = true;
					}
					continue;
				} else {
					$args[] = $rawCallEntry;
				}
			}
		}
		$this->model->serve($method, $args);
		exit;
	}

	/**
	 * @todo http GET support and let the browser cache things too ...
	 *
	 * @param type $method
	 * @param type $parameters
	 */
	public function actionGet($method, $parameters)
	{

	}

	/**
	 *
	 */
	public function actionGenerateJQueryClient()
	{
		MVC::abort();
		$json = new \Foomo\Services\RPC\Serializer\JSON();
		header('Content-Type: text/javascript');
		$generator = new \Foomo\Services\ProxyGenerator\JS\JQuery(MVC::getCurrentUrlHandler()->renderMethodUrl('serve'), $this->model->package);
		$js = \Foomo\Services\ProxyGenerator\JS\JQuery::renderJS($this->model->serviceClassName, MVC::getCurrentUrlHandler()->renderMethodUrl('serve'), $this->model->package);
		// @todo add version number to service name
		$filename = \Foomo\Services\Module::getHtdocsVarDir('js') . DIRECTORY_SEPARATOR . str_replace('.', '', $generator->getProxyName()) . '.js';
		// @todo: use resource to delete
		// @todo: better endpoint integration
		// @todo: minify
		@unlink($filename);
		\Foomo\Modules\Resource\Fs::getAbsoluteResource(\Foomo\Modules\Resource\Fs::TYPE_FILE, $filename)->tryCreate();
		file_put_contents($filename, $js);
		$js = '// http://' . $_SERVER['HTTP_HOST'] . \Foomo\Services\Module::getHtdocsVarUrl() . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $generator->getProxyName() . '.js' . PHP_EOL . $js;
		echo $js;
		exit;
	}

	/**
	 *
	 */
	public function actionPlainTextDocs()
	{
		MVC::abort();
		header('Content-Type: text/plain;charset=utf-8;');
		echo \Foomo\Services\Renderer\PlainDocs::render($this->model->serviceClassName);
		exit;
	}

	/**
	 * explain the service to a machine by dumping a serialized ServiceDescription
	 */
	public function actionExplainMachine()
	{
		// @todo - move this to a better place
		echo serialize($this->model->serveServiceDescription());
		exit;
	}
}