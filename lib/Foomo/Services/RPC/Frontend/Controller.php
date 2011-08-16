<?php

/*
 * This file is part of the foomo Opensource Framework.
 *
 * The foomo Opensource Framework is free software: you can redistribute it
 * and/or modify it under the terms of the GNU Lesser General Public License as
 * published  by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * The foomo Opensource Framework is distributed in the hope that it will
 * be useful, but WITHOUT ANY WARRANTY; without even the implied warranty
 * of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License along with
 * the foomo Opensource Framework. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Foomo\Services\RPC\Frontend;

/**
 * @link www.foomo.org
 * @license www.gnu.org/licenses/lgpl.txt
 * @author jan <jan@bestbytes.de>
 */
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
		$this->auth($this->model->authDomainDev);
		if (isset($_GET['explainMachine'])) {
			$this->actionExplainMachine();
		}
	}

	/**
	 *
	 */
	public function actionGetPHPClient()
	{
		$this->auth($this->model->authDomainDev);
		\Foomo\MVC::abort();
		header('Content-Type: text/plain;charset=utf-8;');
		echo \Foomo\Services\ProxyGenerator\PHP\RPC::render(
				$this->model->serviceClassName,
				new \Foomo\Services\ProxyGenerator\PHP\RPC(
					\Foomo\MVC::getCurrentUrlHandler()->renderMethodUrl('serve'),
					'Foomo\\Services\\RPC\\Serializer\\PHP'
				)
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
		$this->auth($this->model->authDomain);
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
		$this->auth($this->model->authDomain);
	}

	/**
	 *
	 */
	public function actionGenerateJQueryClient()
	{
		$this->auth($this->model->authDomainDev);
		\Foomo\MVC::abort();
		$json = new \Foomo\Services\RPC\Serializer\JSON();
		header('Content-Type: text/javascript');
		$generator = new \Foomo\Services\ProxyGenerator\JS\JQuery(\Foomo\MVC::getCurrentUrlHandler()->renderMethodUrl('serve'), $this->model->package);
		$js = \Foomo\Services\ProxyGenerator\JS\JQuery::renderJS($this->model->serviceClassName, \Foomo\MVC::getCurrentUrlHandler()->renderMethodUrl('serve'), $this->model->package);
		// @todo add version number to service name
		$filename = \Foomo\Services\Module::getHtdocsVarDir('js') . DIRECTORY_SEPARATOR . str_replace('.', '', $generator->getProxyName()) . '.js';
		// @todo: use resource to delete
		// @todo: better endpoint integration
		// @todo: minify
		@unlink($filename);
		\Foomo\Modules\Resource\Fs::getAbsoluteResource(\Foomo\Modules\Resource\Fs::TYPE_FILE, $filename)->tryCreate();
		file_put_contents($filename, $js);
		$js = '// ' . \Foomo\Utils::getServerUrl() . \Foomo\Services\Module::getHtdocsVarPath() . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $generator->getProxyName() . '.js' . PHP_EOL . $js;
		echo $js;
		exit;
	}

	/**
	 *
	 */
	public function actionPlainTextDocs()
	{
		$this->auth($this->model->authDomainDev);
		\Foomo\MVC::abort();
		header('Content-Type: text/plain;charset=utf-8;');
		echo \Foomo\Services\Renderer\PlainDocs::render($this->model->serviceClassName);
		exit;
	}

	/**
	 * explain the service to a machine by dumping a serialized ServiceDescription
	 */
	public function actionExplainMachine()
	{
		$this->auth($this->model->authDomainDev);
		// @todo - move this to a better place
		echo serialize($this->model->serveServiceDescription());
		exit;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $domain
	 */
	private function auth($domain)
	{
		if (!is_null($domain)) \Foomo\BasicAuth::auth($this->model->serviceClassName, $domain);
	}
}