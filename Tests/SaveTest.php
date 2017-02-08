<?php

namespace Tests;

class SaveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Тест проверяет вызов afterSave при сохранении объекта через activeRecord
     */
    public function testAfterSave()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        $new_node_obj = new \Tests\TestNode();
        $new_node_obj->save();

        /** @var int $initial_node_created_at */
        $initial_node_created_at = $new_node_obj->getCreatedAtTs();
        $node_id = $new_node_obj->getId();

        $new_term_obj = new \Tests\TestTerm();
        $new_term_obj->save();

        $new_term_to_node_obj = new \Tests\TestTermToNode();
        $new_term_to_node_obj->setNodeId($node_id);
        $new_term_to_node_obj->setTermId($new_term_obj->getId());
        $new_term_to_node_obj->save();

        /**
         * меняем время создания ноды и сохраняем
         * при этом afterSave в ноде должен реплицировать измененную дату в связь ноды с термом
         */
        $loaded_node_obj = \Tests\TestNode::factory($node_id);
        $updated_node_created_at = $initial_node_created_at + 1000;
        $loaded_node_obj->setCreatedAtTs($updated_node_created_at);
        $loaded_node_obj->save();

        $term_to_node_ids_arr = \Tests\TestTermToNode::getIdsArrForNodeIdByCreatedAtDesc($node_id);
        $this->assertEquals(1, count($term_to_node_ids_arr), 'wrong number of term to node records');

        foreach ($term_to_node_ids_arr as $term_to_node_id){
            $term_to_node_obj = \Tests\TestTermToNode::factory($term_to_node_id);
            $this->assertEquals($updated_node_created_at, $term_to_node_obj->getCreatedAtTs());
        }
    }

    public function testSingleAfterSaveCall(){
        \PHPModelDemo\ModelDemoConfig::init();

        $test_obj = new TestModel();
        $test_obj->setAfterSaveCounter(0);
        $test_obj->save();

        $this->assertEquals(1, $test_obj->getAfterSaveCounter());
    }

    /**
     * Тест проверяет вызов beforeSave при сохранении объекта через activeRecord
     */
    public function testBeforeSave()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        // проверим как вызывается при сохранении нового объекта

        $test_title = rand(1, 1000000);

        $new_node_obj = new \Tests\TestNode();
        $new_node_obj->setTitle($test_title);
        $new_node_obj->save();

        $node_id = $new_node_obj->getId();

        $loaded_node_obj = \Tests\TestNode::factory($node_id);
        $this->assertEquals($loaded_node_obj->getBody(), $test_title . $test_title);

        // проверим как вызывается при сохранении существующего объекта

        $test_title_2 = rand(1, 1000000);

        $loaded_node_obj->setTitle($test_title_2);
        $loaded_node_obj->save();

        $loaded_node_obj_2 = \Tests\TestNode::factory($node_id);
        $this->assertEquals($loaded_node_obj_2->getBody(), $test_title_2 . $test_title_2);
    }

}