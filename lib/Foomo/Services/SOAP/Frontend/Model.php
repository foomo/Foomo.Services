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

namespace Foomo\Services\SOAP\Frontend;

use SoapServer;
use Foomo\Services\SOAP\Utils;

class Model {


	/**
	 * @var SoapServer
	 */
	private $soapServer;
	public $className;
	/**
	 * object that will handle the calls
	 *
	 * @var stdClass
	 */
	private $serverObject;
	private $soapVersion;
	private $requireSSL = false;
	private $encoding;
	private $flashWorkaround = false;
	/**
	 * @var ServiceSoapASProxyRendererSettings
	 */
	public  $ASProxyCompilerSettings;

	public function setServiceInstance($inst)
	{
		$this->className = get_class($inst);
		$this->serverObject = $inst;
	}
	public function __get($propName)
	{
		switch($propName) {
			case'soapServer':
				if(!isset($this->soapServer)) {
					$this->initializeSoapServer();
				}
				return $this->soapServer;
			default:
				return null;
		}
	}
	public function useFlashWorkaround($useFlashWorkaround = true)
	{
		$this->flashWorkaround = $useFlashWorkaround;
	}
	public function setSoapVersion($version)
	{
		$this->soapVersion = $version;
	}
	public function setEncoding($encoding)
	{
		$this->encoding = $encoding;
	}
	public function getWsdlCacheFilename()
	{
		return  \Foomo\Config::getCacheDir() . '/serviceSoap-' . str_replace('\\', '.', $this->className) . '.wsdl';
	}
	public function getEndPoint()
	{
		return \Foomo\Utils::getServerUrl($this->requireSSL) . \Foomo\MVC::getCurrentURLHandler()->renderMethodUrl('serve');
		//return \Foomo\Utils::getServerUrl() . $_SERVER['PHP_SELF'];
	}
	private function getWsdl()
	{
		$cacheFileName = $this->getWsdlCacheFilename();
		if(!file_exists($cacheFileName)) {
			$this->compileWsdl();
		}
		return str_replace('#endPointPlaceHolder#', $this->getEndPoint(), file_get_contents($cacheFileName));
	}
	private function getClassMap()
	{
		$wsdlRenderer = new \Foomo\Services\SOAP\WSDLRenderer();
		\Foomo\Services\SOAP\WSDLRenderer::render($this->className, $wsdlRenderer);
		return $wsdlRenderer->getClassMap();
	}
	public function streamWsdl()
	{
		header('Content-Type: text/xml');
		echo $this->getWsdl();
	}
	public function compileWsdl()
	{
		file_put_contents($this->getWsdlCacheFilename(), Utils::generateWSDL(get_class($this->serverObject)));
	}
	private function initializeSoapServer()
	{
		$options = array(
			'classmap' => $this->getClassMap()
		);
		if($this->encoding) {
			$options['encoding'] = $this->encoding;
		}
		if($this->soapVersion) {
			$options['soap_version'] = $this->soapVersion;
		}
		$wsdlCachefilename = $this->getWsdlCacheFilename();
		if(!file_exists($wsdlCachefilename)) {
			$this->getWsdl();
		}
		$this->soapServer = new SoapServer($wsdlCachefilename, $options);
		if(is_object($this->serverObject)) {
			$this->soapServer->setObject($this->serverObject);
		} else {
			$this->soapServer->setClass($this->className);
			if($this->persistance) {
				$this->soapServer->setPersistence($this->persistance);
			}
		}
	}
	public function serve()
	{
		$this->initializeSoapServer();
		if($this->flashWorkaround) {
			ob_start();
		}
		$this->soapServer->handle();
		if($this->flashWorkaround) {
			header('HTTP/1.1 200 OK');
			ob_end_flush();
		}
	}
}