<?php

namespace Foomo;

Frontend::setUpToolbox(Services\Module::NAME);

echo MVC::run('Foomo\\Services\\Frontend');