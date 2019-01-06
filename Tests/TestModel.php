<?php

namespace Tests;

class TestModel implements
    \OLOG\Model\ActiveRecordInterface
{
    use \OLOG\Model\ActiveRecordTrait;

    const DB_ID = 'phpmodel';
    const DB_TABLE_NAME = 'tests_testmodel';

    public $created_at_ts; // initialized by constructor
    public $title = "";
    public $disable_delete = 0;
    public $throw_exception_after_delete = 0;
    public $after_save_counter = 0;
    protected $id;

    public function afterDelete(){
        if ($this->throw_exception_after_delete){
            throw new \Exception('After delete');
        }
    }

    public function afterSave(){
        $this->removeFromFactoryCache();

        $this->after_save_counter = $this->after_save_counter + 1;
        $this->save();
    }

    static public function getAllIdsArrByCreatedAtDesc($offset = 0, $page_size = 30){
        $ids_arr = \OLOG\DB\DB::readColumn(
            self::DB_ID,
            'select id from ' . self::DB_TABLE_NAME . ' order by created_at_ts desc limit ? offset ?',
            [$page_size, $offset]
        );
        return $ids_arr;
    }

    public function __construct(){
        $this->created_at_ts = time();
    }

    public function canDelete(&$message){
        if ($this->disable_delete){
            $message = 'Delete disabled';
            return false;
        }

        return true;
    }

    public function getId()
    {
        return $this->id;
    }
}
