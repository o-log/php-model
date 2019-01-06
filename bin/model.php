<?php
// not using shebang - default php path may be not the desired one

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

require_once 'vendor/autoload.php';

\Config\Config::init();

\OLOG\Model\CLI\Menu::run();
