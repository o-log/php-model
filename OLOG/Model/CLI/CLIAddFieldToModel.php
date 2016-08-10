<?php

namespace OLOG\Model\CLI;

use OLOG\Assert;
use OLOG\CliUtil;
use Stringy\Stringy;

class CLIAddFieldToModel
{
    const FUNCTION_CODE_ADD_UNIQUE_KEY = 1;
    const FUNCTION_ADD_FOREIGN_KEY = 2;
    const FUNCTION_ADD_SELECTOR = 3;

    protected $field_name = '';
    protected $db_table_field_name = '';
    protected $model_file_path = ''; // полный путь к файлу модели

    public function getTableNameFromClassFile()
    {
        $file_str = file_get_contents($this->model_file_path);
 
        // extract model table name from class
        $table_name_pattern = '@const[\h]+DB_TABLE_NAME[\h]+=[\h]+[\'\"](\w+)[\'\"]@';
        $matches = [];
        if (!preg_match($table_name_pattern, $file_str, $matches)) {
            throw new \Exception("table name not found in class file");
        }

        $model_table_name = $matches[1];

        // TODO: ask user to confirm model table name?

        return $model_table_name;
    }

    public function getDbIdFromClassFile()
    {
        $file_str = file_get_contents($this->model_file_path);

        $model_db_id = '';

        // attempt to extract model table name from class

        $db_id_pattern = '@const[\h]+DB_ID[\h]+=[\h]+[\'\"](\w+)[\'\"]@';
        $matches = [];
        if (!preg_match($db_id_pattern, $file_str, $matches)) {
            echo "\nDB_ID constant not found in class or is not scalar. Enter model db id:\n";

            $model_db_id = trim(fgets(STDIN));
            // TODO: validate entered model_db_id
        } else {
            $model_db_id = $matches[1];
        }

        // TODO: ask user to confirm model db id?

        // TODO: validate model_db_id (presence in config)

        return $model_db_id;
    }

    /**
     * @param PHPClassFile $class_file_obj
     * @return string
     */
    public function askFieldName($class_file_obj){
        echo CliUtil::delimiter();
        echo "Enter field name. Examples for new field names:\n\tnode_title\n\tmedia_id\n";

        //$class_field_names_arr = $class_file_obj->getFieldNamesArr();
        //echo "\nFields in class:\n";

        $field_name = CliUtil::readStdinAnswer();

        // TODO: check field_name format
        return $field_name;
    }

    public function askDataType(){
        echo CliUtil::delimiter();

        $data_types_arr = [];

        $data_types_arr[] = new FieldDataType('tinyint', true, true);
        $data_types_arr[] = new FieldDataType('int', true, true);
        $data_types_arr[] = new FieldDataType('varchar(255)', true, true);
        $data_types_arr[] = new FieldDataType('text', false, false);
        $data_types_arr[] = new FieldDataType('date', true, true);
        $data_types_arr[] = new FieldDataType('datetime', true, true);

        echo "Enter db field data type:\n";
        /**
         * @var  $index
         * @var FieldDataType $data_type_obj
         */
        foreach ($data_types_arr as $index => $data_type_obj){
            echo "\t" . $index . '. ' . $data_type_obj->sql_type_name . "\n";
        }

        $data_type_index = CliUtil::readStdinAnswer();

        if (!array_key_exists($data_type_index, $data_types_arr)){
            throw new \Exception('wrong answer');
        }

        $selected_data_type_obj = $data_types_arr[$data_type_index];

        return $selected_data_type_obj;
    }

    /**
     * @param FieldDataType $field_data_type
     * @return string
     */
    public function askDefaultValue($field_data_type){

        if (!$field_data_type->can_have_default_value){
            return '';
        }

        // TODO: request default value
        echo CliUtil::delimiter();
        echo "Enter field default value: it will be used for class property and database field. If no default value - just press ENTER. Examples:\n\t0\n\t\"\"\n\t\"some_value\"\n";
        $default_value = CliUtil::readStdinAnswer();

        // TODO: check default value format

        // TODO: check whether default value matches field data type

        return $default_value;
    }

