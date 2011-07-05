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

use Foomo\MVC;
use Foomo\Services\ServiceDescription;
use Foomo\Services\Utils;
use Foomo\Config;
use Foomo\Log\Logger;
use Foomo\Services\RPC\Protocol\Call\MethodCall;
use Foomo\Services\RPC\Protocol\Call\MethodArgument;

class Model
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	public $serviceClassName;
	/**
	 * @var stdClass
	 */
	public $serviceClassInstance;
	/**
	 * @var Foomo\Services\RPC\Serializer\SerializerInterface
	 */
	public $serializer;
	/**
	 * @var string
	 */
	public $package;
	/**
	 * @var Foomo\Services\ProxyGenerator\RPC\Report
	 */
	public $proxyGeneratorReport;

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $method
	 * @param array $arguments
	 */
	public function serve($method=null, array $arguments=array())
	{
		ob_start();
		if (!is_null($method)) {
			// missing args ?!
			// get some reflection
			$methodRefl = new \ReflectionMethod($this->serviceClassInstance, $method);
			$parameters = $methodRefl->getParameters();
			if (count($arguments) < count($parameters) && !empty($_POST)) {
				$parameters = array_slice($parameters, count($arguments));
				foreach ($parameters as $parm) {
					/* @var $parm \ReflectionParameter */
					if (isset($_POST[$parm->getName()])) {
						$arguments[] = $_POST[$parm->getName()];
					} else {
						break;
					}
				}
			}
			$methodCall = new MethodCall;
			$methodCall->method = $method;
			$methodCall->arguments = $arguments;
			$reply = \Foomo\Services\RPC\Server::callMethod($this->serviceClassInstance, $methodCall, $this->serializer);
			// unset($reply->id);
			$ret = $this->serializer->serialize($reply);
		} else {
			// incoming data ?!
			if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
				$call = $GLOBALS['HTTP_RAW_POST_DATA'];
			} else if (isset($_POST['call'])) {
				$call = $_POST['call'];
			} else {
				$error = __METHOD__ . ' HTTP_RAW_POST_DATA or $_POST[\'call\'] must be set';
				echo $error;
				trigger_error($error . var_export($_POST, true), E_USER_ERROR);
			}
			// classic fullblown rpc call
			$ret = \Foomo\Services\RPC\Server::run($this->serviceClassInstance, $this->serializer, $call);
		}

		Logger::doneProcessing();

		// check the buffer
		$contents = ob_get_clean();
		if (strlen($contents)) {
			trigger_error('there was output to stdout on your service - that must not happen ! cleaned this from the buffer >' . $contents . '<', E_USER_WARNING);
		}
		// Content header
		header('Content-Type: ' . $this->serializer->getContentMime());
		header('Content-Length: ' . strlen($ret));
		// gzipped output
		ob_start('ob_gzhandler');
		// \Foomo\Utils::appendToPhpErrorLog(PHP_EOL .  '----------------------------------------' . PHP_EOL . $ret);
		echo $ret;
	}

	/**
	 * @return Foomo\Services\ServiceDescription
	 */
	public function serveServiceDescription()
	{
		$descr = new ServiceDescription();
		$descr->documentationUrl = MVC::getCurrentUrlHandler()->renderMethodUrl('default');
		$descr->url = MVC::getCurrentUrlHandler()->baseURL;
		$descr->package = $this->package;
		$descr->name = $this->serviceClassName;
		$descr->type = ($this->serializer instanceof \Foomo\Services\RPC\Serializer\JSON) ? ServiceDescription::TYPE_RPC_JSON : ServiceDescription::TYPE_RPC_AMF;
		$descr->usesRemoteClasses = Utils::getServiceUsesRemoteClasses($descr->name);
		$descr->version = @constant($descr->name . '::VERSION');
		return $descr;
	}
}