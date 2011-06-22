<?php

namespace Foomo\Services\ProxyGenerator\ActionScript;

use Foomo\Services\Reflection\ServiceObjectType;
use Foomo\Config;
use Exception;
use Foomo\CliCall;
use Foomo\Flex\Utils as FlexUtils;
use Foomo\Services\Renderer\AbstractRenderer;
use Foomo\Services\Reflection\RemoteClass;

/**
 * base class to render actionscript service proxies
 *
 */
abstract class AbstractGenerator extends AbstractRenderer
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

	/**
	 * @var string
	 */
	public $targetPackage;
	/**
	 * shall the source directory be cleared before class generation
	 *
	 * @var boolean
	 */
	protected $clearSrcDir;
	/**
	 * hash filename => filecontents, contains the rendered class, before they will be written to the file system
	 *
	 * @var array
	 */
	protected $classFiles;
	/**
	 * class files namely ASClasses, which need to go in extra packages aka the sharedVo√î√∏Œ©s
	 *
	 * hash filename => filecontents
	 *
	 * @var array
	 */
	protected $commonClassFiles = array();
	/**
	 * the class to be rendered as a data class in the DataClass Template
	 *
	 * @var ServiceObjectType
	 */
	public $currentDataClass;
	/**
	 * currently rendered operation
	 *
	 * @var ServiceOperation
	 */
	public $currentOperation;
	/**
	 * operations
	 *
	 * @var ServiceOperation[]
	 */
	public $operations;
	/**
	 * complex types
	 *
	 * @var ServiceObjectType[]
	 */
	public $complexTypes;
	/**
	 * throw types
	 *
	 * @var ServiceObjectType[]
	 */
	public $throwsTypes = array();
	/**
	 * name of the service
	 *
	 * @var string
	 */
	public $serviceName;
	/**
	 * java style package com.foo.bar
	 *
	 * @var string
	 */
	public $myPackage;
	/**
	 * where to write the sources to - a folder in the filesystem
	 *
	 * @var string
	 */
	public $targetSrcDir;
	/**
	 * name of the service proxy class, that will be generated
	 *
	 * @var string
	 */
	public $proxyClassName;
	/**
	 * name of the service proxy base class
	 *
	 * @var string
	 */
	public $proxyBaseClassName;
	/**
	 * folder list, of all the folders, to represent the package structure
	 *
	 * @var array
	 */
	public $packageFolders;
	/**
	 * where are the specific templates
	 *
	 * @var string
	 */
	protected $templateFolder;

	//---------------------------------------------------------------------------------------------
	// ~ Abstract methods implementation
	//---------------------------------------------------------------------------------------------

	public function init($serviceName)
	{
		$this->serviceName = $serviceName;
		$nameParts = explode('\\', $this->serviceName);
		$this->proxyClassName = $this->serviceName . 'Proxy';
		$this->proxyBaseClassName = $this->serviceName . 'ProxyBase';
		if ($this->myPackage != '') {
			$this->myPackage = $this->targetPackage . '.' . strtolower(substr(Utils::getASType($this->serviceName), 0, 1)) . substr(Utils::getASType($this->serviceName), 1);
		} else {
			$serviceType = new ServiceObjectType($this->serviceName);
			// you can only do that in a language as dirty as php ;)
			if (!empty($this->targetPackage)) {
				$packageToUse = $this->targetPackage;
				if ($serviceType->namespace == '\\') {
					$packageToUse .= '.' . lcfirst($serviceType->type);
				}
				$this->myPackage = explode('.', $packageToUse);
			} else {
				$this->myPackage = array();
				foreach (explode('\\', $this->serviceName) as $package) {
					if (!empty($package)) $this->myPackage[] = lcfirst($package);
				}
			}

			$this->myPackage = implode('.', $this->myPackage);
		}
		$this->operations = array();
		$this->classFiles = array();
		$this->complexTypes = array();
	}

	/**
	 * render the service type itself
	 *
	 * @param ServiceObjectType $type
	 */
	public function renderServiceType(ServiceObjectType $type)
	{

	}

	/**
	 * @param ServiceObjectType $type
	 */
	public function renderType(ServiceObjectType $type)
	{
		if (!Utils::isASStandardType($type->type)) $this->complexTypes[$type->type] = $type;
	}

	/**
	 * @param Foomo\Services\Reflection\ServiceOperation $op
	 */
	public function renderOperation(\Foomo\Services\Reflection\ServiceOperation $op)
	{
		$this->currentOperation = $op;
		array_push($this->operations, $op);

		// Method calls
		$view = $this->getView('MethodCallClass');
		$this->classFiles['calls' . DIRECTORY_SEPARATOR . $this->operationToMethodCallName($op->name)] = $view->render();

		// Method calls events
		$view = $this->getView('MethodCallEventClass');
		$this->classFiles['events' . DIRECTORY_SEPARATOR . $this->operationToMethodCallEventName($op->name)] = $view->render();

		// Method calls exceptions
		if (count($this->currentOperation->throwsTypes) > 0) {
			foreach ($this->currentOperation->throwsTypes as $throwType) {
				if (!isset($this->throwsTypes[$throwType->type])) $this->throwsTypes[$throwType->type] = $throwType;
			}
		}

		// Operations
		$view = $this->getView('OperationClass');
		$this->classFiles['operations' . DIRECTORY_SEPARATOR . $this->operationToOperationName($op->name)] = $view->render();

		// Operations events
		$view = $this->getView('OperationEventClass');
		$this->classFiles['events' . DIRECTORY_SEPARATOR . $this->operationToOperationEventName($op->name)] = $view->render();

		// Commands
		$view = $this->getView('AbstractCommandClass');
		$this->classFiles['commands' . DIRECTORY_SEPARATOR . $this->operationToAbstractCommandName($op->name)] = $view->render();
	}

	/**
	 * @return string a report of what was done
	 */
	public function output()
	{
		// rendering the proxy class
		$view = $this->getView('ProxyClass');
		$this->classFiles[Utils::getASType($this->serviceName) . 'Proxy'] = $view->render();

		// render all the vos
		foreach ($this->complexTypes as $complexType) $this->renderVOClass($complexType);

		// render all exception events
		foreach ($this->throwsTypes as $throwType) {
			$this->currentDataClass = $this->complexTypes[$throwType->type];
			$view = $this->getView('ExceptionEventClass');
			$this->classFiles['events' . DIRECTORY_SEPARATOR . $this->toEventName(Utils::getASType($this->currentDataClass->type))] = $view->render();
		}


		/*
		// rendering the proxy base class
		$view = $this->getView('ProxyBaseClass');
		$this->classFiles['model' . DIRECTORY_SEPARATOR . Utils::getASType($this->serviceName) . 'ProxyBase'] = $view->render();

		// rendering the proxy class
		$view = $this->getView('ProxyClass');
		$this->classFiles['model' . DIRECTORY_SEPARATOR . Utils::getASType($this->serviceName) . 'Proxy'] = $view->render();

		// AS3 class alias registry
		$view = $this->getView('ClassAliasRegistry');
		$this->classFiles['model' . DIRECTORY_SEPARATOR . 'ClassAliasRegistry'] = $view->render();
		*/

		return $this->export();
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * derive a constant name for an exception event
	 *
	 * @param string $type exception type
	 *
	 * @return name of the exception
	 */
	public function exceptionTypeToEventConstName($type)
	{
		$parts = explode('\\', $type);
		$ret = 'EXCEPTION';
		foreach ($parts as $part) {
			if (!empty($part)) {
				$ret .= '_' . strtoupper($part);
			}
		}
		return $ret;
	}

	/**
	 * derive an exception prop name for a command event class from an exception type
	 *
	 * @param string $type exception type
	 *
	 * @return string name of the prop
	 */
	public function exceptionTypeToEventPropName($type)
	{
		return 'exception' . str_replace('\\', '', $type);
	}

	/**
	 * for remote class alias registration
	 *
	 * @param ServiceObjectType $type
	 *
	 * @return string
	 */
	public function getVORemoteAliasName(ServiceObjectType $type)
	{
		return str_replace('\\', '.', $type->type);
	}

	/**
	 * get the class name for a value object
	 *
	 * @param ServiceObjectType $type
	 *
	 * @return string
	 */
	public function getVOClassName(ServiceObjectType $type)
	{
		// are therer annotations that direct to another class name, than the php one
		foreach ($type->annotations as $annotation) {
			if ($annotation instanceOf RemoteClass) {
				if (!empty($annotation->name)) {
					$remoteBaseClassName = basename(str_replace('.', DIRECTORY_SEPARATOR, $annotation->name));
					if ($type->type == $remoteBaseClassName) {
						return $remoteBaseClassName . 'Base';
					} else {
						return $type->type;
					}
				}
			}
		}
		// is there a namspace in the class name
		if (strpos($type->type, '\\') !== false) {
			$parts = explode('\\', $type->type);
			return $parts[count($parts) - 1];
		} else {
			return $type->type;
		}
	}

	public function packTgz()
	{
		$path = $this->getPath();
		$ret = 'PACKING SOURCES' . PHP_EOL;
		$ret .= 'removing old archive ' . $this->getTGZFileName() . PHP_EOL;
		@unlink($this->getTgzFileName());
		$tarFiles = array_values(array_diff(\scandir($this->targetSrcDir), array('.', '..')));
		$ret .= PHP_EOL . 'packing sources with tar - ';
		$tarCall = new CliCall(
						'tar',
						array_merge(
							array(
								'--directory',
								$this->targetSrcDir,
								'-czvf',
								$this->getTgzFileName(),
							),
							$tarFiles
						)
		);
		$tarCall->execute();
		$ret .= $tarCall->report;
		return $ret;
	}

	/**
	 * @param string $configId Flex config id to use
	 * @return string
	 */
	public function compile($configId)
	{
		$ret = 'COMPILING' . PHP_EOL;
		$ret .= 'removing old swc ' . $this->getSWCFileName() . PHP_EOL;
		@unlink($this->getSWCFileName());
		$ret .= 'calling Adobe Compc (Flex Component Compiler) see what the compiler reported - ';
		$compileReport = '';
		$externalLibs = array();
		$sourcePaths = array($this->targetSrcDir);

		# get flex config
		$flexConfigEntry = \Foomo\Flex\DomainConfig::getInstance()->getEntry($configId);
		$sourcePaths = array_unique(array_merge($sourcePaths, $flexConfigEntry->sourcePaths));
		$externalLibs = array_unique(array_merge($externalLibs, $flexConfigEntry->externalLibs));

		# add zugspitze swcs
		$sources = \Foomo\Zugspitze\Vendor::getSources();
		$zsExternals = array(
			'org.foomo.zugspitze.core' => 'zugspitze_core.swc',
			'org.foomo.zugspitze.services.core' => 'zugspitze_servicesCore.swc',
		);
		foreach ($zsExternals as $zsKey => $zsValue) {
			$libraryProject = $sources->getLibraryProject($zsKey);
			$zsLibrarySwc = $libraryProject->pathname . DIRECTORY_SEPARATOR . 'bin'  . DIRECTORY_SEPARATOR . $zsValue;
			if (\file_exists($zsLibrarySwc)) $externalLibs[] = $zsLibrarySwc;
		}

		$swcFile = FlexUtils::compileLibrarySWC($compileReport, $flexConfigEntry->sdkPath, $sourcePaths, array($this->targetSrcDir), $externalLibs);

		if (!file_exists($swcFile)) {
			throw new Exception(
					'Adobe Compc (Flex Component Compiler) failed to create the swc.' . PHP_EOL .
					PHP_EOL .
					'This typically means, that there are incomplete phpDoc comments for your service classes method' . PHP_EOL .
					'parameters and / or return values and / or the corresponding value objects.' . PHP_EOL .
					'The resulting action script will have errors like' . PHP_EOL .
					PHP_EOL .
					'// missing type declaration' . PHP_EOL .
					'public var lastResult:;' . PHP_EOL .
					PHP_EOL .
					'see also what the flex compiler put to stdErr' . PHP_EOL .
					PHP_EOL .
					$compileReport,
					1
			);
		} else {
			$ret .= $compileReport;
		}
		if (!@rename($swcFile, $this->getSWCFileName())) {
			throw new Exception('created swc ' . $swcFile . ' coudl not be moved to ' . $this->getSWCFileName(), 1);
		} else {
			$ret .= 'moving created swc ' . $swcFile . ' to ' . $this->getSWCFileName() . PHP_EOL;
		}
		return $ret;
	}

	public function operationToEventFaultName($opName)
	{
		return $opName . 'Fault';
	}

	public function operationExceptionName($opName, $excetionType)
	{
		return $opName . 'Exception' . Utils::getASType($excetionType);
	}

	public function operationToEventResultName($opName)
	{
		return $opName . 'Result';
	}

	public function operationToEventClassName($opName)
	{
		return ucfirst($opName) . 'Event';
		//return $this->serviceName . ucfirst($opName) . 'Event';
	}

	public function operationToOperationVarName($opName)
	{
		return 'operation' . ucfirst($opName);
		//return $this->serviceName . ucfirst($opName) . 'Operation';
	}

	public function complexTypeToImplClassName($complexType)
	{
		$typeName = str_replace('[]', '', $complexType->type);
		$ret = 'impl' . $typeName; //strtolower(substr($typeName,0,1)) . substr($typeName,1);
		return $ret;
	}

	public function typeToUpper($typeName)
	{
		$ret = '';
		$prepend = '';
		for ($i = 0; $i < strlen($typeName); $i++) {
			$part = substr($typeName, $i, 1);
			$upperPart = strtoupper($part);
			if ($upperPart != $part) {
				$ret .= $upperPart;
			} else {
				$ret .= $prepend . $upperPart;
				$prepend = '_';
			}
		}
		return $ret;
	}

	public function operationToResponderInterfaceName($opName)
	{
		return 'I' . ucfirst($opName) . 'Responder';
		//return $this->serviceName . ucfirst($opName) . 'Operation';
	}

	public function getClientAsClassImport($type)
	{
		$asType = new ServiceObjectType($type);
		foreach ($asType->annotations as $annotation) {
			if ($annotation instanceOf RemoteClass && !empty($annotation->name)) {
				return 'import ' . $annotation->name . ';';
			}
			if ($annotation instanceOf RemoteClass && !empty($annotation->package)) {
				$type = str_replace('[]', '', $type);
				return 'import ' . $annotation->package . '.' . Utils::getASType($type) . ';';
			}
		}
		if (!Utils::isASStandardType($asType->type)) {
			return 'import ' . $asType->getRemotePackage() . '.' . Utils::getASType(str_replace('[]', '', $type)) . ';';
		}
	}

	/**
	 * @return string
	 */
	public function getSWCFilename()
	{
		return \Foomo\Services\Module::getTmpDir() . DIRECTORY_SEPARATOR . str_replace('\\', '', $this->serviceName) . '.swc';
	}

	/**
	 * @return string
	 */
	public function getTGZFilename()
	{
		return \Foomo\Services\Module::getTmpDir() . DIRECTORY_SEPARATOR . str_replace('\\', '', $this->serviceName) . '.tgz';
	}

	/**
	 * render a (complex) type - and write it into $this->classFiles
	 *
	 * @param ServiceObjectType $type
	 */
	protected function renderVOClass(ServiceObjectType $type)
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
			$classFileName = Utils::getASType($type->type);
		}

		if ($isCommonClass) {
			$this->commonClassFiles[$commonPath . DIRECTORY_SEPARATOR . $classFileName] = $content;
		} else {
			$this->commonClassFiles[$type->getRemotePackagePath() . DIRECTORY_SEPARATOR . Utils::getASType($type->type)] = $content;
		}
	}

	/**
	 * myMethod => AbstractMyMethodCommand
	 *
	 * @param string $name
	 * @return string
	 */
	public function operationToAbstractCommandName($name)
	{
		return 'Abstract' . ucfirst($name) . 'Command';
	}

	/**
	 * myMethod => MyMethodCall
	 *
	 * @param string $name
	 * @return string
	 */
	public function operationToMethodCallName($name)
	{
		return ucfirst($name) . 'Call';
	}

	/**
	 * myMethod => MyMethodCallEvent
	 *
	 * @param string $name
	 * @return string
	 */
	public function operationToMethodCallEventName($name)
	{
		return ucfirst($name) . 'CallEvent';
	}

	/**
	 * myMethod => MyMethodOperation
	 *
	 * @param string $name
	 * @return string
	 */
	public function operationToOperationName($name)
	{
		return ucfirst($name) . 'Operation';
	}

	/**
	 * myMethod => MyMethodOperationEvent
	 *
	 * @param string $name
	 * @return string
	 */
	public function operationToOperationEventName($name)
	{
		return ucfirst($name) . 'OperationEvent';
	}

	/**
	 * myMethod => MyMethodEvent
	 *
	 * @param string $name
	 * @return string
	 */
	public function toEventName($name)
	{
		return ucfirst($name) . 'Event';
	}

	/**
	 * myOpName => MY_OP_NAME
	 *
	 * @param string $opName name of the operation
	 * @return string name of the event
	 */
	public function toConstantName($opName)
	{
		$ret = '';
		for ($i = 0; $i < strlen($opName); $i++) {
			$c = substr($opName, $i, 1);
			if (strtoupper($c) != $c) {
				$ret .= strtoupper($c);
			} else {
				$ret .= '_' . $c;
			}
		}
		return $ret;
	}

	/**
	 *	Returns all client class imports
	 *
	 * @return string
	 */
	public function getAllClientClassImports()
	{
		$output = array();
		foreach ($this->complexTypes as $type) {
			if ('' != $import = $this->getClientAsClassImport($type->type)) {
				$output[] = $import;
			}
		}
		return implode(PHP_EOL, $output);
	}

	/**
	 * Returns all your.package.calls.*Operation
	 *
	 * @return string
	 */
	public function getMethodCallImports()
	{
		$output = array();
		foreach ($this->operations as $operation) {
			$output[] = '	import ' . $this->myPackage . '.calls.' . $this->operationToMethodCallName($operation->name) . ';';
		}
		return implode(PHP_EOL, $output);
	}

	//---------------------------------------------------------------------------------------------
	// ~ Protected methods
	//---------------------------------------------------------------------------------------------


	/**
	 * @param string $code
	 * @param int $indent
	 * @return string
	 */
	protected function indent($code, $indent)
	{
		return str_repeat(chr(9), 3 + $indent) . $code;
	}

	/**
	 * try to create a folder - will only work, if the parent folder exists
	 *
	 * @param string $folder /path/to/some/folder
	 * @return boolean true
	 * @throws Exception if it does not work
	 */
	protected function tryCreateFolder($folder)
	{
		if (file_exists($folder)) {
			if (!is_dir($folder)) {
				throw new Exception($folder . ' should be a folder', 1);
			}
		} else {
			if (is_writable(dirname($folder))) {
				if (@mkdir($folder) && @chmod($folder, 0775)) {
					return true;
				} else {
					throw new Exception('could not create ' . $folder, 1);
				}
			} else {
				throw new Exception('could not create ' . $folder, 1);
			}
		}
	}

	/**
	 * write the classes to the file system, tar them and if possible create a swc
	 *
	 * @return string a report of what was done
	 * @throws Exception with a text report, of what went wrong
	 */
	protected function export()
	{
		// setup structure
		$ret = 'GENERATING SOURCES' . PHP_EOL;
		if ((!empty($this->targetSrcDir) && is_dir($this->targetSrcDir)) || $this->tryCreateFolder($this->targetSrcDir)) { // && is_writable($this->targetSrcDir)) {
			$path = $this->getPath();
			if ($this->clearSrcDir) {
				$rmCall = new CliCall('rm', array('-Rvf', $path . '/*'));
				$rmCall->execute();
				if ($rmCall->exitStatus === 0) {
					$ret .= PHP_EOL . 'clearing old sources in ' . $path . ' :' . PHP_EOL . implode(PHP_EOL . '  ', explode(PHP_EOL, $rmCall->stdOut)) . PHP_EOL;
				} else {
					throw new Exception('failed to remove old sources ' . PHP_EOL . $rmCall->report, 1);
				}
			}
		} else {
			throw new Exception('targetSrcDir does not exist ... ' . $this->targetSrcDir);
		}
		foreach ($this->packageFolders as $folder) {
			$folder = $path . DIRECTORY_SEPARATOR . $folder;
			$this->tryCreateFolder($folder);
		}
		$ret .= 'writing class files :' . PHP_EOL . PHP_EOL;
		foreach ($this->classFiles as $fileName => $fileContents) {
			$fileName = $path . DIRECTORY_SEPARATOR . $fileName . '.as';
			$ret .= '  ' . $fileName . PHP_EOL;
			if (!@file_put_contents($fileName, $fileContents)) {
				throw new Exception('could not write file ' . $fileName . ' ' . $fileContents, 1);
			}
		}
		$ret .= $this->writeCommonClassFiles();
		return $ret;
	}

	/**
	 * get a (specific) template
	 *
	 * @param string $templateBaseName base name of the template
	 *
	 * @return Foomo\View
	 */
	protected function getView($templateBaseName)
	{
		$view = \Foomo\Services\Module::getView($this, $this->templateFolder . DIRECTORY_SEPARATOR . $templateBaseName, $this);
		if($view) {
			return $view;
		} else {
			return \Foomo\Services\Module::getView($this, $templateBaseName, $this);
		}
	}

	//---------------------------------------------------------------------------------------------
	// ~ Private methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @return string
	 */
	private function getPath()
	{
		$packageFolders = explode('.', $this->myPackage);
		$path = $this->targetSrcDir;
		foreach ($packageFolders as $packageFolder) {
			$path .= DIRECTORY_SEPARATOR . $packageFolder;
			$this->tryCreateFolder($path);
		}
		return $path;
	}

	/**
	 * write shared value objects
	 *
	 */
	private function writeCommonClassFiles()
	{
		$ret = '';
		foreach ($this->commonClassFiles as $fileName => $fileContents) {
			$path = $this->targetSrcDir;
			foreach (explode(DIRECTORY_SEPARATOR, dirname($fileName)) as $packageFolder) {
				$path .= DIRECTORY_SEPARATOR . $packageFolder;
				$this->tryCreateFolder($path);
			}
			$ret .= '  ' . $this->targetSrcDir . DIRECTORY_SEPARATOR . $fileName . '.as' . PHP_EOL;
			file_put_contents($this->targetSrcDir . DIRECTORY_SEPARATOR . $fileName . '.as', $fileContents);
		}
		return $ret;
	}
}
