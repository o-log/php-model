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
        echo "\nChoose model class file:\n";

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

        // TODO: ask user to confirm model table name


        // request field_data_type
        echo "\nEnter db field data type\n";
        echo "Examples:\nint\ntext\nvarchar(255)\n";
        $field_data_type = trim(fgets(STDIN));
        // TODO: validate data_type



        // TODO: request default value
        // TODO: enable no default value
        echo "\nEnter field default value. If null - just press enter.\n";
        echo "Examples:\n0\n\"\"\n\"value\"\n";
        $default_value = trim(fgets(STDIN));

        // TODO: check default value format



        $class_field_default_value_str = '';
        $default_value_str = '';
        if ($default_value != ''){
            $class_field_default_value_str = ' = ' . $default_value;
            $default_value_str = ' default ' . $default_value;
        }

        // TODO: use field default value here
        // TODO: more clever whitespace before new field (the same as before id field)
        $file_str = preg_replace($pattern, 'protected $id;' . "\n" . '    protected $' . $field_name . $class_field_default_value_str . ';', $file_str);

        // TODO: write getter and setter

        file_put_contents($filename, $file_str);

        echo "\nModel class file updated\n";


        // TODO: request field is nullable
        $field_is_nullable = '';

        echo "\nChoose whether database field is nullable:\n1 null\n2 not null\n";
        $is_nullable_reply = trim(fgets(STDIN));

        switch ($is_nullable_reply){
            case 1:
                $field_is_nullable = '';
                break;

            case 2:
                $field_is_nullable = ' not null ';
                break;

            default:
                throw new \Exception('Unsupported answer');
        }


        //
        // adding sql
        //

        // TODO: extract model db id from class (что делать если там константа? запрашивать у пользователя)

        // request model_db_id
        echo "enter model db id:\n";
        $model_db_id = trim(fgets(STDIN));
        // TODO: validate model_db_id


        $sql = 'alter table ' . $model_table_name . ' add column ' . $field_name . ' ' . $field_data_type . ' ' . $field_is_nullable . ' ' . $default_value_str . ';';

        CLIExecuteSql::addSqlToRegistry($model_db_id, $sql);

        echo "\nSQL registry updated\n";

        echo "\nExtra functions:\n1 create unique key for field\n2 create foreign key for field\n3 create index for key";
    }
}