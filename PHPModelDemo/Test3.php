<?php
namespace PHPModelDemo;

use OLOG\Model\ActiveRecordTrait;
use OLOG\Model\FactoryTrait;
use OLOG\Model\FactoryInterface;
use OLOG\Model\ActiveRecordInterface;
use OLOG\Model\ProtectPropertiesTrait;

class Test3 implements
    FactoryInterface,
    ActiveRecordInterface
{
    use FactoryTrait;
    use ActiveRecordTrait;
    use ProtectPropertiesTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'phpmodeldemo_test3';

    const _ID = 'id';
    const _CREATED_AT_TS = 'created_at_ts';
    
    const _TITLE = 'title';
    protected $title;
    protected $id;

    public function getTitle(){
        return $this->title;
    }

    public function setTitle($value){
        $this->title = $value;
    }


    protected $created_at_ts; // initialized by constructor
    
    public function __construct(){
        $this->created_at_ts = time();
    }

    static public function idsByCreatedAtDesc($offset = 0, $page_size = 30){
        $ids_arr = \OLOG\DB\DB::readColumn(
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
     * @return int
     */
    public function getCreatedAtTs()
    {
        return $this->created_at_ts;
    }

    /**
     * @param int $timestamp
     */
    public function setCreatedAtTs($timestamp)
    {
        $this->created_at_ts = $timestamp;
    }
}