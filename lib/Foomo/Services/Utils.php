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

namespace Foomo\Services;

/**
 * provides a directory of available services
 */
class Utils
{
	//---------------------------------------------------------------------------------------------
	// ~ Public static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * get services
	 *
	 * @return array of services array('/path/to/service', 'path/to/next/service', ...)
	 */
	public static function getAllServices()
	{
		$allServices = array();
		foreach(\Foomo\Modules\Manager::getEnabledModules() as $enabledModuleName) {
			$services = self::getServices($enabledModuleName);
			$allServices[$enabledModuleName] = $services;
		}
		return $allServices;
	}

	/**
	 * get all local service descriptions
	 *
	 * @return array
	 */
	public static function getAllLocalServiceDescriptions()
	{
		$localServices = self::getAllServices();
		$ret = array();
		foreach($localServices as $domain => $serviceUris) {
			$ret[$domain] = array();
			foreach($serviceUris as $serviceUri) {
				if(($serviceDescription = self::getServiceDescription(\Foomo\Utils::getServerUrl(false, true) . $serviceUri))) {
					$ret[$domain][] = $serviceDescription;
				}
			}
		}
		return $ret;
	}

	/**
	 * load a service description
	 *
	 * @param string $url
	 * @return Foomo\Services\ServiceDescription
	 */
	public static function getServiceDescription($url)
	{
		$serviceUrl = $url .'?explainMachine';
		if(!($serialized = @file_get_contents($serviceUrl))) {
			trigger_error('could not read from ' . $serviceUrl, E_USER_WARNING);
		}
		if(($serviceDescription = @unserialize($serialized)) && ($serviceDescription instanceof ServiceDescription)) {
			 return $serviceDescription;
		} else {
			trigger_error('could not unserialize service description from ' . $serviceUrl);
			return null;
		}
	}

	/**
	 * map a host to a service url
	 *
	 * @todo reimplement
	 * @deprecated needs reimplementation
	 * @param string $host the blank host
	 */
	public static function getServiceToolsUrl($host)
	{
		$siteUrls = self::getRemoteServiceSites();
		$scheme = 'http://';
		foreach($siteUrls as $siteUrl) {
			$siteHost = parse_url($siteUrl, PHP_URL_HOST);
			if($siteHost == $host) {
				$scheme = parse_url($siteUrl, PHP_URL_SCHEME) .'://';
				break;
			}
		}
		return $scheme . $host . '/r/modules/services/index.php';
	}

	/**
	 * if you want to call stuff from a remote host, you might need crecentials
	 *
	 * @param string $host somehost.com
	 *
	 * @return string sth. like http://user:password@somehost.com
	 */
	public static function getRemoteServiceUrlWithCredentials($host)
	{
		if($host == $_SERVER['HTTP_HOST']) {
			return \Foomo\Utils::getServerUrl(false, true);
		}
		foreach(self::getRemoteServiceSites() as $remoteSite) {
			if(parse_url($remoteSite, PHP_URL_HOST) == $host) {
				return $remoteSite;
			}
		}
	}

	/**
	 * if you want to call stuff from a remote host, you might need crecentials
	 *
	 * @param string $host somehost.com
	 *
	 * @return string sth. like http://user:password@somehost.com
	 */
	public static function getRemoteServiceUrl($host)
	{
		if($host == $_SERVER['HTTP_HOST']) {
			return \Foomo\Utils::getServerUrl();
		}
		foreach(self::getRemoteServiceSites() as $remoteSite) {
			if(parse_url($remoteSite, PHP_URL_HOST) == $host) {
				return parse_url($remoteSite, PHP_URL_SCHEME) . '://' . parse_url($remoteSite, PHP_URL_HOST);
			}
		}
	}

