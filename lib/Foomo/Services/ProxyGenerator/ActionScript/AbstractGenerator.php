<?php

namespace Foomo\Services\ProxyGenerator\ActionScript;

use Foomo\Flash\ActionScript\PHPUtils;
use Foomo\Flash\ActionScript\ViewHelper;

/**
 * base class to render actionscript service proxies
 *
 */
abstract class AbstractGenerator extends \Foomo\Services\Renderer\AbstractRenderer
{
	//---------------------------------------------------------------------------------------------
	// ~ Variables
	//---------------------------------------------------------------------------------------------

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
	 * @var string
	 */
	public $targetPackage;
	/**
	 * the class to be rendered as a data class in the DataClass Template
	 *
	 * @var Foomo\Services\Reflection\ServiceObjectType
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
	 * @var Foomo\Services\Reflection\ServiceObjectType[]
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
	 * folder list, of all the folders, to represent the package structure
	 *
	 * @var array
	 */
	public $packageFolders;

	//---------------------------------------------------------------------------------------------
	// ~ Constructor
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $targetPackage
	 * @param string $targetSrcDir
	 */
	public function __construct($targetPackage)
	{
		$this->targetPackage = $targetPackage;
	}

	//---------------------------------------------------------------------------------------------
	// ~ Abstract methods
	//---------------------------------------------------------------------------------------------

	/**
	 * get target src dir
	 *
	 * @return string
	 */
	abstract public function getTargetSrcDir();

	/**
	 * get swc file name
	 *
	 * @return string
	 */
	abstract public function getSWCFilename();

	/**
	 * get tgz file name
	 *
	 * @return string
	 */
	abstract public function getTGZFilename();

	/**
	 * get a (specific) template
	 *
	 * @param string $template base name of the template
	 * @return Foomo\View
	 */
	abstract protected function getView($template);

	//---------------------------------------------------------------------------------------------
	// ~ Abstract methods implementation
	//---------------------------------------------------------------------------------------------

