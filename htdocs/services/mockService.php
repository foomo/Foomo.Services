<?php

use Foomo\Services\RPC;
use Foomo\Services\RPC\Serializer\PHP;

Foomo\Session::lockAndLoad();

RPC::serveClass(
	Foomo\Session::getClassInstance('Foomo\\Services\\Mock\\Service'),
	$serializer = new PHP()
);