	/**
	 * check all remote service sites defined in the comma separated list \Foomo\EXTERNAL_SERVICE_SITES and returns the in an array
	 *
	 * @todo reimplement
	 * @deprecated needs reimplementation
	 * @return array array('sitea.com' => array('site' => arrray(), 'foomo' => array), 'siteb.com' ) => array())
	 */
	public static function getAllRemoteServiceDescriptions()
	{
		$ret = array();
		$siteUrls = self::getRemoteServiceSites();
		if(count($siteUrls) > 0) {
			foreach ($siteUrls as $siteUrl) {
				$serviceUrl = $siteUrl . '/r/modules/services/index.php?class=serviceTools&action=getAvailableServices';
				$host = parse_url($siteUrl, PHP_URL_HOST);
				if(!($serialized = @file_get_contents($serviceUrl))) {
					trigger_error('could not read from ' . $serviceUrl, E_USER_WARNING);
				}
				if(($siteServices = unserialize($serialized)) && is_array($siteServices)) {
					$ret[$host] = array('foomo' => array(), 'site' => array());
					foreach(array('foomo', 'site') as $domain) {
						foreach($siteServices[$domain] as $serviceUri) {
							$serviceUrl = $siteUrl . $serviceUri;
							if(($serviceDescription = self::getServiceDescription($serviceUrl))) {
								$ret[$host][$domain][] = $serviceDescription;
							}
						}
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * does the given service use remote client classes
	 *
	 * @param string $serviceName name of the service
	 *
	 * @return boolean
	 */
	public static function getServiceUsesRemoteClasses($serviceName)
	{
		$reader = new Reflection($serviceName);
		foreach($reader->getTypes() as $serviceObjectType) {
			/* @var $serviceObjectType Reflection\ServiceObjectType */
			foreach($serviceObjectType->annotations as $annotation) {
				/* @var $annotation Reflection\RemoteClass */
				if(!empty($annotation->name)) {
					 return true;
				}
			}
		}
		return false;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private static methods
	//---------------------------------------------------------------------------------------------

	/**
	 * get the remote service sites in an array
	 *
	 * @todo reimplement
	 * @deprecated needs reimplementation
	 * @return array
	 */
	private static function getRemoteServiceSites()
	{
		static $ret;
		if(!$ret) {
		 	$ret = array();
			if(defined('\Foomo\EXTERNAL_SERVICE_SITES')) {
				$siteUrls = explode(', ', \Foomo\EXTERNAL_SERVICE_SITES);
				foreach($siteUrls as $siteUrl) {
					$ret[] = trim($siteUrl);
				}
			}
		}
		return $ret;
	}

	/**
	 * scans for module services
	 *
	 * @param string $moduleName name of the module
	 * @param string $path
	 * @return array
	 */
	private static function getServices($moduleName, $path = null)
	{
		if(is_null($path)) {
			$path = \Foomo\CORE_CONFIG_DIR_MODULES . DIRECTORY_SEPARATOR . $moduleName . DIRECTORY_SEPARATOR . 'htdocs' . DIRECTORY_SEPARATOR . 'services';
		}
		$results = array();
		if(is_dir($path)) {
			$directoryIterator = new \DirectoryIterator($path);
			while($directoryIterator->valid()) {
				$current = $directoryIterator->current();
				if(!$current->isDot() && $current->isFile()) {
					$suffix = substr($current->getFilename(), strlen($current->getFilename())-4);
					if($suffix === '.php') {
						$serviceFilename = $path . '/' . $current->getFilename();
						$results[] = \Foomo\ROOT_HTTP . '/modules/' . $moduleName . '/services/' . substr($serviceFilename, strlen($path)+1);
					}
				} else {
					$dot = substr($current->getFilename(),0,1);
					if($dot != '.' && $current->isDir() && !$current->isDot() &&$current->getFilename() != '') {
						$results = array_merge($results, self::getServices($moduleName, $path . '/' . $current->getFilename()));
					}
				}
				$directoryIterator->next();
			}
			sort($results);
		}
		return $results;
	}
}