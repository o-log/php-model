<?php

require_once 'vendor/autoload.php';

\PHPModelDemo\ModelDemoConfig::init();

\OLOG\DB\MigrateCLI::run();