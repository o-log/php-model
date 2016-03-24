<?php

namespace OLOG\Model\CLI;

class CLIMenu
{
    const FUNCTION_EXECUTE_SQL = 1;
    const FUNCTION_CREATE_MODEL = 2;
    const FUNCTION_ADD_MODEL_FIELD = 3;
    const FUNCTION_MODEL_FIELD_EXTRAS = 4;

    static public function run(){
        echo "Choose PHPModel function:\n";
        echo "\t" . self::FUNCTION_EXECUTE_SQL . ": execute new SQL queries from registry\n";
        echo "\t" . self::FUNCTION_CREATE_MODEL . ": create model\n";
        echo "\t" . self::FUNCTION_ADD_MODEL_FIELD . ": add field to existing model\n";
        echo "\t" . self::FUNCTION_MODEL_FIELD_EXTRAS . ": extra operations on model field\n";

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

            case self::FUNCTION_MODEL_FIELD_EXTRAS:
                $cli_add_field_obj = new CLIAddFieldToModel();
                $cli_add_field_obj->extraFieldFunctions();
                break;

            default:
                exit;
        }
    }
}