<?php

namespace Foomo;

Session::lockAndLoad();

Services\SOAP::serveClass(
	Session::getClassInstance('Foomo\\Services\\Mock\\Service')
);