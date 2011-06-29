<?php

use Foomo\Services\RPC;
use Foomo\Services\RPC\Serializer\JSON;

Foomo\Session::lockAndLoad();

RPC::serveClass(
	Foomo\Session::getClassInstance('Foomo\Services\Mock\Service'),
	$serializer = new JSON(),
	$package = 'mockProxy'
);