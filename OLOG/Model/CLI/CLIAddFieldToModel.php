<?php

namespace OLOG\Model\CLI;

use OLOG\Assert;
use Stringy\Stringy;

class CLIAddFieldToModel
{
    protected $new_field_name = '';
    protected $model_file_path = ''; // полный путь к файлу модели
    protected $model_table_name = '';
    protected $model_db_id = '';

    public function askTableName($file_str)
    {

        // extract model table name from class
        $table_name_pattern = '@const DB_TABLE_NAME = [\'\"](\w+)[\'\"]@';
        $matches = [];
        if (!preg_match($table_name_pattern, $file_str, $matches)) {
            echo "table name not found\n";
            exit();
        }

        $model_table_name = $matches[1];

        // TODO: ask user to confirm model table name

        return $model_table_name;
    }

    public function askDbId($file_str)
    {

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

        return $model_db_id;
    }

    public function addField()
    {
        echo "\nChoose model class file:\n";

        $this->model_file_path = CLIFileSelector::selectFileName(getcwd());
        echo "\nClass file: " . $this->model_file_path . "\n";

        // ask field name

        echo "\nEnter new field name. Examples:\n\tnode_title\n\tmedia_id\n";
        $this->new_field_name = trim(fgets(STDIN));

        // TODO: check field_name format

        // TODO: check errors
        $file_str = file_get_contents($this->model_file_path);

        // TODO: more complex pattern?
        $pattern = '@[ ]+protected \$id;@';
        if (!preg_match($pattern, $file_str)) {
            echo "ID field not found\n";
            exit;
        }

        $this->model_table_name = $this->askTableName($file_str);


        // request field_data_type
        echo "\nEnter db field data type. Examples:\n\tint\n\ttext\n\tvarchar(255)\n";
        $field_data_type = trim(fgets(STDIN));
        // TODO: validate data_type


        // TODO: request default value
        // TODO: enable no default value
        echo "\nEnter field default value: it will be used for class property and database field. If no default value - just press ENTER. Examples:\n\t0\n\t\"\"\n\t\"value\"\n";
        $default_value = trim(fgets(STDIN));

        // TODO: check default value format

        // TODO: check whether default value matched field data type

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
        $replacement .= '    protected $' . $this->new_field_name . $class_field_default_value_str . ';' . "\n";
        $replacement .= '    protected $id;' . "\n";

        echo "\nCreate selector method for new field?\n\t1 Yes\n\tENTER No\n";
        $answer_create_selector = trim(fgets(STDIN));

        if ($answer_create_selector == 1) {
            $selector_template = self::selectorTemplate();
            $selector_template = self::replaceFieldVariables($selector_template, $this->new_field_name);
            $replacement .= $selector_template;
        }

        $gettersSettersTemplate = self::gettersSettersTemplate();
        $gettersSettersTemplate = self::replaceFieldVariables($gettersSettersTemplate, $this->new_field_name);
        $replacement .= $gettersSettersTemplate;

        $file_str = preg_replace($pattern, $replacement, $file_str);

        // TODO: write getter and setter

        // TODO: check errors
        file_put_contents($this->model_file_path, $file_str);

        echo "\nModel class file updated\n";


        // TODO: request field is nullable
        $field_is_nullable = '';

        echo "\nChoose whether database field is nullable:\n1: null\n2: not null\n"; // TODO: use constants
        $is_nullable_reply = trim(fgets(STDIN));

        switch ($is_nullable_reply) {
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


        $sql = 'alter table ' . $this->model_table_name . ' add column ' . $this->new_field_name . ' ' . $field_data_type . ' ' . $field_is_nullable . ' ' . $default_value_str . '  /* rand' . rand(0, 999999) . ' */;';

        CLIExecuteSql::addSqlToRegistry($this->model_db_id, $sql);

        echo "\nSQL registry updated\n";

        $this->askExtraFunctions();
    }

    static public function replaceFieldVariables($str, $field_name){
        $camelized_field_name = Stringy::create($field_name)->upperCamelize();

        $str = str_replace('#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#', $camelized_field_name, $str);
        $str = str_replace('#FIELDTEMPLATE_FIELD_NAME#', $field_name, $str);

        return $str;
    }

    const FUNCTION_CODE_ADD_UNIQUE_KEY = 1;
    const FUNCTION_ADD_FOREIGN_KEY = 2;

    public function askExtraFunctions()
    {

        // TODO: сейчас все это работает для только что созданного поля, нужно добавить возможность выбора произвольного поля чтобы можно было просто создавать индексы и т.п.
        Assert::assert($this->model_table_name);
        Assert::assert($this->new_field_name);
        Assert::assert($this->model_db_id);

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
        Assert::assert($this->model_table_name);
        Assert::assert($this->new_field_name);
        Assert::assert($this->model_db_id);

        $sql = 'alter table ' . $this->model_table_name . ' add unique key UK_' . $this->new_field_name . '_' . rand(0, 999999) . ' (' . $this->new_field_name . ')  /* rand' . rand(0, 999999) . ' */;';

        CLIExecuteSql::addSqlToRegistry($this->model_db_id, $sql);

        echo "\nSQL registry updated\n";
    }

    public function addForeignKey()
    {
        Assert::assert($this->model_table_name);
        Assert::assert($this->new_field_name);
        Assert::assert($this->model_db_id);

        // TODO: select model instead of table name?

        echo "Enter target db table name:\n";
        $target_table_name = trim(fgets(STDIN));

        // TODO: check table name format

        // TODO: select from target model fields?
        echo "Enter target db table field name:\n";
        $target_field_name = trim(fgets(STDIN));

        // TODO: check field name format

        $sql = 'alter table ' . $this->model_table_name . ' add foreign key FK_' . $this->new_field_name . '_' . rand(0, 999999) . ' (' . $this->new_field_name . ')  references ' . $target_table_name . ' (' . $target_field_name . ') /* rand' . rand(0, 999999) . ' */;';

        CLIExecuteSql::addSqlToRegistry($this->model_db_id, $sql);

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
        return $this->get#FIELDTEMPLATE_FIELD_NAME#;
    }

    public function set#FIELDTEMPLATE_CAMELIZED_FIELD_NAME#($value){
        $this->#FIELDTEMPLATE_FIELD_NAME# = $value;
    }

EOT;
    }

}