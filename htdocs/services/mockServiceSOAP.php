<?php

\Foomo\Session::lockAndLoad();
\Foomo\Services\SOAP::serveClass(\Foomo\Session::getClassInstance('Foomo\\Services\\Mock\\Service'));