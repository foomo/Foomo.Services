<?php

namespace Foomo\Services\SOAP\Frontend;

use SoapServer;

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
		return  \Foomo\Config::getCacheDir() . '/serviceSoap-' . $this->className;
	}
	public function getEndPoint()
	{
		return \Foomo\Utils::getServerUrl() . $_SERVER['PHP_SELF'];
	}
	private function getWsdl()
	{
		$cacheFileName = $this->getWsdlCacheFilename();
		if(!file_exists($cacheFileName)) {
			Util::compileServer($this, true);
		}
		return str_replace('#endPointPlaceHolder#', $this->getEndPoint(), file_get_contents($cacheFileName));
	}
	private function getClassMap()
	{
		$wsdlGenerator = new ServiceSoapWsdlRenderer($this->getEndPoint());
		$serviceReader = new ServiceReader($this->className, $wsdlGenerator);
		$serviceReader->render();
		return $wsdlGenerator->getClassMap();
	}
	public function streamWsdl()
	{
		header('Content-Type: text/xml');
		echo $this->getWsdl();
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
	private function internalServeClass()
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
	/**
	 * serve a class as a service including documentation, AS proxy generation etc.
	 *
	 * @param string $className name of the class that shall be run as a service
	 * @param boolean $persistent whether or not the served object should be persistant
	 * @param string $asPackage a package name like org.foomo.some.package
	 * @param string $asSrcDir where to write the sctionscript classes to typically /tmp
	 * @param boolean $useFlashWorkaround send a 200 http header with a fault instead of a 500
	 */
	public static function aaaserveClass($className, $persistent = false, $asPackage = null, $asSrcDir = null, $useFlashWorkaround = true, $serviceInstance = null)
	{
		$server = self::getService($className);
		$server->useFlashWorkaround($useFlashWorkaround);
		if($serviceInstance) {
			$server->setServerObject($serviceInstance);
		} else {
			$server->setPersistance($persistent);
		}
		if($asPackage) {
			if(empty($asSrcDir)) {
				$asSrcDir = tempnam(sys_get_temp_dir(), __CLASS__ . '-srcDir-');
				unlink($asSrcDir);
				mkdir($asSrcDir);
			}
			$compilerSettings = new ServiceSoapASProxyRendererSettings();
			$compilerSettings->ASSrcDir = $asSrcDir;
			$compilerSettings->targetPackage = $asPackage;
			$server->setASProxyCompilerSettings($compilerSettings);
		}
		if(\Foomo\Config::getMode() == \Foomo\Config::MODE_DEVELOPMENT) {
			ini_set('soap.wsdl_cache', '0');
			$fp = fopen(ini_get('error_log'), 'a+');
			fwrite($fp, PHP_EOL . '----------------- ' . $className . ' -------------------' . PHP_EOL .  file_get_contents('php://input') . PHP_EOL);
			fclose($fp);
			try {
				$server->serve();
			} catch (Exception $e) {
				trigger_error('an error occured see incoming request above ""' . $e->getMessage() . '"', E_USER_ERROR);
			}
		} else {
			$server->serve();
		}
	}
}