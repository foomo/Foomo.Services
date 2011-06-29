<?php

use Foomo\Services\RPC;
use Foomo\Services\RPC\Serializer\PHP;

Foomo\Session::lockAndLoad();

// @todo: rename package
RPC::serveClass(
	Foomo\Session::getClassInstance('Foomo\Services\Mock\Service', 'a'),
	$serializer = new PHP(),
	$actionScriptPackage = 'com.bestbytes.zugspitze.services.namespaces.php'
);