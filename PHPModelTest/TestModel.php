<?php
/**
 * create table test_model(id int not null auto_increment primary key, title varchar(255) not null default '') default charset utf8;
 */

namespace PHPModelTest;

class TestModel implements \OLOG\Model\InterfaceFactory
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecord;
    use \OLOG\Model\ProtectProperties;

    const DB_ID = \PHPModelTest\Config::DB_NAME_PHPMODELTEST;
    const DB_TABLE_NAME = 'test_model';

    protected $id = 0;
    protected $title = '';

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($title){
        $this->title = $title;
    }

}