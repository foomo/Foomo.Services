<?php

namespace Foomo\Services\ProxyGenerator\ActionScript;

class Report extends \Foomo\Services\ProxyGenerator\Report
{
	//---------------------------------------------------------------------------------------------
	// ~ Constants
	//---------------------------------------------------------------------------------------------

	const MSG_COMPILER_NOT_AVAILABLE = 'only available in development mode';

	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	public $swcFilename;
	/**
	 * @var string
	 */
	public $tgzFilename;
	/**
	 * @var Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator
	 */
	public $generator;
}