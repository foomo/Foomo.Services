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

namespace Foomo\Services\SOAP;

use Foomo\Services\SOAP;
use Foomo\Services\ServiceDescription;
use Foomo\Services\Reflection;
use Foomo\Services\Renderer\HtmlDocs;
/**
 * utility class, which provides all the extended documentation, interface and proxy generation functionality for the SoapServer
 */
final class Utils {
	/**
	 * explains the service for a machine - the zugspitz scaffold generator is using this wrapper
	 *
	 * @param stdClass $service
	 */
	public static function explainMachine($service)
	{
		if(isset($service->ASProxyCompilerSettings)) {
			$package = $service->ASProxyCompilerSettings->targetPackage;
		} else {
			$package = '';
		}
		$baseUrl = $_SERVER['PHP_SELF'];
		$description = new ServiceDescription();
		$description->type = ServiceDescription::TYPE_SOAP;
		//$description->downloadUrl = $this->ASProxyClientSWCUrl;//$baseUrl . '?getASProxySWC';
		$description->documentationUrl = $baseUrl . '?explain';
		$description->package = $package;
		$description->name = get_class($service);
		$description->usesRemoteClasses = \Foomo\Services\Utils::getServiceUsesRemoteClasses($description->name);
		$description->compilerAvailable = \Foomo\Config::getMode() != \Foomo\Config::MODE_PRODUCTION;
		$versionConstName = get_class($service) . '::VERSION';
		if(defined($versionConstName)) {
			$description->version = constant($versionConstName);
		}
		//$description->recompileUrl = $this->compileProxyUrl;//$baseUrl . '?compile&clearSrcDir';
		echo serialize($description);
		// echo serialize(array('package' => $package, 'class' => $service->className));
	}
	/**
	 * @param string $serviceClassName
	 * 
	 * @return string wsdl
	 */
	public static function generateWSDL($serviceClassName)
	{
		return WSDLRenderer::render($serviceClassName);
	}
	/**
	 * compiles the AS Proxy
	 *
	 * @param boolean $clearSrcDir clear the source directory or not
	 * @return string compiler output or error message
	 */
	public static function compileASProxy($clearSrcDir = false)
	{
		/*
		if(isset($this->serviceSoap->ASProxyCompilerSettings)) {
			try {
				$reader = new Reflection($this->serviceSoap->className, new \Foomo\Services\ProxyGenerator\ActionScript\SOAP($this->serviceSoap->ASProxyCompilerSettings, $clearSrcDir));
				$ret = 'COMPILATION SUCCEEDED : ' . PHP_EOL . PHP_EOL . $reader->render();
			} catch(\Exception $e) {
				$ret = 'COMPILATION FAILED : ' . PHP_EOL . PHP_EOL .  $e->getMessage();
			}
			return $ret;
		} else {
			return 'you have to configure the ASProxyCompiler, if you want to use it';
		}
		*/
	}
	/**
	 * streams the generated proxy to the client or outputs an error message
	 *
	 * @param ServiceSoap $service
	 * @param boolean $asSWC stream the SWC or the tgz
	 */
	public static function getASProxy(ServiceSoap $service, $asSWC = false)
	{
		$downloadName = $service->className;
		if($asSWC) {
			$mime = 'application/octet-stream';
			$downloadName .= '.swc';
		} else {
			$mime = 'application/x-compressed';
			$downloadName .= '.tgz';
		}
		$file = realpath(sys_get_temp_dir()) . '/' . $downloadName ;
		//die($file);
		if(isset($service->ASProxyCompilerSettings) && file_exists($file)) {
			\Foomo\Utils::streamFile($file, $downloadName, $mime, true);
			exit;
		} elseif(!file_exists($file) && isset($service->ASProxyCompilerSettings)) {
			$error = 'compile first and check, if the compilation succeeded ' . $file . ' does not exist and I can not stream it to you';
		} else {
			$error = 'no ASProxyCompilerSettings set ?!';
		}
		trigger_error($error, E_USER_NOTICE);
		echo $error;
	}
}