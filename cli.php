<?php

require_once 'vendor/autoload.php';

\OLOG\ConfWrapper::assignConfig(\PHPModelDemo\Config::get());

\OLOG\Model\CLI\CLIMenu::run();