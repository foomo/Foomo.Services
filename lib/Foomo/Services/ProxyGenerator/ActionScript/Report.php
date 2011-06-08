<?php


namespace Foomo\Services\ProxyGenerator\ActionScript;

class Report extends \Foomo\Services\ProxyGenerator\Report {
	const MSG_COMPILER_NOT_AVAILABLE = 'only available in development mode';
	public $swcFilename;
	public $tgzFilename;
	/**
	 * @var Foomo\Services\ProxyGenerator\ActionScript\AbstractGenerator
	 */
	public $generator;
}