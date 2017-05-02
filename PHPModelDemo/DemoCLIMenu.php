<?php

namespace PHPModelDemo;

use OLOG\CLIUtil;
use OLOG\Model\InterfaceCLIMenu;

class DemoCLIMenu implements InterfaceCLIMenu
{
    public static function run(){
        CLIUtil::delimiter();
        echo 'Demo cli menu started.' . "\n";
    }
}