<?php

namespace Foomo\Services\ProxyGenerator\ActionScript;

use Foomo\Services\RPC as RPCService;
use Foomo\Services\Reflection\ServiceObjectType;
use Foomo\Services\Reader;
use Foomo\Services\Reflection;
use Foomo\Flash\ActionScript\PHPUtils;
use Foomo\Flash\ActionScript\ViewHelper;

/**
 * renders AS RPC Proxy clients rocking Zugspitze
 */
class RPC extends AbstractGenerator
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var string[]
	 */
	public $packageFolders = array('calls', 'operations', 'events', 'commands');
	/**
	 * @var ServiceObjectType[]
	 */
	public $throwsTypes = array();


	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @param Foomo\Services\Reflection\ServiceOperation $op
	 */
	public function renderOperation(\Foomo\Services\Reflection\ServiceOperation $op)
	{
		parent::renderOperation($op);

		// Method calls
		$view = $this->getView('MethodCallClass');
		$this->classFiles['calls' . DIRECTORY_SEPARATOR . ViewHelper::toClassName($op->name, 'Call')] = $view->render();

		// Method calls events
		$view = $this->getView('MethodCallEventClass');
		$this->classFiles['events' . DIRECTORY_SEPARATOR . ViewHelper::toClassName($op->name, 'CallEvent')] = $view->render();

		// Method calls exceptions
		if (count($this->currentOperation->throwsTypes) > 0) {
			foreach ($this->currentOperation->throwsTypes as $throwType) {
				if (!isset($this->throwsTypes[$throwType->type])) $this->throwsTypes[$throwType->type] = $throwType;
			}
		}

		// Operations
		$view = $this->getView('OperationClass');
		$this->classFiles['operations' . DIRECTORY_SEPARATOR . ViewHelper::toClassName($op->name, 'Operation')] = $view->render();

		// Operations events
		$view = $this->getView('OperationEventClass');
		$this->classFiles['events' . DIRECTORY_SEPARATOR . ViewHelper::toClassName($op->name, 'OperationEvent')] = $view->render();

		// Commands
		$view = $this->getView('AbstractCommandClass');
		$this->classFiles['commands' . DIRECTORY_SEPARATOR . ViewHelper::toClassName($op->name, 'Command', 'Abstract')] = $view->render();
	}

	/**
	 * @return string a report of what was done
	 */
	public function output()
	{
		// rendering the proxy class
		$view = $this->getView('ProxyClass');
		$this->classFiles[PHPUtils::getASType($this->serviceName) . 'Proxy'] = $view->render();

		// render all the vos
		foreach ($this->complexTypes as $complexType) $this->renderVOClass($complexType);

		// render all exception events
		foreach ($this->throwsTypes as $throwType) {
			$this->currentDataClass = $this->complexTypes[$throwType->type];
			$view = $this->getView('ExceptionEventClass');
			$this->classFiles['events' . DIRECTORY_SEPARATOR . PHPUtils::getASType($this->currentDataClass->type) . 'Event'] = $view->render();
		}

		return parent::output();
	}

	/**
	 * @return string
	 */
	public function getSWCFilename()
	{
		return \Foomo\Services\Module::getTempDir() . DIRECTORY_SEPARATOR . str_replace('\\', '', $this->serviceName) . '.swc';
	}

	/**
	 * @return string
	 */
	public function getTGZFilename()
	{
		return \Foomo\Services\Module::getTempDir() . DIRECTORY_SEPARATOR . str_replace('\\', '', $this->serviceName) . '.tgz';
	}

	//---------------------------------------------------------------------------------------------
	// ~ Protected methods
	//---------------------------------------------------------------------------------------------

	/**
	 * get a (specific) template
	 *
	 * @param string $template base name of the template
	 * @return Foomo\View
	 */
	protected function getView($template)
	{
		return \Foomo\Services\Module::getView($this, $template, $this);
	}


	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	/**
	 * render a (complex) type - and write it into $this->classFiles
	 *
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 */
	private function renderVOClass(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		// that is for the views
		$this->currentDataClass = $type;

		// check in the annotations if the class is shared by other services or has a remote class
		$isCommonClass = false;
		$hasRemoteClass = false;
		foreach ($type->annotations as $annotation) {
			if ($annotation instanceOf RemoteClass) {
				/* @var $annotation RemoteClass */
				if (!empty($annotation->package)) {
					$commonPath = str_replace('.', DIRECTORY_SEPARATOR, $annotation->package);
					$isCommonClass = true;
				}

				if (!empty($annotation->name)) {
					trigger_error('rendering a base class for remote class ' . $annotation->name, E_USER_NOTICE);
					$remoteBaseClassName = basename(str_replace('.', DIRECTORY_SEPARATOR, $annotation->name));
					$hasRemoteClass = true;
				}
				break;
			}
		}

		$view = $this->getView('VOClass');
		$content = $view->render();

		if ($hasRemoteClass) {
			$classFileName = $this->getVOClassName($type);
		} else {
			$classFileName = PHPUtils::getASType($type->type);
		}

		if ($isCommonClass) {
			$this->commonClassFiles[$commonPath . DIRECTORY_SEPARATOR . $classFileName] = $content;
		} else {
			$this->commonClassFiles[$type->getRemotePackagePath() . DIRECTORY_SEPARATOR . PHPUtils::getASType($type->type)] = $content;
		}
	}

}