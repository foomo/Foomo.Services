<?php
namespace Foomo\Services;

use Foomo\Config;

/**
 * describes a service
 *
 */
class ServiceDescription
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	/**
	 * a rpc service JSON for transports
	 */
	const TYPE_RPC_JSON		= 'serviceTypeRpcJson';
	/**
	 * a rpc service using AMF for transports
	 */
	const TYPE_RPC_AMF		= 'serviceTypeRpcAmf';
	/**
	 * a soap service
	 */
	const TYPE_SOAP			= 'serviceTypeSoap';

	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * basically the name of the class that was exposed as a service
	 *
	 * @var string
	 */
	public $name;
	/**
	 * typically the AS package i.e. a java like package notation com.foo.bar. ...
	 *
	 * @var string
	 */
	public $package;
	/**
	 * as what kind of service was it exposed
	 *
	 * @var string
	 */
	public $type;
	/**
	 * version of the server and the generated client
	 *
	 * @var float
	 */
	public $version = 0.0;
	/**
	 * does the client use remote classes
	 *
	 * @var boolean
	 */
	public $usesRemoteClasses = false;
	/**
	 * uri, where to find a html documentation
	 *
	 * @var string
	 */
	public $documentationUrl;
	/**
	 * compile and download - all at once
	 *
	 * @var string
	 */
	public $compileAndDownloadUrl;
	/**
	 * is the compiler available or not to provide the compilation of sources and swcs
	 *
	 * @var boolean
	 */
	public $compilerAvailable;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	public function __construct()
	{
		$this->compilerAvailable = Config::getMode() == Config::MODE_DEVELOPMENT;
	}
}