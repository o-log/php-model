<?php

namespace OLOG\Model\CLI;

use OLOG\Assert;

class PHPClassFile
{
    public $class_file_path;
    public $class_file_text;
    public $class_name;
    public $class_namespace = '';

    static public $id_field_pattern = '@[\h]+protected \$id;@';
    static public $id_field_with_constant_pattern = '@[\h]+const _ID = \'id\';[\v]+[\h]+protected \$id;@';

    // TODO
    // implement on reflections, regexp cant match complex properties (with multi-line default values, etc.)
    public function getFieldNamesArr(){
        /*
        $field_name_pattern = '@\h+(protected|public|private)\h+\$([\w_]+)(\h*=\h*[]);@';

        $matches_arr = [];

        preg_match_all($field_name_pattern, $this->class_file_text, $matches_arr);
        */
    }

    public function save(){
        $put_result = file_put_contents($this->class_file_path, $this->class_file_text);
        if (!$put_result) throw new \Exception();
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
    public function insertAboveIdField($str){
        $id_field_with_constant_pattern = self::$id_field_with_constant_pattern;

        if (!preg_match($id_field_with_constant_pattern, $this->class_file_text)) {
            $id_field_pattern = self::$id_field_pattern;

            if (!preg_match($id_field_pattern, $this->class_file_text)) {
                throw new \Exception("ID field not found");
            }

            $str .= '    protected $id;' . "\n";

            $this->class_file_text = preg_replace($id_field_pattern, $str, $this->class_file_text);
        }

        $str .= '    const _ID = \'id\';' . "\n";
        $str .= '    protected $id;' . "\n";

        $this->class_file_text = preg_replace($id_field_with_constant_pattern, $str, $this->class_file_text);
    }

    /**
     * здесь поддержку константы _ID (как в insertAboveIdField) не делал - затрагивается только строка с самим полем id
     *
     * @param $str
     * @throws \Exception
     */
    public function insertBelowIdField($str){
        $id_field_pattern = self::$id_field_pattern;

        if (!preg_match($id_field_pattern, $this->class_file_text)) {
            throw new \Exception("ID field not found");
        }

        $str = '    protected $id;' . "\n" . $str;

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
    }

    public function extractClassNamespace(){
        $namespace_matches = [];
        $namespace_pattern = '@\Rnamespace\s+(\w+);@';
        if (preg_match($namespace_pattern, $this->class_file_text, $namespace_matches)) {
            $this->class_namespace = $namespace_matches[1];
        }
    }

    public function extractClassName(){

        $class_name_matches = [];
        $class_name_pattern = '@\Rclass\s+(\w+)@';

        if (preg_match($class_name_pattern, $this->class_file_text, $class_name_matches)) {
            $this->class_name = $class_name_matches[1];
        } else {
            throw new \Exception("class name not found in class file");
        }
    }

    /**
     * @return mixed
     */
    public function getClassFilePath()
    {
        return $this->class_file_path;
    }

    /**
     * @param mixed $class_file_path
     */
    public function setClassFilePath($class_file_path)
    {
        $this->class_file_path = $class_file_path;
    }

    /**
     * @return string
     */
    public function getClassFileText()
    {
        return $this->class_file_text;
    }

    /**
     * @param string $class_file_text
     */
    public function setClassFileText($class_file_text)
    {
        $this->class_file_text = $class_file_text;
    }
}