<?php

namespace Tests;

class TestTermToNode implements
    \OLOG\Model\ActiveRecordInterface
{
    use \OLOG\Model\ActiveRecordTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'testtermtonode';

    public $created_at_ts = '';
    public $term_id;
    public $node_id;
    protected $id;

    static public function getIdsArrForNodeIdByCreatedAtDesc($value){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where node_id = ? order by created_at_ts desc',
            array($value)
        );
        return $ids_arr;
    }

    static public function getIdsArrForTermIdByCreatedAtDesc($value){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' where term_id = ? order by created_at_ts desc',
            array($value)
        );
        return $ids_arr;
    }

    static public function getAllIdsArrByCreatedAtDesc(){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' order by created_at_ts desc'
        );
        return $ids_arr;
    }

    public function __construct(){
        $this->created_at_ts = time();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}