    public function addField()
    {
        echo CliUtil::delimiter();
        echo "Choose model class file:\n";
        $this->model_file_path = CLIFileSelector::selectFileName(getcwd());
        echo "\nClass file: " . $this->model_file_path . "\n";

        $class_file_obj = new PHPClassFile($this->model_file_path);
        echo 'Class to be updated: ' . $class_file_obj->class_namespace . "\\" . $class_file_obj->class_name . "\n";

        $this->field_name = $this->askFieldName($class_file_obj);

        /** @var FieldDataType $field_data_type */
        $field_data_type = $this->askDataType();

        $default_value = $this->askDefaultValue($field_data_type);

        //
        //
        //

        $class_field_default_value_str = '';
        $sql_default_value_str = '';

        if ($default_value != '') {
            $class_field_default_value_str = ' = ' . $default_value;
            $sql_default_value_str = ' default ' . $default_value;
        }

        $field_string_for_class = '    protected $' . $this->field_name . $class_field_default_value_str . ';' . "\n";
        $class_file_obj->insertAboveIdField($field_string_for_class);

        $getters_setters_template = self::gettersSettersTemplate();
        $getters_setters_template = self::replaceFieldNamePlaceholders($getters_setters_template, $this->field_name);
        $class_file_obj->insertBelowIdField($getters_setters_template);

        $class_file_obj->save();

        echo "\nModel class file updated\n";

        //
        //
        //

        $sql_field_is_nullable_str = '';

        if ($field_data_type->can_be_null) {
            echo CliUtil::delimiter();
            echo "Choose whether database field is nullable:\n\tn: null\n\tENTER: not null\n"; // TODO: use constants
            $is_nullable_reply = trim(fgets(STDIN));

            switch ($is_nullable_reply) {
                case 'n': // TODO: use constant
                    $sql_field_is_nullable_str = '';
                    break;

                case '': // TODO: use constant
                    $sql_field_is_nullable_str = ' not null ';
                    break;

                default:
                    throw new \Exception('Unsupported answer');
            }
        }

        //
        // adding sql
        //

        $model_db_id = $this->getDbIdFromClassFile();
        $model_table_name = $this->getTableNameFromClassFile();

        $this->db_table_field_name = $this->field_name;
        $sql = 'alter table ' . $model_table_name . ' add column ' . $this->db_table_field_name . ' ' . $field_data_type->sql_type_name . ' ' . $sql_field_is_nullable_str . ' ' . $sql_default_value_str . '  /* rand' . rand(0, 999999) . ' */;';

        CLIExecuteSql::addSqlToRegistry($model_db_id, $sql);

        echo "\nSQL registry updated\n";

        echo CliUtil::delimiter();
        echo "Press ENTER to execure SQL queries, enter n to skip:\n";
        $command_str = CliUtil::readStdinAnswer();

        if ($command_str == ''){
            CLIExecuteSql::executeSqlScreen();
        }

        $this->extraFieldFunctionsScreen();
    }

    static public function replaceFieldNamePlaceholders($str, $field_name){
        $camelized_field_name = Stringy::create($field_name)->upperCamelize();

        $str = str_replace('#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#', $camelized_field_name, $str);
        $str = str_replace('#FIELDTEMPLATE_FIELD_NAME#', $field_name, $str);

        return $str;
    }

    public function extraFieldFunctionsScreen()
    {
        if (!$this->model_file_path){
            echo "\nChoose model class file:\n";
            $this->model_file_path = CLIFileSelector::selectFileName(getcwd());
            echo "\nClass file: " . $this->model_file_path . "\n";
        }

        $class_file_obj = new PHPClassFile($this->model_file_path);

        if (!$this->field_name) {
            $this->field_name = $this->askFieldName($class_file_obj);
        }

        Assert::assert($this->model_file_path);
        Assert::assert($this->field_name);

        while (true) {
            echo CliUtil::delimiter();

            echo "Extra functions:\n";
            echo "\t" . self::FUNCTION_CODE_ADD_UNIQUE_KEY . ": create unique key for field\n";
            echo "\t" . self::FUNCTION_ADD_FOREIGN_KEY . ": create foreign key for field\n";
            echo "\t" . self::FUNCTION_ADD_SELECTOR . ": create selector for field\n";

            echo "\t" . "ENTER: exit to menu\n"; // TODO: use constants

            $function_code = trim(fgets(STDIN));

            // TODO: check format

            switch ($function_code) {
                case self::FUNCTION_CODE_ADD_UNIQUE_KEY:
                    $this->addUniqueKey();
                    break;

                case self::FUNCTION_ADD_FOREIGN_KEY:
                    $this->addForeignKey();
                    break;

                case self::FUNCTION_ADD_SELECTOR:
                    $this->addSelector();
                    break;

                case '':
                    return;
            }
        }
    }

