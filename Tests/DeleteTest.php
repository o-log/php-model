<?php

namespace Tests;

use Config\Config;
use OLOG\DB\DB;

class DeleteTest extends \PHPUnit_Framework_TestCase
{
    public function testCanDeleteTrue()
    {
        Config::init();

        // нормальное удаление модели

        $obj = new \Tests\TestModel();
        $obj->save();

        $this->assertEquals(false, DB::inTransaction(TestModel::DB_ID));

        $obj_id = $obj->getId();
        $obj->delete();

        $this->assertEquals(false, DB::inTransaction(TestModel::DB_ID));

        $test_model_ids_arr = DB::readColumn(
            \Tests\TestModel::DB_ID,
            'select id from ' . \Tests\TestModel::DB_TABLE_NAME . ' where id = ?',
            array($obj_id)
        );

        $this->assertEquals(0, count($test_model_ids_arr)); // проверяем что запись в БД удалена
    }

    public function testCanDeleteFalse()
    {
        // удаление модели при котором canDelete возвращает false

        $obj2 = new \Tests\TestModel();
        $obj2->disable_delete = true;
        $obj2->save();

        $this->assertEquals(false, DB::inTransaction(TestModel::DB_ID));

        $obj2_id = $obj2->getId();

        $exception_message = '';

        try {
            $obj2->delete();
        } catch (\Exception $e){
            $exception_message = $e->getMessage();
        }

        $this->assertEquals('Delete disabled', $exception_message);

        $this->assertEquals(false, DB::inTransaction(TestModel::DB_ID));

        $test_model_ids_arr = DB::readColumn(
            \Tests\TestModel::DB_ID,
            'select id from ' . \Tests\TestModel::DB_TABLE_NAME . ' where id = ?',
            array($obj2_id)
        );

        $this->assertEquals(1, count($test_model_ids_arr)); // проверяем что запись в БД не удалена
    }

    public function testAfterDeleteAndTransaction()
    {
        Config::init();

        // нормальное удаление модели

        $obj = new \Tests\TestModel();
        $obj->throw_exception_after_delete = true;
        $obj->save();

        $this->assertEquals(false, DB::inTransaction(TestModel::DB_ID));

        $obj_id = $obj->getId();

        //$this->expectException(\Exception::class);
        //$this->expectExceptionMessage('After delete');

        $exception_message = '';
        try {
            $obj->delete();
        } catch (\Exception $e){
            $exception_message = $e->getMessage();
            //DB::rollBack(TestModel::DB_ID);
        }

        $this->assertEquals('After delete', $exception_message);

        $this->assertEquals(false, DB::inTransaction(TestModel::DB_ID));

        $test_model_ids_arr = DB::readColumn(
            \Tests\TestModel::DB_ID,
            'select id from ' . \Tests\TestModel::DB_TABLE_NAME . ' where id = ?',
            array($obj_id)
        );

        $this->assertEquals(1, count($test_model_ids_arr)); // проверяем что запись в БД осталась, т.е. транзакция с удалением была откачена
    }

}