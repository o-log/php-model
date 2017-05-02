<?php

namespace OLOG\Model;

use OLOG\CLIUtil;
use OLOG\Model\CLI\PHPModelCLIMenu;

class CLIMenu
{
    static public function run(){
        $menu_classes_arr = ModelConfig::getCLIMenuClassesArr();

        echo 'Choose module:' . "\n";

        foreach ($menu_classes_arr as $index => $menu_class){
            echo "\t" . $index . ': ' . $menu_class . "\n";
        }

        echo "\tENTER: " . 'PHPModel cli menu' . "\n";

        $command_str = CLIUtil::readStdinAnswer();

        if ($command_str == '') {
            PHPModelCLIMenu::run();
        } else {
            $module_index = $command_str;
            if (!array_key_exists($module_index, $menu_classes_arr)){
                echo 'Unknown command' . "\n";
                return;
            } else {
                /** @var InterfaceCLIMenu $menu_class */
                $menu_class = $menu_classes_arr[$module_index];
                $menu_class::run();
            }
        }
    }
}