    public function addSelector()
    {
        Assert::assert($this->field_name);
        Assert::assert($this->model_file_path);

        $class_file_obj = new PHPClassFile($this->model_file_path);

        $selector_template = self::selectorTemplate();
        $selector_template = self::replaceFieldNamePlaceholders($selector_template, $this->field_name);
        $class_file_obj->insertBelowIdField($selector_template);

        $class_file_obj->save();

        echo "\nClass file updated\n";
    }

    public function addUniqueKey()
    {
        Assert::assert($this->field_name);
        Assert::assert($this->model_file_path);

        $model_db_id = $this->getDbIdFromClassFile();
        $model_table_name = $this->getTableNameFromClassFile();

        Assert::assert($model_table_name);
        Assert::assert($model_db_id);

        $sql = 'alter table ' . $model_table_name . ' add unique key UK_' . $this->field_name . '_' . rand(0, 999999) . ' (' . $this->field_name . ')  /* rand' . rand(0, 999999) . ' */;';

        CLIExecuteSql::addSqlToRegistry($model_db_id, $sql);

        echo "\nSQL registry updated\n";
    }

    public function addForeignKey()
    {
        Assert::assert($this->field_name);
        Assert::assert($this->model_file_path);

        $model_db_id = $this->getDbIdFromClassFile();
        $model_table_name = $this->getTableNameFromClassFile();

        Assert::assert($model_table_name);
        Assert::assert($model_db_id);

        // TODO: select model instead of table name?

        echo "Enter target db table name:\n";
        $target_table_name = trim(fgets(STDIN));

        // TODO: check table name format

        // TODO: select from target model fields?
        echo "Enter target db table field name:\n";
        $target_field_name = trim(fgets(STDIN));

        // TODO: check field name format

        $on_delete_action = '';

        echo "Choose foreign key action on delete:\n";
        echo "\tc: cascade (delete this model when deleting referenced model)\n";
        echo "\tENTER: default action (restrict deleting referenced model)\n";
        $delete_action_code = trim(fgets(STDIN));
        if ($delete_action_code == 'c'){
            $on_delete_action = ' on delete cascade ';
        }

        $sql = 'alter table ' . $model_table_name . ' add constraint FK_' . $this->field_name . '_' . rand(0, 999999) . ' foreign key (' . $this->field_name . ')  references ' . $target_table_name . ' (' . $target_field_name . ') ' . $on_delete_action . '/* rand' . rand(0, 999999) . ' */;';

        CLIExecuteSql::addSqlToRegistry($model_db_id, $sql);

        echo "\nSQL registry updated\n";
    }

    static public function selectorTemplate(){
        return <<<'EOT'

    static public function getIdsArrFor#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#ByCreatedAtDesc($value, $offset = 0, $page_size = 30){
        if (is_null($value)) {
            return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where #FIELDTEMPLATE_FIELD_NAME# is null order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
            );
        } else {
            return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where #FIELDTEMPLATE_FIELD_NAME# = ? order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset),
                array($value)
            );
        }
    }

EOT;
    }

    static public function gettersSettersTemplate()
    {
        return <<<'EOT'

    public function get#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#(){
        return $this->#FIELDTEMPLATE_FIELD_NAME#;
    }

    public function set#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#($value){
        $this->#FIELDTEMPLATE_FIELD_NAME# = $value;
    }

EOT;
    }

}