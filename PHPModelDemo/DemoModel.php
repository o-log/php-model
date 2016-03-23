<?php

namespace PHPModelDemo;

class DemoModel implements \OLOG\Model\InterfaceFactory
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecord;
    use \OLOG\Model\ProtectProperties;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'demo_model';

    protected $id;
    protected $dud = 0;
    protected $widget_name = "empty";
    protected $text_nullable;
    protected $test_varchar = "";
    protected $test = 0;
    protected $title = '';

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * @param $title
     */
    public function setTitle($title){
        $this->title = $title;
    }

}