<?php

namespace OLOG\Model\CLI;

use OLOG\Assert;

class CLIAddFieldToModel
{
    protected $new_field_name = '';
    protected $model_file_path = ''; // полный путь к файлу модели
    protected $model_table_name = '';
    protected $model_db_id = '';

    public function selectFileName($folder){
        while (true) {
            $dirty_arr = scandir($folder);

            // убираем все элементы, которые начинаются с .
            $arr = [];
            foreach ($dirty_arr as $dir_item){
                if (!preg_match('@^\.@', $dir_item)){
                    $arr[] = $dir_item;
                }
            }

            echo "\n" . $folder . ":\n";

            foreach ($arr as $index => $item) {
                echo str_pad($index, 8, '.') . $item . "\n";
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
    
    public function askTableName($file_str){

        // extract model table name from class
        $table_name_pattern = '@const DB_TABLE_NAME = [\'\"](\w+)[\'\"]@';
        $matches = [];
        if (!preg_match($table_name_pattern, $file_str, $matches)){
            echo "table name not found\n";
            exit();
        }

        $model_table_name = $matches[1];

        // TODO: ask user to confirm model table name
        
        return $model_table_name;
    }

    public function askDbId($file_str){

        $model_db_id = '';

        // attempt to extract model table name from class

        $db_id_pattern = '@const DB_ID = [\'\"](\w+)[\'\"]@';
        $matches = [];
        if (!preg_match($db_id_pattern, $file_str, $matches)){
            echo "\nDB_ID constant not found in class or is not scalar. Enter model db id:\n";

            $model_db_id = trim(fgets(STDIN));
            // TODO: validate entered model_db_id
        } else {
            $model_db_id = $matches[1];
        }

        // TODO: ask user to confirm model db id?

        return $model_db_id;
    }

    public function addField(){
        echo "\nChoose model class file:\n";

        $this->model_file_path = $this->selectFileName(getcwd());
        echo "\nClass file: " . $this->model_file_path . "\n";

        echo "\nEnter new field name. Examples:\nnode_title\nmedia_id\n";
        $this->new_field_name = trim(fgets(STDIN));

        // TODO: check field_name format

        // TODO: check errors
        $file_str = file_get_contents($this->model_file_path);

        // TODO: more complex pattern?
        $pattern = '@protected \$id;@';
        if (!preg_match($pattern, $file_str)){
            echo "ID field not found\n";
            exit;
        }

        $this->model_table_name = $this->askTableName($file_str);


        // request field_data_type
        echo "\nEnter db field data type. Examples:\nint\ntext\nvarchar(255)\n";
        $field_data_type = trim(fgets(STDIN));
        // TODO: validate data_type



        // TODO: request default value
        // TODO: enable no default value
        echo "\nEnter field default value. If null - just press enter. Examples:\n0\n\"\"\n\"value\"\n";
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
        $file_str = preg_replace($pattern, 'protected $id;' . "\n" . '    protected $' . $this->new_field_name . $class_field_default_value_str . ';', $file_str);

        // TODO: write getter and setter

        // TODO: check errors
        file_put_contents($this->model_file_path, $file_str);

        echo "\nModel class file updated\n";


        // TODO: request field is nullable
        $field_is_nullable = '';

        echo "\nChoose whether database field is nullable:\n1: null\n2: not null\n"; // TODO: use constants
        $is_nullable_reply = trim(fgets(STDIN));

        switch ($is_nullable_reply){
            case 1: // TODO: use constant
                $field_is_nullable = '';
                break;

            case 2: // TODO: use constant
                $field_is_nullable = ' not null ';
                break;

            default:
                throw new \Exception('Unsupported answer');
        }


        //
        // adding sql
        //

        // TODO: extract model db id from class (что делать если там константа? запрашивать у пользователя)

        $this->model_db_id = $this->askDbId($file_str);
        // TODO: validate model_db_id


        $sql = 'alter table ' . $this->model_table_name . ' add column ' . $this->new_field_name . ' ' . $field_data_type . ' ' . $field_is_nullable . ' ' . $default_value_str . '  /* rand' . rand(0, 9999) . ' */;';

        CLIExecuteSql::addSqlToRegistry($this->model_db_id, $sql);

        echo "\nSQL registry updated\n";

        /*
        while (true){
            $this->askExtraFunctions();
        }
        */
    }

    /*
    const FUNCTION_CODE_ADD_UNIQUE_KEY = 1;

    public function askExtraFunctions(){
        echo "\nExtra functions:\n";
        echo self::FUNCTION_CODE_ADD_UNIQUE_KEY . ": create unique key for field\n";
        echo "2: create foreign key for field\n";
        echo "3: create index for key\n";
        echo "ENTER: exit\n"; // TODO: use constants

        $function_code = trim(fgets(STDIN));

        // TODO: check format

        switch ($function_code){
            case self::FUNCTION_CODE_ADD_UNIQUE_KEY:
                $this->addUniqueKey();
                break;

            case '':
                exit;
        }
    }
    */

    /*
    public function addUniqueKey(){
        Assert::assert($this->model_table_name);
        Assert::assert($this->new_field_name);
        Assert::assert($this->model_db_id);

        $sql = 'alter table ' . $this->model_table_name . ' add column ' . $this->new_field_name . ' ' . $field_data_type . ' ' . $field_is_nullable . ' ' . $default_value_str . ';';

        CLIExecuteSql::addSqlToRegistry($this->model_db_id, $sql);

        echo "\nSQL registry updated\n";

        while (true){
            $this->askExtraFunctions();
        }
    }
    */
}