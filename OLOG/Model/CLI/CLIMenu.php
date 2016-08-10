<?php

namespace OLOG\Model\CLI;

use OLOG\CliUtil;

class CLIMenu
{
    const FUNCTION_EXECUTE_SQL = 1;
    const FUNCTION_CREATE_MODEL = 2;
    const FUNCTION_ADD_MODEL_FIELD = 3;
    const FUNCTION_MODEL_FIELD_EXTRAS = 4;

    static public function run()
    {
        while (true) {
            echo CliUtil::delimiter();
            echo "Choose PHPModel function:\n";
            echo "\t" . self::FUNCTION_EXECUTE_SQL . ": execute new SQL queries from registry\n";
            echo "\t" . self::FUNCTION_CREATE_MODEL . ": create model\n";
            echo "\t" . self::FUNCTION_ADD_MODEL_FIELD . ": add field to existing model\n";
            echo "\t" . self::FUNCTION_MODEL_FIELD_EXTRAS . ": extra operations on model field\n";
            echo "\tENTER: exit\n";

            $command_str = CliUtil::readStdinAnswer();

            switch ($command_str) {
                case self::FUNCTION_EXECUTE_SQL:
                    CLIExecuteSql::executeSqlScreen();
                    break;

                case self::FUNCTION_CREATE_MODEL:
                    CLICreateModel::enterClassNameScreen();
                    break;

                case self::FUNCTION_ADD_MODEL_FIELD:
                    $cli_add_field_obj = new CLIAddFieldToModel();
                    $cli_add_field_obj->addField();
                    break;

                case self::FUNCTION_MODEL_FIELD_EXTRAS:
                    $cli_add_field_obj = new CLIAddFieldToModel();
                    $cli_add_field_obj->extraFieldFunctionsScreen();
                    break;

                default:
                    exit;
            }
        }
    }
}