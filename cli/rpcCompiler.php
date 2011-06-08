<?php

include(dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'radactCli.inc.php');

echo \Foomo\Services\Cli::serveClass('Foomo\Services\RPC\Compiler');