<?php
declare(strict_types=1);

namespace PHPModelDemo;

use OLOG\Model\ActiveRecordInterface;
use OLOG\Model\ActiveRecordTrait;

class DemoModel3 implements
    ActiveRecordInterface
{
    use ActiveRecordTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'phpmodeldemo_demomodel3';

    const _CREATED_AT_TS = 'created_at_ts';
    public $created_at_ts;
    const _RANDINT = 'randint';
    public $randint;
    const _ID = 'id';
    public $id;

    public function beforeSave(){
        $this->randint = rand(0, 10);
    }

    /**
     * @return DemoModel3[]
     */
    static public function forRandint($randint, int $limit = 30, int $offset = 0): array {
        return self::idsToObjs(self::idsForRandint($randint, $limit, $offset));
    }

    static public function idsForRandint($randint, $limit = 30, $offset = 0): array {
        if (is_null($randint)){
            throw new \Exception('NULL values not supported in selector.');
        }

        return \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME .
            ' where ' . self::_RANDINT . '=?' .
            ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$randint, $limit, $offset]
        );
    }


    /**
     * @return DemoModel3[]
     */
    static public function all(int $limit = 30, int $offset = 0): array {
        return self::idsToObjs(self::ids($limit, $offset));
    }

    static public function ids($limit = 30, $offset = 0): array {
        return \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select ' . self::_ID . ' from ' . self::DB_TABLE_NAME .
            ' order by ' . self::_CREATED_AT_TS . ' desc limit ? offset ?',
            [$limit, $offset]
        );
    }



    public function __construct(){
        $this->created_at_ts = time();
    }

    public function getId()
    {
        return $this->id;
    }
}
