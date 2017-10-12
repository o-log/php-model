<?php

namespace Tests;

use OLOG\Model\ActiveRecordTrait;
use OLOG\Model\FactoryTrait;
use OLOG\Model\FactoryInterface;
use OLOG\Model\ActiveRecordInterface;
use OLOG\Model\ProtectPropertiesTrait;

class LoadTestModel implements
    ActiveRecordInterface
{
    use ActiveRecordTrait;
    use ProtectPropertiesTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'tests_loadtestmodel';

    const _ID = 'id';
    const _CREATED_AT_TS = 'created_at_ts';

    // поле закомментировано для теста загрузки отсутствующего свойства
    //const _EXTRA_FIELD = 'extra_field';
    //public $extra_field;

    const _TITLE = 'title';
    public $title = "";
    protected $id;
    public $field_not_in_table;

    protected $created_at_ts; // initialized by constructor
    
    public function __construct(){
        $this->created_at_ts = time();
    }

    static public function getAllIdsArrByCreatedAtDesc($offset = 0, $page_size = 30){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$page_size, $offset]
        );
        return $ids_arr;
    }

    public function getId()
    {
        return $this->id;
    }
}