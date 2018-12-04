<?php
declare(strict_types=1);

namespace Tests;

use OLOG\Model\ActiveRecordInterface;
use OLOG\Model\ActiveRecordTrait;

class AfterLoadTestModel implements
    ActiveRecordInterface
{
    use ActiveRecordTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'tests_afterloadtestmodel';

    const _CREATED_AT_TS = 'created_at_ts';
    public $created_at_ts;
    const _RANDINT = 'randint';
    public $randint;
    const _ID = 'id';
    public $id;


    public function __construct(){
        $this->created_at_ts = time();
        $this->randint = rand(0, 100000);
    }

    public function getId()
    {
        return $this->id;
    }

    public function beforeSave()
    {


        if (property_exists($this, '__original')){
            unset($this->__original);
        }
    }

    public function afterLoad()
    {
        $this->__original = $this;
    }
}
