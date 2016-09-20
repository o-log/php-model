<?php

namespace Tests;

class DeleteTest extends \PHPUnit_Framework_TestCase
{
    public function testCanDeleteTrue()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        // нормальное удаление модели

        $obj = new \Tests\TestModel();
        $obj->save();
        $obj_id = $obj->getId();
        $obj->delete();

        $test_model_ids_arr = \OLOG\DB\DBWrapper::readColumn(
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
        $obj2->setDisableDelete(true);
        $obj2->save();
        $obj2_id = $obj2->getId();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Delete disabled');

        $obj2->delete();

        $test_model_ids_arr = \OLOG\DB\DBWrapper::readColumn(
            \Tests\TestModel::DB_ID,
            'select id from ' . \Tests\TestModel::DB_TABLE_NAME . ' where id = ?',
            array($obj2_id)
        );

        $this->assertEquals(0, count($test_model_ids_arr)); // проверяем что запись в БД удалена

    }

    public function testAfterDeleteAndTransaction()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        // нормальное удаление модели

        $obj = new \Tests\TestModel();
        $obj->setThrowExceptionAfterDelete(true);
        $obj->save();

        $obj_id = $obj->getId();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('After delete');

        $obj->delete();

        $test_model_ids_arr = \OLOG\DB\DBWrapper::readColumn(
            \Tests\TestModel::DB_ID,
            'select id from ' . \Tests\TestModel::DB_TABLE_NAME . ' where id = ?',
            array($obj_id)
        );

        $this->assertEquals(1, count($test_model_ids_arr)); // проверяем что запись в БД осталась, т.е. транзакция с удалением была откачена

    }

}