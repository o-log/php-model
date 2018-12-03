<?php

namespace OLOG\Model\CLI;

use OLOG\CLIUtil;
use Stringy\Stringy;

class CLIAddFieldToModel
{
    const FUNCTION_CODE_ADD_UNIQUE_KEY = 1;
    const FUNCTION_ADD_FOREIGN_KEY = 2;
    const FUNCTION_ADD_SELECTOR = 3;

    public $field_name = '';
    protected $db_table_field_name = '';
    public $model_file_path = ''; // полный путь к файлу модели

    /**
     * @var FieldDataType[]
     */
    public $data_types;

    public function __construct()
    {
        $data_types_arr = [];

        $data_types_arr[] = new FieldDataType('tinyint', 'tinyint', true, true, false);
        $data_types_arr[] = new FieldDataType('int', 'int', true, true, false);
        $data_types_arr[] = new FieldDataType('string', 'varchar(255)', true, true, true);
        $data_types_arr[] = new FieldDataType('text', 'text', false, false, false);
        $data_types_arr[] = new FieldDataType('date', 'date', true, true, false);
        $data_types_arr[] = new FieldDataType('datetime', 'datetime', true, true, false);
        $data_types_arr[] = new FieldDataType('bigint', 'bigint', true, true, false);

        $this->data_types = $data_types_arr;
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
        echo CLIUtil::delimiter();
        echo "Enter field default value: it will be used for class property and database field. If no default value - just press ENTER. Examples:\n\t0\n\t\"\"\n\t\"some_value\"\n";
        $default_value = CLIUtil::readStdinAnswer();

        // TODO: check default value format

        // TODO: check whether default value matches field data type

        return $default_value;
    }

    /**
     * @param FieldDataType $field_data_type
     * @return string
     */
    public function askCollate($field_data_type){

        if (!$field_data_type->can_have_collate){
            return '';
        }

        echo CLIUtil::delimiter();

        // alter table imbalance_match change column url url varchar(255) collate utf8_bin not null /* 873468753645 */

        echo "Enter database field collate, press ENTER to leave default. Examples:\n\tutf8_bin - for url fields to make them case sensitive\n";
        $collate = CLIUtil::readStdinAnswer();

        // TODO: check format

        // TODO: check whether value matches field data type

        return $collate;
    }

    static public function constantNameForFieldName($field_name){
        return '_' . strtoupper($field_name);
    }

