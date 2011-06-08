<?php

namespace Foomo\Services\RPC\Frontend;

use Foomo\Services\Renderer\RendererInterface;
use Foomo\Config;
use Foomo\Services\ProxyGenerator\ActionScript\RPC;
use Foomo\Flex\Utils;
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

	public function actionGetPHPClient()
	{
		MVC::abort();
		header('Content-Type: text/plain;charset=utf-8;');
		echo \Foomo\Services\ProxyGenerator\PHP\RPC::render(
				$this->model->serviceClassName, new \Foomo\Services\ProxyGenerator\PHP\RPC(MVC::getCurrentUrlHandler()->renderMethodUrl('serve'), 'Foomo\\Services\\RPC\\Serializer\\PHP')
		);
		exit;
	}

	public function actionDefault()
	{
		if (isset($_GET['explainMachine'])) {
			$this->actionExplainMachine();
		}
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
	 * compile an AS client and make it availabe as .tgz source and .swc and output a report
	 *
	 */
	public function actionGenerateASClient()
	{
		if ($this->checkDevAccess()) {
			$this->generateASCientSrc();
		}
	}

	public function actionGenerateJQueryClient()
	{
		MVC::abort();
		$json = new \Foomo\Services\RPC\Serializer\JSON();
		header('Content-Type: text/javascript');
		$generator = new \Foomo\Services\ProxyGenerator\JS\JQuery(MVC::getCurrentUrlHandler()->renderMethodUrl('serve'), $this->model->package);
		$js = \Foomo\Services\ProxyGenerator\JS\JQuery::renderJS($this->model->serviceClassName, MVC::getCurrentUrlHandler()->renderMethodUrl('serve'), $this->model->package);
		if (isset($this->model->srcDir)) {// && is_dir($model->srcDir)) {
			$srcFile = $this->model->srcDir . DIRECTORY_SEPARATOR . $generator->getProxyName() . '.js';
			if (is_writeable($this->model->srcDir)) {
				file_put_contents($srcFile, $js);
			}
		}
		echo $js;
		exit;
	}

	public function actionplainTextDocs()
	{
		MVC::abort();
		header('Content-Type: text/plain;charset=utf-8;');
		echo \Foomo\Services\Renderer\PlainDocs::render($this->model->serviceClassName);
		exit;
	}

	public function actionCompileAndDownloadClient()
	{
		if ($this->checkDevAccess()) {
			$this->model->proxyGeneratorReport = RPC::compileSrc(
				$this->model->serviceClassInstance, 
				$this->model->package, 
				$this->checkSrcDir($this->model->srcDir)
			);
			if ($this->model->proxyGeneratorReport->success) {
				$this->streamSwc();
			}
		}
	}

	/**
	 * get the current swc
	 */
	public function actionGetASClientAsSWC()
	{
		if ($this->checkDevAccess()) {
			$this->generateASCientSrc();
			if (!file_exists($this->model->proxyGeneratorReport->generator->getSWCFilename())) {
				$this->actionCompileAndDownloadClient();
			} else {
				$this->streamSwc();
			}
		}
	}

	/**
	 * generate and pack source
	 */
	public function actionGetASClientAsTgz()
	{
		if ($this->checkDevAccess()) {
			$this->model->proxyGeneratorReport = RPC::packSrc(
							$this->model->serviceClassInstance, $this->model->package, $this->checkSrcDir($this->model->srcDir)
			);
			if ($this->model->proxyGeneratorReport->success) {
				MVC::abort();
				Utils::streamSourcesAsTgz($this->model->proxyGeneratorReport->generator->getTGZFilename());
				exit;
			}
		}
	}

	/**
	 * explain the service to a machine by dumping a serialized ServiceDescription
	 *
	 */
	public function actionExplainMachine()
	{
		// @todo - move this to a better place
		echo serialize($this->model->serveServiceDescription());
		exit;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	/**
	 * 
	 */
	private function generateASCientSrc()
	{
		$this->model->proxyGeneratorReport = RPC::generateSrc(
			$this->model->serviceClassInstance, 
			$this->model->package, 
			$this->checkSrcDir($this->model->srcDir)
		);
	}

	/**
	 * 
	 */
	private function streamSwc()
	{
		MVC::abort();
		Utils::streamSWC($this->model->proxyGeneratorReport->generator->getSWCFilename());
		exit;
	}

	/**
	 *
	 * @param type $asSrcDir
	 * @return type 
	 */
	private function checkSrcDir($asSrcDir=null)
	{
		if (empty($asSrcDir)) {
			$flexConfig = \Foomo\Flex\DomainConfig::getInstance();
			if (!empty($flexConfig->generatedSrcDir)) {
				$asSrcDir = $flexConfig->generatedSrcDir;
			} else {
				$asSrcDir = tempnam(Config::getTempDir(), __CLASS__ . '-srcDir-');
				unlink($asSrcDir);
				mkdir($asSrcDir);
			}
		}
		return $asSrcDir;
	}

	/**
	 * check if a compiler related feature is available or not
	 *
	 * @return boolean
	 */
	private function checkDevAccess()
	{
		$ret = (Config::getMode() != Config::MODE_PRODUCTION);
		if (!$ret) {
			$this->model->proxyGeneratorReport = new \Foomo\Services\ProxyGenerator\RPC\Report();
			$this->model->proxyGeneratorReport->success = false;
			$this->model->proxyGeneratorReport->message = \Foomo\Services\ProxyGenerator\RPC\Report::MSG_COMPILER_NOT_AVAILABLE;
		}
		return $ret;
	}

}