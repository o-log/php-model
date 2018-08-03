<?php

namespace OLOG\Model\CLI;

use OLOG\CLIUtil;

class Menu
{
    const FUNCTION_ADD_MODEL_FIELD = 2;
    const FUNCTION_MODEL_FIELD_EXTRAS = 3;

    static public function run()
    {
        $class_path = isset($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';
        if (!$class_path){
            CLIUtil::error('Class file path must be passed as first parameter.');
            CLIUtil::error('If the class file doesn\'t exist - it will be created.');
            exit;
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
            exit;
        }

        $field_name = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : '';
        if (!$field_name){
            CLIUtil::error('Class exists, you have to pass class field name as second parameter.');
            exit;
        }

        $class_file_obj = new PHPClassFile($class_path);
        $prop_names = $class_file_obj->getFieldNamesArr();

        if (!in_array($field_name, $prop_names)){
            $cli_add_field_obj = new CLIAddFieldToModel();

            echo "New field: " . $field_name . "\n";

            $datatype_name = isset($_SERVER['argv'][3]) ? $_SERVER['argv'][3] : '';

            if (!$datatype_name){
                CLIUtil::error('You have to pass new field data type as third parameter. Available data types:');
                foreach ($cli_add_field_obj->data_types as $data_type){
                    CLIUtil::error('- ' . $data_type->render());
                }
                exit;
            }

            $entered_datatype = null;
            foreach ($cli_add_field_obj->data_types as $datatype){
                if ($datatype->title == $datatype_name){
                    $entered_datatype = $datatype;
                }
            }

            if (!$datatype){
                CLIUtil::error('Datatype "' . $datatype_name . '" not found. Available data types:');
                foreach ($cli_add_field_obj->data_types as $data_type){
                    CLIUtil::error('- ' . $data_type->render());
                }
                exit;
            }

            $cli_add_field_obj->model_file_path = $class_path;
            $cli_add_field_obj->field_name = $field_name;
            $cli_add_field_obj->addFieldScreen($entered_datatype);
            exit;
        }

        echo "Field exists\n";

        $cli_add_field_obj = new CLIAddFieldToModel();
        $cli_add_field_obj->model_file_path = $class_path;
        $cli_add_field_obj->field_name = $field_name;
        $cli_add_field_obj->extraFieldFunctionsScreen();
    }
}