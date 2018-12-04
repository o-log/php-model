<?php

require_once '../vendor/autoload.php';

\Config\Config::init();

echo '<div><a href="/">reload</a></div>';

\PHPModelDemo\MainA::render();
