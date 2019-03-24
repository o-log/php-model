<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace OLOG\Model\CLI;

use function foo\func;

class PHPClassFile
{
    public $class_file_path;
    public $class_file_text;
    public $class_name;
    public $class_namespace = '';
    public $model_table_name = '';
    public $model_db_id = '';

    static public $id_field_pattern = '@[\h]+(public|protected) \$id;@';
    static public $id_field_str = '    protected $id;' . "\n";
    static public $id_field_with_constant_pattern = '@[\h]+const _ID = \'id\';[\v]+[\h]+protected \$id;@';

    public function extractTableName()
    {
        $table_name_pattern = '@const[\h]+DB_TABLE_NAME[\h]+=[\h]+[\'\"](\w+)[\'\"]@';
        $matches = [];
        if (!preg_match($table_name_pattern, $this->class_file_text, $matches)) {
            throw new \Exception("table name not found in class file");
        }

        $this->model_table_name = $matches[1];
    }

    public function extractDbId()
    {
        $db_id_pattern = '@const[\h]+DB_ID[\h]+=[\h]+[\'\"](\w+)[\'\"]@';
        $matches = [];
        if (!preg_match($db_id_pattern, $this->class_file_text, $matches)) {
            throw new \Exception("DB_ID constant not found in class or is not scalar.");
        }

        // TODO: validate model_db_id (check presence in config)
        $this->model_db_id = $matches[1];
    }

    public function getFieldNamesArr(): array
    {
        $reflection = new \ReflectionClass($this->class_namespace . '\\' . $this->class_name);
        return array_map(
            function(\ReflectionProperty $prop)
            {
                return $prop->getName();
            },
            $reflection->getProperties()
        );
    }

    public function save(): void
    {
        $put_result = file_put_contents($this->class_file_path, $this->class_file_text);
        if (!$put_result){
            throw new \Exception();
        }
    }

    /**
     * здесь поле id вставляется под новое поле, чтобы новые поля вставлялись над полем id, а новые методы - под ним
     * то есть поле id как бы разделяет свойства и методы
     *
     * поддерживается и просто поле id, и поле id с константой _ID над ним - если поле с константой, то они так и останутся парочкой
     *
     * @param $str
     * @throws \Exception
     */
    public function insertAboveIdField($str): void
    {
        $id_field_with_constant_pattern = self::$id_field_with_constant_pattern;

        if (!preg_match($id_field_with_constant_pattern, $this->class_file_text)) {
            $id_field_pattern = self::$id_field_pattern;

            if (!preg_match($id_field_pattern, $this->class_file_text)) {
                throw new \Exception("ID field not found");
            }

            $str .= self::$id_field_str;

            $this->class_file_text = preg_replace($id_field_pattern, $str, $this->class_file_text);
        } else {
            $str .= '    const _ID = \'id\';' . "\n";
            $str .= self::$id_field_str;

            $this->class_file_text = preg_replace($id_field_with_constant_pattern, $str, $this->class_file_text);
        }
    }

    /**
     * здесь поддержку константы _ID (как в insertAboveIdField) не делал - затрагивается только строка с самим полем id
     *
     * @param $str
     * @throws \Exception
     */
    public function insertBelowIdField($str): void
    {
        $id_field_pattern = self::$id_field_pattern;

        if (!preg_match($id_field_pattern, $this->class_file_text)) {
            throw new \Exception("ID field not found");
        }

        $str = self::$id_field_str . $str;

        $this->class_file_text = preg_replace($id_field_pattern, $str, $this->class_file_text);
    }

    /**
     * Loads file.
     * PHPClassFile constructor.
     * @param $model_file_path
     */
    public function __construct($model_file_path)
    {
        $this->class_file_path = $model_file_path;

        $this->class_file_text = file_get_contents($this->class_file_path);
        if (!$this->class_file_text) throw new \Exception(); // TODO: better check?

        $this->extractClassName();
        $this->extractClassNamespace();
        $this->extractDbId();
        $this->extractTableName();
    }

    public function extractClassNamespace(): void
    {
        $namespace_matches = [];
        $namespace_pattern = '@\Rnamespace\s+(\w+);@';
        if (preg_match($namespace_pattern, $this->class_file_text, $namespace_matches)) {
            $this->class_namespace = $namespace_matches[1];
        }
    }

    public function extractClassName(): void
    {
        $class_name_matches = [];
        $class_name_pattern = '@\Rclass\s+(\w+)@';

        if (preg_match($class_name_pattern, $this->class_file_text, $class_name_matches)) {
            $this->class_name = $class_name_matches[1];
        } else {
            throw new \Exception("class name not found in class file");
        }
    }
}
