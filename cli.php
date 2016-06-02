<?php

require_once 'vendor/autoload.php';

\OLOG\ConfWrapper::assignConfig(\PHPModelDemo\ModelDemoConfig::get());

\OLOG\Model\CLI\CLIMenu::run();