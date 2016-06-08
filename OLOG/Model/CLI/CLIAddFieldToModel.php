<?php

namespace OLOG\Model\CLI;

use OLOG\Assert;
use OLOG\CliUtil;
use Stringy\Stringy;

class CLIAddFieldToModel
{
    protected $field_name = '';
    protected $db_table_field_name = '';
    protected $model_file_path = ''; // полный путь к файлу модели
    //protected $model_table_name = '';
    //protected $model_db_id = '';

    public function getTableNameFromClassFile()
    {
        $file_str = file_get_contents($this->model_file_path);
 
        // extract model table name from class
        $table_name_pattern = '@const DB_TABLE_NAME = [\'\"](\w+)[\'\"]@';
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

        $db_id_pattern = '@const DB_ID = [\'\"](\w+)[\'\"]@';
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

    public function askFieldName(){
        echo CliUtil::delimiter();
        echo "\nEnter field name. Examples:\n\tnode_title\n\tmedia_id\n";
        //$field_name = trim(fgets(STDIN));
        $field_name = CliUtil::readStdinAnswer();

        // TODO: check field_name format
        return $field_name;
    }

    public function addField()
    {
        echo CliUtil::delimiter();
        echo "\nChoose model class file:\n";
        $this->model_file_path = CLIFileSelector::selectFileName(getcwd());
        echo "\nClass file: " . $this->model_file_path . "\n";

        // TODO: check errors
        $file_str = file_get_contents($this->model_file_path);


        $class_name_matches = [];
        $class_name_pattern = '@\Rclass\s+(\w+)@';
        $class_name = '';
        if (preg_match($class_name_pattern, $file_str, $class_name_matches)) {
            $class_name = $class_name_matches[1];
        } else {
            echo "class name not found\n";
            exit;
        }

        $namespace_matches = [];
        $namespace_pattern = '@\Rnamespace\s+(\w+);@';
        $namespace = '';
        if (preg_match($namespace_pattern, $file_str, $namespace_matches)) {
            $namespace = $namespace_matches[1];
        }

        echo 'Class to be updated: ' . $namespace . "\\" . $class_name . "\n";


        // ask field name
        $this->field_name = $this->askFieldName();



        // check id field presence?

        // TODO: more complex pattern?
        $id_field_pattern = '@[ ]+protected \$id;@';
        if (!preg_match($id_field_pattern, $file_str)) {
            echo "ID field not found\n";
            exit;
        }



        // request field_data_type
        echo CliUtil::delimiter();
        echo "\nEnter db field data type. Examples:\n\tint\n\ttext\n\tvarchar(255)\n";
        //$field_data_type = trim(fgets(STDIN));
        $field_data_type = CliUtil::readStdinAnswer();
        // TODO: validate data_type


        // TODO: request default value
        // TODO: enable no default value
        echo CliUtil::delimiter();
        echo "\nEnter field default value: it will be used for class property and database field. If no default value - just press ENTER. Examples:\n\t0\n\t\"\"\n\t\"value\"\n";
        //$default_value = trim(fgets(STDIN));
        $default_value = CliUtil::readStdinAnswer();

        // TODO: check default value format

        // TODO: check whether default value matches field data type

        $class_field_default_value_str = '';
        $default_value_str = '';
        if ($default_value != '') {
            $class_field_default_value_str = ' = ' . $default_value;
            $default_value_str = ' default ' . $default_value;
        }

        // TODO: use field default value here
        // TODO: more clever whitespace before new field (the same as before id field)

        $replacement = '';

        // здесь поле id вставляется под новое поле, чтобы новые поля вставлялись над полем id, а новые метода - под ним
        // поле id как бы разделяет свойства и методы
        $replacement .= '    protected $' . $this->field_name . $class_field_default_value_str . ';' . "\n";
        $replacement .= '    protected $id;' . "\n";

        echo "\nCreate selector method for new field?\n\t1 Yes\n\tENTER No\n";
        $answer_create_selector = trim(fgets(STDIN));

        if ($answer_create_selector == 1) {
            $selector_template = self::selectorTemplate();
            $selector_template = self::replaceFieldVariables($selector_template, $this->field_name);
            $replacement .= $selector_template;
        }

        $gettersSettersTemplate = self::gettersSettersTemplate();
        $gettersSettersTemplate = self::replaceFieldVariables($gettersSettersTemplate, $this->field_name);
        $replacement .= $gettersSettersTemplate;

        $file_str = preg_replace($id_field_pattern, $replacement, $file_str);

        // TODO: write getter and setter

        // TODO: check errors
        file_put_contents($this->model_file_path, $file_str);

        echo "\nModel class file updated\n";


        // TODO: request field is nullable
        $field_is_nullable = '';

        echo CliUtil::delimiter();
        echo "\nChoose whether database field is nullable:\n\tn: null\n\tENTER: not null\n"; // TODO: use constants
        $is_nullable_reply = trim(fgets(STDIN));

        switch ($is_nullable_reply) {
            case 'n': // TODO: use constant
                $field_is_nullable = '';
                break;

            case '': // TODO: use constant
                $field_is_nullable = ' not null ';
                break;

            default:
                throw new \Exception('Unsupported answer');
        }


        //
        // adding sql
        //

        $model_db_id = $this->getDbIdFromClassFile();
        $model_table_name = $this->getTableNameFromClassFile();

        $this->db_table_field_name = $this->field_name;
        //$this->db_table_field_name = $namespace . "\\" . $class_name . "_" . $this->field_name;
        //$this->db_table_field_name = preg_replace('@\W@', '_', $this->db_table_field_name);
        //$this->db_table_field_name = strtolower($this->db_table_field_name);

        $sql = 'alter table ' . $model_table_name . ' add column ' . $this->db_table_field_name . ' ' . $field_data_type . ' ' . $field_is_nullable . ' ' . $default_value_str . '  /* rand' . rand(0, 999999) . ' */;';

        CLIExecuteSql::addSqlToRegistry($model_db_id, $sql);

        echo "\nSQL registry updated\n";

        echo CliUtil::delimiter();
        echo "\nPress ENTER to execure SQL queries, enter n to skip:\n";
        $command_str = CliUtil::readStdinAnswer();

        if ($command_str == ''){
            CLIExecuteSql::executeSqlScreen();
        }

        $this->extraFieldFunctionsScreen();
    }

    static public function replaceFieldVariables($str, $field_name){
        $camelized_field_name = Stringy::create($field_name)->upperCamelize();

        $str = str_replace('#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#', $camelized_field_name, $str);
        $str = str_replace('#FIELDTEMPLATE_FIELD_NAME#', $field_name, $str);

        return $str;
    }

    const FUNCTION_CODE_ADD_UNIQUE_KEY = 1;
    const FUNCTION_ADD_FOREIGN_KEY = 2;

    public function extraFieldFunctionsScreen()
    {
        if (!$this->model_file_path){
            echo "\nChoose model class file:\n";
            $this->model_file_path = CLIFileSelector::selectFileName(getcwd());
            echo "\nClass file: " . $this->model_file_path . "\n";
        }

        if (!$this->field_name) {
            $this->field_name = $this->askFieldName();
        }

        Assert::assert($this->model_file_path);
        Assert::assert($this->field_name);

        echo CliUtil::delimiter();
        echo "\nExtra functions:\n";
        echo "\t" . self::FUNCTION_CODE_ADD_UNIQUE_KEY . ": create unique key for field\n";
        echo "\t" . self::FUNCTION_ADD_FOREIGN_KEY . ": create foreign key for field\n";
        //echo "\t" . "3: create index for key\n";
        echo "\t" . "ENTER: exit\n"; // TODO: use constants

        $function_code = trim(fgets(STDIN));

        // TODO: check format

        switch ($function_code) {
            case self::FUNCTION_CODE_ADD_UNIQUE_KEY:
                $this->addUniqueKey();
                exit;

            case self::FUNCTION_ADD_FOREIGN_KEY:
                $this->addForeignKey();
                exit;

            case '':
                exit;
        }
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

        $sql = 'alter table ' . $model_table_name . ' add constraint FK_' . $this->field_name . '_' . rand(0, 999999) . ' foreign key (' . $this->field_name . ')  references ' . $target_table_name . ' (' . $target_field_name . ') /* rand' . rand(0, 999999) . ' */;';

        CLIExecuteSql::addSqlToRegistry($model_db_id, $sql);

        echo "\nSQL registry updated\n";
    }

    static public function selectorTemplate(){
        return <<<'EOT'

    static public function getIdsArrFor#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#ByCreatedAtDesc($value){
        $ids_arr = \OLOG\DB\DBWrapper::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where #FIELDTEMPLATE_FIELD_NAME# = ? order by created_at_ts desc',
            array($value)
        );
        return $ids_arr;
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