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
        $class_path = $_SERVER['argv'][1];
        if (!$class_path){
            echo('Class file path must be passed as first parameter.' . "\n");
            echo('If the class file doesnt exist - it will be created.' . "\n");
            exit(1);
        }

        echo 'Class path: "' . $class_path . '"' . "\n\n";

        if (!file_exists($class_path)){
            echo "Class file not found, create new class?\n";
            echo "\tENTER Yes\n";
            CLIUtil::readStdinAnswer();

            $pathinfo = pathinfo($class_path);
            $cwd = getcwd();

            CLICreateModel::$model_class_name = $pathinfo['filename'];

            $model_namespace_for_path = $pathinfo['dirname'];
            // убираем из начала текущую папку
            if (strpos($model_namespace_for_path, $cwd) === 0) {
                $model_namespace_for_path = substr($model_namespace_for_path, strlen($cwd));
            }

            // отрезаем слэш в начале если есть
            if (substr($model_namespace_for_path, 0, 1) == DIRECTORY_SEPARATOR) {
                $model_namespace_for_path = substr($model_namespace_for_path, strlen(DIRECTORY_SEPARATOR));
            }

            CLICreateModel::$model_namespace_for_path = $model_namespace_for_path;
            CLICreateModel::$model_namespace_for_class = str_replace(DIRECTORY_SEPARATOR, '\\', $model_namespace_for_path);

            CLICreateModel::chooseModelDBIndex();
        }

        while (true) {
            echo "Choose PHPModel function:\n";
            //echo "\t" . self::FUNCTION_CREATE_MODEL . ": create model\n";
            echo "\t" . self::FUNCTION_ADD_MODEL_FIELD . ": add field to existing model\n";
            echo "\t" . self::FUNCTION_MODEL_FIELD_EXTRAS . ": extra operations on model field\n";
            echo "\tENTER: exit\n";

            $command_str = CLIUtil::readStdinAnswer();

            switch ($command_str) {
//                case self::FUNCTION_CREATE_MODEL:
//                    CLICreateModel::enterClassNameScreen();
//                    break;

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