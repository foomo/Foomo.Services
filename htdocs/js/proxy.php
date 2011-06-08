<?php

header('Content-Type: text/javascript');

echo Foomo\Services\ProxyGenerator\JS\JQuery::render('Foomo\\Services\\Mock\\Service');
