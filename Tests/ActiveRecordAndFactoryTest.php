<?php

class ActiveRecordAndFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Тест проверяет создание, сохранение, загрузку и удаление объекта через activeRecord и factory
     */
    public function testSaveLoadDelete()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        $test_title = rand(1, 10000);
        $new_model = new \Tests\TestModel();
        $new_model->setTitle($test_title);
        $new_model->save();

        $test_model_id = $new_model->getId();
        $this->assertNotEmpty($test_model_id); // тестирует генерацию непустого идентификатора модели при первом сохранении

        $loaded_model_obj = \Tests\TestModel::factory($test_model_id);
        $this->assertEquals($test_title, $loaded_model_obj->getTitle()); // тестируем совпадение заголовков сохраненной и загруженной модели

        $loaded_model_obj->delete();

        $test_model_ids_arr = \OLOG\DB\DBWrapper::readColumn(
            \PHPModelDemo\DemoModel::DB_ID,
            'select id from ' . \Tests\TestModel::DB_TABLE_NAME . ' where id = ?',
            array($test_model_id)
        );

        $this->assertEquals(0, count($test_model_ids_arr)); // проверяем что записей с таким ИД в таблице нет
    }

    /**
     * Тест проверяет вызов afterSave при сохранении объекта через activeRecord
     */
    public function testAfterSave()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        $new_node_obj = new \PHPModelDemo\DemoNode();
        $new_node_obj->save();

        /** @var int $initial_node_created_at */
        $initial_node_created_at = $new_node_obj->getCreatedAtTs();
        $node_id = $new_node_obj->getId();

        $new_term_obj = new \PHPModelDemo\DemoTerm();
        $new_term_obj->save();

        $new_term_to_node_obj = new \PHPModelDemo\DemoTermToNode();
        $new_term_to_node_obj->setNodeId($node_id);
        $new_term_to_node_obj->setTermId($new_term_obj->getId());
        $new_term_to_node_obj->save();

        /**
         * меняем время создания ноды и сохраняем
         * при этом afterSave в ноде должен реплицировать измененную дату в связь ноды с термом
         */

        $loaded_node_obj = \PHPModelDemo\DemoNode::factory($node_id);
        $updated_node_created_at = $initial_node_created_at + 1000;
        $loaded_node_obj->setCreatedAtTs($updated_node_created_at);
        $loaded_node_obj->save();

        $term_to_node_ids_arr = \PHPModelDemo\DemoTermToNode::getIdsArrForNodeIdByCreatedAtDesc($node_id);
        $this->assertEquals(1, count($term_to_node_ids_arr), 'wrong number of term to node records');

        foreach ($term_to_node_ids_arr as $term_to_node_id){
            $term_to_node_obj = \PHPModelDemo\DemoTermToNode::factory($term_to_node_id);
            $this->assertEquals($updated_node_created_at, $term_to_node_obj->getCreatedAtTs());
        }
    }

    /**
     * Тест проверяет вызов beforeSave при сохранении объекта через activeRecord
     */
    public function testBeforeSave()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        // проверим как вызывается при сохранении нового объекта

        $test_title = rand(1, 1000000);

        $new_node_obj = new \PHPModelDemo\DemoNode();
        $new_node_obj->setTitle($test_title);
        $new_node_obj->save();

        $node_id = $new_node_obj->getId();

        $loaded_node_obj = \PHPModelDemo\DemoNode::factory($node_id);
        $this->assertEquals($loaded_node_obj->getBody(), $test_title . $test_title);

        // проверим как вызывается при сохранении существующего объекта

        $test_title_2 = rand(1, 1000000);

        $loaded_node_obj->setTitle($test_title_2);
        $loaded_node_obj->save();

        $loaded_node_obj_2 = \PHPModelDemo\DemoNode::factory($node_id);
        $this->assertEquals($loaded_node_obj_2->getBody(), $test_title_2 . $test_title_2);
    }

    public function testCanDeleteTrue()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        // нормальное удаление модели

        $obj = new \Tests\TestModel();
        $obj->save();
        $obj_id = $obj->getId();
        $obj->delete();

        $test_model_ids_arr = \OLOG\DB\DBWrapper::readColumn(
            \PHPModelDemo\DemoModel::DB_ID,
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
            \PHPModelDemo\DemoModel::DB_ID,
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
            \PHPModelDemo\DemoModel::DB_ID,
            'select id from ' . \Tests\TestModel::DB_TABLE_NAME . ' where id = ?',
            array($obj_id)
        );

        $this->assertEquals(1, count($test_model_ids_arr)); // проверяем что запись в БД осталась, т.е. транзакция с удалением была откачена

    }
}