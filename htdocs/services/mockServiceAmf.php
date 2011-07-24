<?php

use Foomo\Services\RPC;
use Foomo\Services\RPC\Serializer\AMF;

Foomo\Session::lockAndLoad();

// @todo: rename package / client namespace
RPC::create(
		Foomo\Session::getClassInstance('Foomo\\Services\\Mock\\Service')
	)
	->serializeWith(new AMF())
	->clientNamespace('com.bestbytes.zugspitze.services.namespaces.php')
	->run()
;