    public function addFieldScreen($field_data_type)
    {
        $class_file_obj = new PHPClassFile($this->model_file_path);

        /** @var FieldDataType $field_data_type */
        //$field_data_type = $this->askDataType();

        $default_value = $this->askDefaultValue($field_data_type);
        $collate = $this->askCollate($field_data_type);

        //
        //
        //

        $class_field_default_value_str = '';
        $sql_default_value_str = '';
        $sql_collate_str = '';

        if ($default_value != '') {
            $class_field_default_value_str = ' = ' . $default_value;
            $sql_default_value_str = ' default ' . $default_value;
        }

        if ($collate != '') {
            $sql_collate_str = ' collate ' . $collate;
        }

        $field_string_for_class = '    const ' . self::constantNameForFieldName($this->field_name) . ' = \'' . $this->field_name . '\';' . "\n";

        // no naive getters and setters, so make field public
        //$field_string_for_class .= '    protected $' . $this->field_name . $class_field_default_value_str . ';' . "\n";
        $field_string_for_class .= '    public $' . $this->field_name . $class_field_default_value_str . ';' . "\n";
        $class_file_obj->insertAboveIdField($field_string_for_class);

        // no naive getters and setters
        //$getters_setters_template = self::gettersSettersTemplate();
        //$getters_setters_template = self::replaceFieldNamePlaceholders($getters_setters_template, $this->field_name);
        //$class_file_obj->insertBelowIdField($getters_setters_template);

        $class_file_obj->save();

        echo "\nModel class file updated\n";

        //
        //
        //

        $sql_field_is_nullable_str = '';

        if ($field_data_type->can_be_null) {
            echo CLIUtil::delimiter();
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

        $model_db_id = $class_file_obj->model_db_id;
        $model_table_name = $class_file_obj->model_table_name;

        $this->db_table_field_name = $this->field_name;
        $sql = 'alter table ' . $model_table_name . ' add column ' . $this->db_table_field_name . ' ' . $field_data_type->sql_type_name . ' ' . $sql_collate_str . ' ' . $sql_field_is_nullable_str . ' ' . $sql_default_value_str . '  /* rand' . rand(0, 999999) . ' */;';

        \OLOG\DB\Migrate::addMigration($model_db_id, $sql);

        echo "\nSQL registry updated\n";

        echo CLIUtil::delimiter();
        echo "Press ENTER to execure SQL queries, enter n to skip:\n";
        $command_str = CLIUtil::readStdinAnswer();

        if ($command_str == ''){
            \OLOG\DB\MigrateCLI::run();
        }

        $this->extraFieldFunctionsScreen();
    }

    static public function replaceFieldNamePlaceholders($str, $field_name){
        $camelized_field_name = Stringy::create($field_name)->upperCamelize();

        $str = str_replace('#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#', $camelized_field_name, $str);
        $str = str_replace('#FIELDTEMPLATE_FIELD_NAME#', $field_name, $str);
        $str = str_replace('#FIELDTEMPLATE_FIELD_CONSTANT#', self::constantNameForFieldName($field_name), $str);

        return $str;
    }

    static public function replacePageSizePlaceholders($str, $page_size){
        $str = str_replace('#SELECTOR_PAGE_SIZE#', $page_size, $str);

        return $str;
    }

    static public function replaceClassNamePlaceholders($str, $class_name){
        $str = str_replace('#CLASS_NAME#', $class_name, $str);

        return $str;
    }

    public function extraFieldFunctionsScreen()
    {
        if (!$this->model_file_path) throw new \Exception();
        if (!$this->field_name) throw new \Exception();

        while (true) {
            echo CLIUtil::delimiter();

            echo "Extra functions:\n";
            echo "\t" . self::FUNCTION_CODE_ADD_UNIQUE_KEY . ": create unique key for field\n";
            echo "\t" . self::FUNCTION_ADD_FOREIGN_KEY . ": create foreign key for field\n";
            echo "\t" . self::FUNCTION_ADD_SELECTOR . ": create selector method for field\n";

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
        if (!$this->field_name) throw new \Exception();
        if (!$this->model_file_path) throw new \Exception();

        echo "Enter page size for selector, press ENTER to leave default (30):\n";
        $page_size = trim(fgets(STDIN));

        if ($page_size == ''){
            $page_size = '30';
        }

        // TODO: check page size validity

        $class_file_obj = new PHPClassFile($this->model_file_path);

        $selector_template = self::selectorTemplate();
        $selector_template = self::replaceFieldNamePlaceholders($selector_template, $this->field_name);
        $selector_template = self::replacePageSizePlaceholders($selector_template, $page_size);
        $selector_template = self::replaceClassNamePlaceholders($selector_template, $class_file_obj->class_name);
        $class_file_obj->insertBelowIdField($selector_template);

        $class_file_obj->save();

        echo "\nClass file updated\n";

        $model_db_id = $class_file_obj->model_db_id;
        $model_table_name = $class_file_obj->model_table_name;

        if (!$model_table_name) throw new \Exception();
        if (!$model_db_id) throw new \Exception();

        $sql = 'alter table ' . $model_table_name . ' add index INDEX_' . $this->field_name . '_' . rand(0, 99999999) . ' (' . $this->field_name . ', created_at_ts)  /* rand' . rand(0, 999999) . ' */;';

        \OLOG\DB\Migrate::addMigration($model_db_id, $sql);

        echo "\nSQL registry updated\n";
    }

    public function addUniqueKey()
    {
        if (!$this->field_name) throw new \Exception();
        if (!$this->model_file_path) throw new \Exception();

        $class_file_obj = new PHPClassFile($this->model_file_path);
        $model_db_id = $class_file_obj->model_db_id;
        $model_table_name = $class_file_obj->model_table_name;

        if (!$model_table_name) throw new \Exception();
        if (!$model_db_id) throw new \Exception();

        $sql = 'alter table ' . $model_table_name . ' add unique key UK_' . $this->field_name . '_' . rand(0, 999999) . ' (' . $this->field_name . ')  /* rand' . rand(0, 999999) . ' */;';

        \OLOG\DB\Migrate::addMigration($model_db_id, $sql);

        echo "\nSQL registry updated\n";
    }

    public function addForeignKey()
    {
        if (!$this->field_name) throw new \Exception();
        if (!$this->model_file_path) throw new \Exception();

        $class_file_obj = new PHPClassFile($this->model_file_path);
        $model_db_id = $class_file_obj->model_db_id;
        $model_table_name = $class_file_obj->model_table_name;

        if (!$model_table_name) throw new \Exception();
        if (!$model_db_id) throw new \Exception();

        // TODO: select model instead of table name?

        echo "Enter target db table name:\n";
        $target_table_name = trim(fgets(STDIN));

        // TODO: check table name format

        // TODO: select from target model fields?
        echo "Enter target db table field name, press ENTER to leave default (id):\n";
        $target_field_name = trim(fgets(STDIN));

        if ($target_field_name == ''){
            $target_field_name = 'id';
        }

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

        \OLOG\DB\Migrate::addMigration($model_db_id, $sql);

        echo "\nSQL registry updated\n";
    }

    static public function selectorTemplate(){
        return <<<'EOT'

    /**
     * @return #CLASS_NAME#[]
     */
    static public function for#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#($#FIELDTEMPLATE_FIELD_NAME#, int $limit = #SELECTOR_PAGE_SIZE#, int $offset = 0): array {
        return self::idsToObjs(self::idsFor#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#($#FIELDTEMPLATE_FIELD_NAME#, $limit, $offset));
    }

    static public function idsFor#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#($#FIELDTEMPLATE_FIELD_NAME#, $limit = #SELECTOR_PAGE_SIZE#, $offset = 0): array {
        if (is_null($#FIELDTEMPLATE_FIELD_NAME#)){
            throw new \Exception('NULL values not supported in selector.');
        }

        return \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME .
            ' where ' . self::#FIELDTEMPLATE_FIELD_CONSTANT# . '=?' .
            ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$#FIELDTEMPLATE_FIELD_NAME#, $limit, $offset]
        );
    }

EOT;
    }
}
