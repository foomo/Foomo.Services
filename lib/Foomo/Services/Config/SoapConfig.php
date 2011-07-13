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

namespace Foomo\Services\Config;

/**
 * configuration for a soap client
 */
class SoapClient extends \Foomo\Config\AbstractConfig
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	const NAME = 'Foomo.Services.soapClient';

	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * which soap version SOAP_1_1 | SOAP_1_2
	 *
	 * @var string
	 */
	public $soapVersion;
	/**
	 * URL where to get the wsdl
	 *
	 * @var string
	 */
	public $wsdlUrl;
	/**
	 * URL alternate endpoint than described in the wsdl
	 *
	 * @var string
	 */
	public $endPointUrl;
	/**
	 * need a proxy? Use format http://user:password@hostname:port/
	 *
	 * @var string
	 */
	public $proxyUrl;
	/**
	 * how to map remote objects to local types
	 *
	 *   array('RemoteType' => 'LocalType', ...)
	 *
	 * @var array
	 */
	public $classMap = array('SomeRemoteType' => 'SomeLocalType', 'SomeOtherRemoteType' => 'SomeOtherLocalType');
	/**
	 * user agent
	 *
	 * @var string
	 */
	public $userAgent;
	/**
	 * enables the use of SapClient->__getLast.. methods
	 *
	 * @var boolean
	 */
	public $trace = true;
	/**
	 * exceptions are thrown as SoapFault
	 *
	 * @var boolean
	 */
	public $throwsSoapFault = true;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * @param boolean $createDefault
	 */
	public function __construct($createDefault = false)
	{
		if ($createDefault) {
			if (\Foomo\Config::getMode() == \Foomo\Config::MODE_PRODUCTION) {
				$this->trace = false;
			}
		}
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 *
	 * @param array $value
	 */
	public function setValue($value)
	{
		parent::setValue($value);
		$this->throwsSoapFault = (boolean) $this->throwsSoapFault;
	}

	/**
	 * get a configured soap client
	 *
	 * @return SoapClient
	 */
	public function getSoapClient()
	{
		$options = array();
		// soapVersion
		if (isset($this->soapVersion)) {
			$options['soap_version'] = $this->soapVersion;
		}
		// wsdl
		$wsdl = $this->wsdlUrl;

		// basic auth
		$endpointUrl = parse_url($this->endPointUrl);
		if (!empty($endpointUrl['user']) && !empty($endpointUrl['pass'])) {
			$options['login'] = $endpointUrl['user'];
			$options['password'] = $endpointUrl['pass'];
		}

		// proxy
		if (isset($this->proxyUrl)) {
			$proxyUrl = parse_url($this->proxyUrl);
			if (!empty($proxyUrl['user']) && !empty($proxyUrl['pass'])) {
				$options['proxy_login'] = $proxyUrl['user'];
				$options['proxy_password'] = $proxyUrl['pass'];
			}
			$options['proxy_host'] = $proxyUrl['host'];
			if (!empty($proxyUrl['port'])) {
				$options['proxy_port'] = $proxyUrl['port'];
			}
		}

		// class map
		if (is_array($this->classMap)) {
			$options['classmap'] = $this->classMap;
		}

		// user agent
		if (isset($this->userAgent)) {
			$options['user_agent'] = $this->userAgent;
		}

		// trace
		$options['trace'] = (boolean) $this->trace;
		// exceptions
		$options['exceptions'] = $this->throwsSoapFault;

		return new \SoapClient($wsdl, $options);
	}
}