<?php

namespace OLOG\Model\CLI;

class CLIMenu
{
    const FUNCTION_EXECUTE_SQL = 1;
    const FUNCTION_CREATE_MODEL = 2;
    const FUNCTION_ADD_MODEL_FIELD = 3;

    static public function run(){
        echo "PHPModel functions:\n";
        echo self::FUNCTION_EXECUTE_SQL . ": execute SQL from registry\n";
        echo self::FUNCTION_CREATE_MODEL . ": create model\n";
        echo self::FUNCTION_ADD_MODEL_FIELD . ": add field to existing model\n";

        $command_str = trim(fgets(STDIN));

        switch ($command_str){
            case self::FUNCTION_EXECUTE_SQL:
                CLIExecuteSql::executeSql();
                break;

            case self::FUNCTION_CREATE_MODEL:
                CLICreateModel::run();
                break;

            case self::FUNCTION_ADD_MODEL_FIELD:
                $cli_add_field_obj = new CLIAddFieldToModel();
                $cli_add_field_obj->addField();
                break;

            default:
                exit;
        }
    }
}