<?php

namespace Tests;

class TestTerm implements
    \OLOG\Model\ActiveRecordInterface
{
    use \OLOG\Model\ActiveRecordTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'testterm';

    protected $id;
    public $created_at_ts = '';

    public function __construct(){
        $this->created_at_ts = time();
    }

    public function getId()
    {
        return $this->id;
    }
}
