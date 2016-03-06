<?php

namespace OLOG\Model\CLI;

class CLIMenu
{
    static public function run(){
        echo "Enter 1 to execute SQL\n";
        echo "Enter 2 to create model\n";

        $command_str = trim(fgets(STDIN));

        switch ($command_str){
            case "1":
                CLIExecuteSql::executeSql();
                break;

            case "2":
                CLICreateModel::run();
                break;

            default:
                exit;
        }
    }
}