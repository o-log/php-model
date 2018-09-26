<?php
declare(strict_types=1);

namespace PHPModelDemo;

use OLOG\Model\ActiveRecordInterface;
use OLOG\Model\ActiveRecordTrait;

class DemoModel2 implements
    ActiveRecordInterface
{
    use ActiveRecordTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'phpmodeldemo_demomodel2';

    const _CREATED_AT_TS = 'created_at_ts';
    public $created_at_ts;
    const _ID = 'id';
    public $id;
    
    public function __construct(){
        $this->created_at_ts = time();
    }
    
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return DemoModel2[]
     */
    static public function all($limit = 30, $offset = 0){
        return self::idsToObjs(self::ids($limit, $offset));
    }

    static public function ids($limit = 30, $offset = 0){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$limit, $offset]
        );
        return $ids_arr;
    }
}