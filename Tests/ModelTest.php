<?php

class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * проверка создания, сохранения и загрузки объекта
     */
    public function testSaveLoadDelete()
    {
        \OLOG\ConfWrapper::assignConfig(\PHPModelDemo\Config::get());

        $test_title = rand(1, 10000);
        $new_model = new \PHPModelDemo\DemoModel();
        $new_model->setTitle($test_title);
        $new_model->save();

        $test_model_id = $new_model->getId();
        $this->assertNotEmpty($test_model_id); // тестирует генерацию непустого идентификатора модели при первом сохранении

        $loaded_model_obj = \PHPModelDemo\DemoModel::factory($test_model_id);
        $this->assertEquals($test_title, $loaded_model_obj->getTitle()); // тестируем совпадение заголовков сохраненной и загруженной модели


        $loaded_model_obj->delete();

        $test_model_ids_arr = \OLOG\DB\DBWrapper::readColumn(
            \PHPModelDemo\DemoModel::DB_ID,
            'select id from ' . \PHPModelDemo\DemoModel::DB_TABLE_NAME . ' where id = ?',
            array($test_model_id)
        );

        $this->assertEquals(0, count($test_model_ids_arr)); // проверяем что записей с таким ИД в таблице нет
    }

    /**
     * проверка отслеживания изменения объекта
     */
    public function testAfterUpdate(){
        \OLOG\ConfWrapper::assignConfig(\PHPModelDemo\Config::get());

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
}