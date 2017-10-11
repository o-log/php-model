<?php

namespace OLOG\Model\CLI;

use OLOG\CLIUtil;

class Menu
{
    const FUNCTION_CREATE_MODEL = 1;
    const FUNCTION_ADD_MODEL_FIELD = 2;
    const FUNCTION_MODEL_FIELD_EXTRAS = 3;

    static public function run()
    {
        while (true) {
            echo "Choose PHPModel function:\n";
            echo "\t" . self::FUNCTION_CREATE_MODEL . ": create model\n";
            echo "\t" . self::FUNCTION_ADD_MODEL_FIELD . ": add field to existing model\n";
            echo "\t" . self::FUNCTION_MODEL_FIELD_EXTRAS . ": extra operations on model field\n";
            echo "\tENTER: exit\n";

            $command_str = CLIUtil::readStdinAnswer();

            switch ($command_str) {
                case self::FUNCTION_CREATE_MODEL:
                    CLICreateModel::enterClassNameScreen();
                    break;

                case self::FUNCTION_ADD_MODEL_FIELD:
                    $cli_add_field_obj = new CLIAddFieldToModel();
                    $cli_add_field_obj->addFieldScreen();
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