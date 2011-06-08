<?php

namespace Foomo\Services\ProxyGenerator\ActionScript;

use Foomo\Services\Reflection\ServiceObjectType;
use Foomo\Config;
use Exception;
use Foomo\CliCall;
use Foomo\Flex\Settings as FlexSettings;
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
	
	public $standarTypes = array(
		'int' => 'int',
		'integer' => 'int',
		'bool' => 'Boolean',
		'boolean' => 'Boolean',
		'string' => 'String',
		'float' => 'Number',
		'double' => 'Number',
		'mixed' => 'Object',
	);

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
			$this->myPackage = $this->targetPackage . '.' . strtolower(substr($this->typeToASType($this->serviceName), 0, 1)) . substr($this->typeToASType($this->serviceName), 1);
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
		if (!\in_array(\strtolower($type->type), \array_keys($this->standarTypes))) {
			$this->complexTypes[$type->type] = $type;
		} 
	}

	public function renderOperation(\Foomo\Services\Reflection\ServiceOperation $op)
	{
		$this->currentOperation = $op;
		array_push($this->operations, $op);
		
		// Events
		$view = $this->getView('EventClass');
		$this->classFiles['events' . DIRECTORY_SEPARATOR . $this->operationToEventClassName($op->name)] = $view->render();

		// ops
		$view = $this->getView('OperationClass');
		$this->classFiles['model' . DIRECTORY_SEPARATOR . $this->operationToOperationName($op->name)] = $view->render();

		// commands
		$view = $this->getView('CommandClass');
		$this->classFiles['commands' . DIRECTORY_SEPARATOR . $this->operationToCommandName($op->name)] = $view->render();
		
		// responders
		$view = $this->getView('ResponderInterface');
		$this->classFiles['responders' . DIRECTORY_SEPARATOR . $this->operationToResponderInterfaceName($op->name)] = $view->render();
	}

	public function output()
	{
		// rendering the proxy base class
		$view = $this->getView('ProxyBaseClass');
		$this->classFiles['model' . DIRECTORY_SEPARATOR . $this->typeToASType($this->serviceName) . 'ProxyBase'] = $view->render();

		// rendering the proxy class
		$view = $this->getView('ProxyClass');
		$this->classFiles['model' . DIRECTORY_SEPARATOR . $this->typeToASType($this->serviceName) . 'Proxy'] = $view->render();

		// AS3 class alias registry
		$view = $this->getView('ClassAliasRegistry');
		$this->classFiles['model' . DIRECTORY_SEPARATOR . 'ClassAliasRegistry'] = $view->render();

		// render all the vos
		foreach ($this->complexTypes as $complexType) {
			$this->renderTypeClass($complexType);
		}

		return $this->export();
	}
	
	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

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
	 * map a type between php and ActionScript
	 *
	 * @param string $type php class name | type
	 * @return string ActionScript class name | type
	 */
	public function typeToASType($type)
	{
		$asType = '*';
		# check if it's a typed array
		$isArray = (substr($type, strlen($type) - 2) == '[]');
		if ($isArray) $type = \substr($type, 0, strlen($type) - 2);

		if (isset($this->standarTypes[\strtolower($type)])) {
			$asType = $this->standarTypes[\strtolower($type)];
		} else {
			$serviceObjectType = new ServiceObjectType($type);
			if ('' != $remoteClass = $serviceObjectType->getRemoteClass()) {
				$remoteClass = basename(str_replace('.', DIRECTORY_SEPARATOR, $remoteClass));
				$asType = $remoteClass;
			} else {
				if (strpos($type, '\\') !== false) {
					$parts = explode('\\', $type);
					$asType = $parts[count($parts) - 1];
				} else {
					$asType = $type;
				}
			}
		}
		return ($isArray) ? 'Array' : $asType;
		// @todo kevin: switch to vectors
		#return ($isArray) ? 'Vector.<' . $asType . '>' : $asType;
	}

	/**
	 * derive an event name from an operation name - basically turns sth. like myOpName to MY_OP_NAME
	 *
	 * @param string $opName name of the operation
	 * @return string name of the event
	 */
	public function operationToEventName($opName)
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
	 * render a (complex) type - and write it into $this->classFiles
	 *
	 * @param ServiceObjectType $type
	 */
	protected function renderTypeClass(ServiceObjectType $type)
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
		
		$view = $this->getView('DataClass');
		$content = $view->render();

		if ($hasRemoteClass) {
			$classFileName = $this->getVOClassName($type);
		} else {
			$classFileName = $this->typeToASType($type->type);
		}

		if ($isCommonClass) {
			$this->commonClassFiles[$commonPath . DIRECTORY_SEPARATOR . $classFileName] = $content;
		} else {
			$this->commonClassFiles[$type->getRemotePackagePath() . DIRECTORY_SEPARATOR . $this->typeToASType($type->type)] = $content;
		}
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
				if (@mkdir($folder)) {
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
				$rmCall = new CliCall(
								'rm',
								array(
									'-Rvf',
									$path . '/*'
								)
				);
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

	public function packTgz()
	{
		$path = $this->getPath();
		$ret = 'PACKING SOURCES' . PHP_EOL;
		$ret .= 'removing old archive ' . $this->getTGZFileName() . PHP_EOL;
		@unlink($this->getTgzFileName());
		$tarFiles = array_diff(\scandir($this->targetSrcDir), array('.', '..'));;
		$ret .= PHP_EOL . 'packing sources with tar - ';
		$tarCall = new CliCall(
						'tar',
						array(
							'--directory',
							$this->targetSrcDir,
							'-czvf',
							$this->getTgzFileName(),
							\implode(' ', $tarFiles)
						)
		);
		$tarCall->execute();
		$ret .= $tarCall->report;
		return $ret;
	}

	public function compile()
	{
		$ret = 'COMPILING' . PHP_EOL;
		$ret .= 'removing old swc ' . $this->getSWCFileName() . PHP_EOL;
		@unlink($this->getSWCFileName());
		$ret .= 'calling Adobe Compc (Flex Component Compiler) see what the compiler reported - ';
		$compileReport = '';
		$sourcePaths = array($this->targetSrcDir);
		$flexConfig = \Foomo\Flex\DomainConfig::getInstance();
		if ($flexConfig && !empty($flexConfig->srcDirs)) {
			$sourcePaths = array_unique(array_merge($sourcePaths, $flexConfig->srcDirs));
		}

		if (\Foomo\Services\Utils::getServiceUsesRemoteClasses($this->serviceName)) {
			if (!is_dir(FlexSettings::$PROJECT_DIR . DIRECTORY_SEPARATOR . 'src') || !is_dir(FlexSettings::$PROJECT_DIR . DIRECTORY_SEPARATOR . 'libs')) {
				throw new Exception('The project needs client sources and apparently they are not there - please check ' . FlexSettings::$PROJECT_DIR, 1);
			}
			$sourcePaths[] = FlexSettings::$PROJECT_DIR . DIRECTORY_SEPARATOR . 'src';
			$sourcePaths[] = FlexSettings::$PROJECT_DIR . DIRECTORY_SEPARATOR . 'libs';
		}

		$swcs = array(
			FlexSettings::$FLEX_HOME . DIRECTORY_SEPARATOR . 'frameworks' . DIRECTORY_SEPARATOR . 'libs',
			FlexSettings::$FLEX_HOME . DIRECTORY_SEPARATOR . 'frameworks' . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . 'air'
		);
		
		# add zugspitze swcs
		$zsScaffold = new \Zugspitze\Scaffold(\Zugspitze\Module::getVendorDir());
		$zsLibraries = $zsScaffold->getLibraries(false);
		$zsExternals = array(
			'com.bestbytes.zugspitze.core' => 'zugspitze_core.swc',
			'com.bestbytes.zugspitze.services' => 'zugspitze_services.swc',
		);
		foreach ($zsExternals as $zsKey => $zsValue) {
			$zsLibrary = $zsLibraries[$zsKey];
			$zsLibrarySwc = $zsLibrary->pathname . DIRECTORY_SEPARATOR . 'bin'  . DIRECTORY_SEPARATOR . $zsValue;
			if (\file_exists($zsLibrarySwc)) $swcs[] = $zsLibrarySwc;
		}

		$swcFile = FlexUtils::compileLibrarySWC($compileReport, $sourcePaths, array($this->targetSrcDir), $swcs);
		
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

	public function getSWCFilename()
	{
		return Config::getTempDir() . DIRECTORY_SEPARATOR . str_replace('\\', '', $this->serviceName) . '.swc';
	}

	public function getTGZFilename()
	{
		return Config::getTempDir() . DIRECTORY_SEPARATOR . str_replace('\\', '', $this->serviceName) . '.tgz';
	}

	protected function indent($code, $indent)
	{
		return str_repeat(chr(9), 3 + $indent) . $code;
	}

	public function operationToEventFaultName($opName)
	{
		return $opName . 'Fault';
	}

	public function operationExceptionName($opName, $excetionType)
	{
		return $opName . 'Exception' . $this->typeToASType($excetionType);
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

	public function operationToOperationName($opName)
	{
		return ucfirst($opName) . 'Operation';
		//return $this->serviceName . ucfirst($opName) . 'Operation';
	}

	public function operationToOperationVarName($opName)
	{
		return 'operation' . ucfirst($opName);
		//return $this->serviceName . ucfirst($opName) . 'Operation';
	}

	public function operationToCommandName($opName)
	{
		return ucfirst($opName) . 'Command';
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

	public function getAllClientClassImports()
	{
		$ret = '// all local imports' . PHP_EOL;
		foreach ($this->complexTypes as $type) {
			if ('' != $import = $this->getClientAsClassImport($type->type)) {
				$ret .= $import . PHP_EOL;
			}
		}
		return $ret;
	}

	public function getClientAsClassImport($type)
	{
		$asType = new ServiceObjectType($type);
		foreach ($asType->annotations as $annotation) {
			if ($annotation instanceOf RemoteClass && !empty($annotation->name)) {
				return '	import ' . $annotation->name . ';';
			}
			if ($annotation instanceOf RemoteClass && !empty($annotation->package)) {
				$type = str_replace('[]', '', $type);
				return '	import ' . $annotation->package . '.' . $this->typeToASType($type) . ';';
			}
		}
		if (!\array_key_exists($asType->type, $this->standarTypes)) {
			return '	import ' . $asType->getRemotePackage() . '.' . $this->typeToASType(str_replace('[]', '', $type)) . ';';
		}
	}

}
