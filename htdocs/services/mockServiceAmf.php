<?php

use Foomo\Services\RPC;
use Foomo\Services\RPC\Serializer\AMF;

Foomo\Session::lockAndLoad();

RPC::serveClass(
	Foomo\Session::getClassInstance('Foomo\\Services\\Mock\\Service'),
	$serializer = new AMF,
	$actionScriptPackage = 'com.bestbytes.zugspitze.services.namespaces.php'
);