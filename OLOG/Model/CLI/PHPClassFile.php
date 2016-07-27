<?php

namespace OLOG\Model\CLI;

use OLOG\Assert;

class PHPClassFile
{
    public $class_file_path;
    public $class_file_text;

    public function __construct($model_file_path)
    {
        $this->class_file_path = $model_file_path;

        $this->class_file_text = file_get_contents($this->class_file_path);
        Assert::assert($this->class_file_text); // TODO: better check?
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