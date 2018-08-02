<?php
// not using shebang - default php path may be not desired

require_once 'vendor/autoload.php';

\Config\Config::init();

\OLOG\Model\CLI\Menu::run();