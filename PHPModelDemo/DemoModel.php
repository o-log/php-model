<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace PHPModelDemo;

use OLOG\Model\ActiveRecordInterface;
use OLOG\Model\ActiveRecordTrait;

class DemoModel implements
    ActiveRecordInterface
{
    use ActiveRecordTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'phpmodeldemo_demomodel';

    const _ID = 'id'; // field names constants for CRUD
    const _CREATED_AT_TS = 'created_at_ts';

    public $created_at_ts; // initialized by constructor
    const _TITLE = 'title';
    public $title;
    const _BOOL_VAL = 'bool_val';
    public $bool_val;
    public $id;

    /**
     * @return DemoModel[]
     */
    static public function forBoolVal($value, int $limit = 30, int $offset = 0): array {
        return self::idsToObjs(self::idsForBoolVal($value, $limit, $offset));
    }

    static public function idsForBoolVal($value, $limit = 30, $offset = 0){
        $args = [$limit, $offset];
        if (!is_null($value)){
            array_unshift($args, $value);
        }

        return \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' where ' . self::_BOOL_VAL . ' ' . (is_null($value) ? 'is null' : '=?') . ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            $args
        );
    }



    public function __construct(){
        $this->created_at_ts = time();
    }

    public function getId()
    {
        return $this->id;
    }

    static public function idsByCreatedAtDesc($offset = 0, $page_size = 30){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME . ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$page_size, $offset]
        );
        return $ids_arr;
    }
}