	/**
	 * @param string $serviceName
	 */
	public function init($serviceName)
	{
		$this->serviceName = $serviceName;

		$this->targetSrcDir = $this->getTargetSrcDir();
		@unlink($this->targetSrcDir);
		\Foomo\Modules\Resource\Fs::getAbsoluteResource(\Foomo\Modules\Resource\Fs::TYPE_FOLDER, $this->targetSrcDir)->tryCreate();

		$nameParts = explode('\\', $this->serviceName);
		$this->proxyClassName = $this->serviceName . 'Proxy';
		if ($this->myPackage != '') {
			$this->myPackage = $this->targetPackage . '.' . strtolower(substr(PHPUtils::getASType($this->serviceName), 0, 1)) . substr(PHPUtils::getASType($this->serviceName), 1);
		} else {
			$serviceType = new \Foomo\Services\Reflection\ServiceObjectType($this->serviceName);
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
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderServiceType(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
	}

	/**
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 */
	public function renderType(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		if (!PHPUtils::isASStandardType($type->type)) $this->complexTypes[$type->type] = $type;
	}

	/**
	 * @param Foomo\Services\Reflection\ServiceOperation $op
	 */
	public function renderOperation(\Foomo\Services\Reflection\ServiceOperation $op)
	{
		$this->currentOperation = $op;
		array_push($this->operations, $op);
	}

	/**
	 * @return string a report of what was done
	 */
	public function output()
	{
		return $this->export();
	}

	//---------------------------------------------------------------------------------------------
	// ~ Public methods
	//---------------------------------------------------------------------------------------------

	/**
	 * for remote class alias registration
	 *
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 * @return string
	 */
	public function getVORemoteAliasName(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		return str_replace('\\', '.', $type->type);
	}

	/**
	 * get the class name for a value object
	 *
	 * @param Foomo\Services\Reflection\ServiceObjectType $type
	 * @return string
	 */
	public function getVOClassName(\Foomo\Services\Reflection\ServiceObjectType $type)
	{
		// are therer annotations that direct to another class name, than the php one
		foreach ($type->annotations as $annotation) {
			if ($annotation instanceOf \Foomo\Services\Reflection\RemoteClass) {
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
	 * @return string
	 */
	public function packTgz()
	{
		$ret = 'PACKING SOURCES' . PHP_EOL;
		if (file_exists($this->getTGZFilename())) {
			$ret .= 'removing old archive ' . $this->getTGZFileName() . PHP_EOL;
			@unlink($this->getTgzFileName());
		}
		$ret .= PHP_EOL . 'packing sources with tar - ';
		$ret .= \Foomo\CliCall\Tar::create($this->getTGZFilename())
			->moveIntoDirectory($this->targetSrcDir)
			->addDirectoryFiles()
			->createTgz()
			->report
		;
		return $ret;
	}

	/**
	 * @param string $configId Flex config id to use
	 * @return string
	 */
	public function compile($configId)
	{
		$ret = 'COMPILING' . PHP_EOL;
		if (file_exists($this->getSWCFileName())) {
			$ret .= 'removing old swc ' . $this->getSWCFileName() . PHP_EOL;
			@unlink($this->getSWCFileName());
		}
		$ret .= 'calling Adobe Compc (Flex Component Compiler) see what the compiler reported - ';
		$compileReport = '';


		$externalLibs = array();
		$sourcePaths = array();


		# get flex config
		$flexConfigEntry = \Foomo\Flash\Module::getCompilerConfig()->getEntry($configId);

		# get compiler
		$compc = \Foomo\CliCall\Compc::create($flexConfigEntry->sdkPath);
		$compc->addSourcePaths(array($this->targetSrcDir));
		$compc->addIncludeSources(array($this->targetSrcDir));
		$compc->addSourcePaths($flexConfigEntry->sourcePaths);
		$compc->addExternalLibraryPaths($flexConfigEntry->externalLibs);


		# add zugspitze swcs
		$sources = \Foomo\Zugspitze\Vendor::getSources();
		$zsExternals = array(
			'org.foomo.zugspitze.core'					=> 'zugspitze_core.swc',
			'org.foomo.zugspitze.services.core.rpc'		=> 'zugspitze_servicesCoreRpc.swc',
			'org.foomo.zugspitze.services.core.proxy'	=> 'zugspitze_servicesCoreProxy.swc',
		);
		foreach ($zsExternals as $zsKey => $zsValue) {
			$libraryProject = $sources->getLibraryProject($zsKey);
			$zsLibrarySwc = $libraryProject->pathname . DIRECTORY_SEPARATOR . 'bin'  . DIRECTORY_SEPARATOR . $zsValue;
			if (\file_exists($zsLibrarySwc)) $compc->addExternalLibraryPaths(array($zsLibrarySwc));
		}

		$compc->compileSwc($this->getSWCFileName());

		if (!file_exists($this->getSWCFileName())) {
			throw new \Exception(
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
					$compc->report,
					1
			);
		} else {
			$ret .= $compc->report;
		}
		return $ret;
	}

	/**
	 * Returns all client class imports
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
	 * @param string $type
	 * @return string
	 */
	public function getClientAsClassImport($type)
	{
		$asType = new \Foomo\Services\Reflection\ServiceObjectType($type);
		foreach ($asType->annotations as $annotation) {
			if ($annotation instanceOf \Foomo\Services\Reflection\RemoteClass && !empty($annotation->name)) {
				return 'import ' . $annotation->name . ';';
			}
			if ($annotation instanceOf \Foomo\Services\Reflection\RemoteClass && !empty($annotation->package)) {
				$type = str_replace('[]', '', $type);
				return 'import ' . $annotation->package . '.' . PHPUtils::getASType($type) . ';';
			}
		}
		if (!PHPUtils::isASStandardType($asType->type)) {
			return 'import ' . $asType->getRemotePackage() . '.' . PHPUtils::getASType(str_replace('[]', '', $type)) . ';';
		}
	}

	//---------------------------------------------------------------------------------------------
	// ~ Protected methods
	//---------------------------------------------------------------------------------------------

	/**
	 * @return string
	 */
	protected function getPath()
	{
		$packageFolders = explode('.', $this->myPackage);
		$path = $this->targetSrcDir;
		foreach ($packageFolders as $packageFolder) {
			$path .= DIRECTORY_SEPARATOR . $packageFolder;
			\Foomo\Modules\Resource\Fs::getAbsoluteResource(\Foomo\Modules\Resource\Fs::TYPE_FOLDER, $path)->tryCreate();
		}
		return $path;
	}

	/**
	 * write shared value objects
	 */
	protected function writeCommonClassFiles()
	{
		$ret = '';
		foreach ($this->commonClassFiles as $fileName => $fileContents) {
			$path = $this->targetSrcDir;
			foreach (explode(DIRECTORY_SEPARATOR, dirname($fileName)) as $packageFolder) {
				$path .= DIRECTORY_SEPARATOR . $packageFolder;
				\Foomo\Modules\Resource\Fs::getAbsoluteResource(\Foomo\Modules\Resource\Fs::TYPE_FOLDER, $path)->tryCreate();
			}
			$ret .= '  ' . $this->targetSrcDir . DIRECTORY_SEPARATOR . $fileName . '.as' . PHP_EOL;
			file_put_contents($this->targetSrcDir . DIRECTORY_SEPARATOR . $fileName . '.as', $fileContents);
		}
		return $ret;
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
		if ((!empty($this->targetSrcDir) && is_dir($this->targetSrcDir)) || \Foomo\Modules\Resource\Fs::getAbsoluteResource(\Foomo\Modules\Resource\Fs::TYPE_FOLDER, $this->targetSrcDir)->tryCreate()) {
			$path = $this->getPath();
			if ($this->clearSrcDir) {
				$rmCall = new \Foomo\CliCall('rm', array('-Rvf', $path . '/*'));
				$rmCall->execute();
				if ($rmCall->exitStatus === 0) {
					$ret .= PHP_EOL . 'clearing old sources in ' . $path . ' :' . PHP_EOL . implode(PHP_EOL . '  ', explode(PHP_EOL, $rmCall->stdOut)) . PHP_EOL;
				} else {
					throw new \Exception('failed to remove old sources ' . PHP_EOL . $rmCall->report, 1);
				}
			}
		} else {
			throw new \Exception('targetSrcDir does not exist ... ' . $this->targetSrcDir);
		}
		foreach ($this->packageFolders as $folder) {
			\Foomo\Modules\Resource\Fs::getAbsoluteResource(\Foomo\Modules\Resource\Fs::TYPE_FOLDER, $path . DIRECTORY_SEPARATOR . $folder)->tryCreate();
		}
		$ret .= 'writing class files :' . PHP_EOL . PHP_EOL;
		foreach ($this->classFiles as $fileName => $fileContents) {
			$fileName = $path . DIRECTORY_SEPARATOR . $fileName . '.as';
			$ret .= '  ' . $fileName . PHP_EOL;
			if (!@file_put_contents($fileName, $fileContents)) {
				throw new \Exception('could not write file ' . $fileName . ' ' . $fileContents, 1);
			}
		}
		$ret .= $this->writeCommonClassFiles();
		return $ret;
	}
}
