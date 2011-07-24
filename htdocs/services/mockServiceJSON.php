<?php

use Foomo\Services\RPC;
use Foomo\Services\RPC\Serializer\JSON;

Foomo\Session::lockAndLoad();

RPC::create(
		Foomo\Session::getClassInstance('Foomo\\Services\\Mock\\Service')
	)
	->serializeWith(new JSON())
	->clientNamespace('mockProxy')
	->run()
;