<?php

use Foomo\Services\RPC;
use Foomo\Services\RPC\Serializer\PHP;

Foomo\Session::lockAndLoad();

RPC::create(
		Foomo\Session::getClassInstance('Foomo\\Services\\Mock\\Service', 'a')
	)
	->serializeWith(new PHP())
	->run()
;