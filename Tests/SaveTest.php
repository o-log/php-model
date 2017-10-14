<?php

namespace Tests;

use OLOG\DB\DB;
use OLOG\Model\ModelConfig;

class SaveTest extends \PHPUnit_Framework_TestCase
{
    public function testInsertWithMissingFieldWithException(){
        \PHPModelDemo\ModelDemoConfig::init();

        $new_model_obj = new \Tests\LoadTestModel();

        ModelConfig::setIgnoreMissingPropertiesOnSave(false);

        //$this->expectException(\Exception::class);
        //$this->expectExceptionMessage('missing property');

        $exception_message = '';
        try {
            $new_model_obj->save();
        } catch (\Exception $e){
            $exception_message = $e->getMessage();
        }

        $this->assertStringStartsWith('Missing property when saving model: ', $exception_message);

        //DB::commit(LoadTestModel::DB_ID);

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(LoadTestModel::DB_ID));
    }

    public function testUpdateWithMissingFieldWithException(){
        \PHPModelDemo\ModelDemoConfig::init();

        //$new_model = new \Tests\LoadTestModel();
        $model_ids_arr = LoadTestModel::getAllIdsArrByCreatedAtDesc();
        if (!count($model_ids_arr)) throw new \Exception();
        $model_id = $model_ids_arr[0];

        ModelConfig::setIgnoreMissingPropertiesOnLoad(true);
        $model_obj = LoadTestModel::factory($model_id);
        $model_obj->title = rand(0, 999999);

        ModelConfig::setIgnoreMissingPropertiesOnSave(false);

        //$this->expectException(\Exception::class);
        //$this->expectExceptionMessage('missing property');

        $exception_message = '';
        try {
            $model_obj->save();
        } catch (\Exception $e){
            $exception_message = $e->getMessage();
        }

        $this->assertStringStartsWith('Missing property when saving model: ', $exception_message);

        //DB::commit(LoadTestModel::DB_ID);

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(LoadTestModel::DB_ID));
    }

    public function testInsertWithMissingFieldWithoutException(){
        \PHPModelDemo\ModelDemoConfig::init();

        $new_model = new \Tests\LoadTestModel();

        ModelConfig::setIgnoreMissingPropertiesOnSave(true);

        $new_model->save();

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(LoadTestModel::DB_ID));

        //DB::commit(LoadTestModel::DB_ID);

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(LoadTestModel::DB_ID));
    }

    public function testUpdateWithMissingFieldWithoutException(){
        \PHPModelDemo\ModelDemoConfig::init();

        //$new_model = new \Tests\LoadTestModel();
        $model_ids_arr = LoadTestModel::getAllIdsArrByCreatedAtDesc();
        if (!count($model_ids_arr)) throw new \Exception();
        $model_id = $model_ids_arr[0];

        ModelConfig::setIgnoreMissingPropertiesOnLoad(true);
        $model_obj = LoadTestModel::factory($model_id);
        $model_obj->title = rand(0, 1000);

        ModelConfig::setIgnoreMissingPropertiesOnSave(true);

        //$this->expectException(\Exception::class);
        //$this->expectExceptionMessage('missing property');

        $model_obj->save();

        //DB::commit(LoadTestModel::DB_ID);

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(LoadTestModel::DB_ID));
    }

    /**
     * Тест проверяет вызов afterSave при сохранении объекта через activeRecord
     */
    public function testAfterSave()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        $new_node_obj = new \Tests\TestNode();
        $new_node_obj->save();

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(TestNode::DB_ID));

        /** @var int $initial_node_created_at */
        $initial_node_created_at = $new_node_obj->created_at_ts;
        $node_id = $new_node_obj->getId();

        $new_term_obj = new \Tests\TestTerm();
        $new_term_obj->save();

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(TestTerm::DB_ID));

        $new_term_to_node_obj = new \Tests\TestTermToNode();
        $new_term_to_node_obj->node_id = $node_id;
        $new_term_to_node_obj->term_id = $new_term_obj->getId();
        $new_term_to_node_obj->save();

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(TestTermToNode::DB_ID));

        /**
         * меняем время создания ноды и сохраняем
         * при этом afterSave в ноде должен реплицировать измененную дату в связь ноды с термом
         */
        $loaded_node_obj = \Tests\TestNode::factory($node_id);
        $updated_node_created_at = $initial_node_created_at + 1000;
        $loaded_node_obj->created_at_ts = $updated_node_created_at;
        $loaded_node_obj->save();

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(TestNode::DB_ID));

        $term_to_node_ids_arr = \Tests\TestTermToNode::getIdsArrForNodeIdByCreatedAtDesc($node_id);
        $this->assertEquals(1, count($term_to_node_ids_arr), 'wrong number of term to node records');

        foreach ($term_to_node_ids_arr as $term_to_node_id){
            $term_to_node_obj = \Tests\TestTermToNode::factory($term_to_node_id);
            $this->assertEquals($updated_node_created_at, $term_to_node_obj->created_at_ts);
        }

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(TestTermToNode::DB_ID));
    }

    public function testSingleAfterSaveCall(){
        \PHPModelDemo\ModelDemoConfig::init();

        $test_obj = new TestModel();
        $test_obj->after_save_counter = 0;
        $test_obj->save();

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(TestNode::DB_ID));

        $this->assertEquals(1, $test_obj->after_save_counter);
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
        $new_node_obj->title = $test_title;
        $new_node_obj->save();

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(TestNode::DB_ID));

        $node_id = $new_node_obj->getId();

        $loaded_node_obj = \Tests\TestNode::factory($node_id);
        $this->assertEquals($loaded_node_obj->body, $test_title . $test_title);

        // проверим как вызывается при сохранении существующего объекта

        $test_title_2 = rand(1, 1000000);

        $loaded_node_obj->title = $test_title_2;
        $loaded_node_obj->save();

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(TestNode::DB_ID));

        $loaded_node_obj_2 = \Tests\TestNode::factory($node_id);
        $this->assertEquals($loaded_node_obj_2->body, $test_title_2 . $test_title_2);

        // ensure we have no active transaction
        $this->assertEquals(false, DB::inTransaction(TestNode::DB_ID));
    }

}