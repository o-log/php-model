<?php

namespace PHPModelDemo;

class DemoModel implements \OLOG\Model\InterfaceFactory
{
    use \OLOG\Model\FactoryTrait;
    use \OLOG\Model\ActiveRecordTrait;
    use \OLOG\Model\ProtectPropertiesTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'demo_model';

    const _WEIGHT = 'weight';
    protected $weight = 0;
    const _COLLATE_TEST = 'collate_test';
    protected $collate_test = '';
    const _COLLATE_TEST_2 = 'collate_test_2';
    protected $collate_test_2 = '';
    const _COLLATE_TEST_3 = 'collate_test_3';
    protected $collate_test_3 = 0;
    const _DEMO_NODE_ID = 'demo_node_id';
    protected $demo_node_id;
    protected $id;

    static public function getIdsArrForDemoNodeIdByCreatedAtDesc($value, $offset = 0, $page_size = 10){
        if (is_null($value)) {
            return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where ' . self::_DEMO_NODE_ID . ' is null order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset)
            );
        } else {
            return \OLOG\DB\DBWrapper::readColumn(
                self::DB_ID,
                'select id from ' . self::DB_TABLE_NAME . ' where ' . self::_DEMO_NODE_ID . ' = ? order by created_at_ts desc limit ' . intval($page_size) . ' offset ' . intval($offset),
                array($value)
            );
        }
    }


    public function getDemoNodeId(){
        return $this->demo_node_id;
    }

    public function setDemoNodeId($value){
        $this->demo_node_id = $value;
    }



    public function getCollateTest3(){
        return $this->collate_test_3;
    }

    public function setCollateTest3($value){
        $this->collate_test_3 = $value;
    }



    public function getCollateTest2(){
        return $this->collate_test_2;
    }

    public function setCollateTest2($value){
        $this->collate_test_2 = $value;
    }



    public function getCollateTest(){
        return $this->collate_test;
    }

    public function setCollateTest($value){
        $this->collate_test = $value;
    }



    public function getWeight(){
        return $this->weight;
    }

    public function setWeight($value){
        $this->weight = $value;
    }


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