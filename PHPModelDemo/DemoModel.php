<?php

namespace PHPModelDemo;

class DemoModel implements \OLOG\Model\InterfaceFactory
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecord;
    use \OLOG\Model\ProtectProperties;

    const DB_ID = \PHPModelDemo\Config::DB_NAME_PHPMODELDEMO;
    const DB_TABLE_NAME = 'demo_model';

    protected $id;
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