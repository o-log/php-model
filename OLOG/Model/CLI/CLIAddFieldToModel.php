<?php

namespace OLOG\Model\CLI;

class CLIAddFieldToModel
{
    static public function selectFileName($folder){
        while (true) {
            $arr = scandir($folder);

            echo "\n" . $folder . ":\n";

            foreach ($arr as $index => $item) {
                echo $index . "\t" . $item . "\n";
            }

            echo "\nEnter file or directory index:\n";
            $index = trim(fgets(STDIN));

            if (!array_key_exists($index, $arr)) {
                echo "Index not found\n";
                continue;
            }

            $selected_path = $folder . DIRECTORY_SEPARATOR . $arr[$index];

            if (is_dir($selected_path)){
                $folder = $selected_path;
                continue;
            }

            return $selected_path;
        }
    }

    static public function addField(){
        $filename = self::selectFileName(getcwd());

        echo "selected file: " . $filename . "\n";

        echo "\nEnter new field name:\n";
        echo "Example: node_title\n";
        $field_name = trim(fgets(STDIN));

        // TODO: check field_name format

        $file_str = file_get_contents($filename);

        // TODO: more complex pattern?
        $pattern = '@protected \$id;@';
        if (!preg_match($pattern, $file_str)){
            echo "ID field not found\n";
            exit;
        }

        $model_table_name = '';

        // extract model table name from class
        $table_name_pattern = '@const DB_TABLE_NAME = [\'\"](\w+)[\'\"]@';
        $matches = [];
        if (!preg_match($table_name_pattern, $file_str, $matches)){
            echo "table name not found\n";
            exit();
        }

        $model_table_name = $matches[1];

        // TODO: extract model db id from class (что делать если там константа? запрашивать у пользователя?)

        // TODO: use field default value here
        $file_str = preg_replace($pattern, 'protected $id;' . "\n" . 'protected $' . $field_name . ';', $file_str);

        // TODO: write getter and setter

        file_put_contents($filename, $file_str);

        //
        // adding sql
        //

        // request model_db_id
        echo "enter model db id:\n";
        $model_db_id = trim(fgets(STDIN));
        // TODO: validate model_db_id

        // request field_data_type
        echo "enter db field data type\n";
        echo "Example: varchar(255)\n";
        $field_data_type = trim(fgets(STDIN));
        // TODO: validate data_type

        // TODO: request field is nullable
        $field_is_nullable = ' not null ';

        // TODO: request default value
        // TODO: enable no default value
        $default_value = '""';
        $default_value_str = ' default ' . $default_value;

        $sql = 'alter table ' . $model_table_name . ' add column ' . $field_name . ' ' . $field_data_type . ' ' . $field_is_nullable . ' ' . $default_value_str . ';';


        CLIExecuteSql::addSqlToRegistry($model_db_id, $sql);
    }
}