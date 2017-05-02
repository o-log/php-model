<?php
namespace Tests;

use OLOG\Model\ActiveRecordTrait;
use OLOG\Model\FactoryTrait;
use OLOG\Model\InterfaceDelete;
use OLOG\Model\InterfaceFactory;
use OLOG\Model\InterfaceLoad;
use OLOG\Model\InterfaceSave;
use OLOG\Model\ProtectPropertiesTrait;

class LoadTestModel implements
    InterfaceFactory,
    InterfaceLoad,
    InterfaceSave,
    InterfaceDelete
{
    use FactoryTrait;
    use ActiveRecordTrait;
    use ProtectPropertiesTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'tests_loadtestmodel';

    const _ID = 'id';
    const _CREATED_AT_TS = 'created_at_ts';

    // поле закомментировано для теста загрузки отсутствующего свойства
    //const _EXTRA_FIELD = 'extra_field';
    //protected $extra_field;

    const _TITLE = 'title';
    protected $title;
    protected $id;
    protected $field_not_in_table;

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($value){
        $this->title = $value;
    }



    public function getExtraField(){
        return $this->extra_field;
    }

    public function setExtraField($value){
        $this->extra_field = $value;
    }


    protected $created_at_ts; // initialized by constructor
    
    public function __construct(){
        $this->created_at_ts = time();
    }

    static public function getAllIdsArrByCreatedAtDesc($offset = 0, $page_size = 30){
        $ids_arr = \OLOG\DB\DBWrapper::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' order by ' . self::_CREATED_AT_TS . ' desc limit ' . intval($page_size) . ' offset ' . intval($offset)
        );
        return $ids_arr;
    }

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
    public function getCreatedAtTs()
    {
        return $this->created_at_ts;
    }

    /**
     * @param string $timestamp
     */
    public function setCreatedAtTs($timestamp)
    {
        $this->created_at_ts = $timestamp;
    }
}