<?php

namespace Tests;

use Config\Config;
use OLOG\DB\DB;
use OLOG\Model\ModelConfig;

class AfterLoadTest extends \PHPUnit_Framework_TestCase
{
    public function testAfterLoad()
    {
        Config::init();

        $new_model_obj = new AfterLoadTestModel();
        $new_model_obj->save();

        $test_model_id = $new_model_obj->getId();
        $this->assertNotEmpty($test_model_id);

        try {
            $loaded_model_obj = AfterLoadTestModel::factory($test_model_id);
        } catch (\Exception $e){
            $exception_message = $e->getMessage();
        }

        $this->assertEquals(
            $loaded_model_obj->__original->randint,
            $new_model_obj->randint
        );

        // ensure we have no active transaction after load
        $this->assertEquals(false, DB::inTransaction(LoadTestModel::DB_ID));
    }
}
