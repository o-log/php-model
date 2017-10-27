<?php

namespace Tests;

use Config\Config;
use OLOG\DB\DB;
use OLOG\Model\ModelConfig;

class LoadTest extends \PHPUnit_Framework_TestCase
{
    public function testLoadWithMissingFieldWithException()
    {
        Config::init();

        $new_model_obj = new \Tests\LoadTestModel();
        ModelConfig::setIgnoreMissingPropertiesOnSave(true);
        $new_model_obj->save();

        $test_model_id = $new_model_obj->getId();
        $this->assertNotEmpty($test_model_id);

        // test missing property exception

        ModelConfig::setIgnoreMissingPropertiesOnLoad(false);
        $exception_message = '';

        try {
            $loaded_model_obj = \Tests\LoadTestModel::factory($test_model_id);
        } catch (\Exception $e){
            $exception_message = $e->getMessage();
        }

        $this->assertEquals('Missing "extra_field" property in class "Tests\LoadTestModel" while property is present in DB table "tests_loadtestmodel"', $exception_message);

        // ensure we have no active transaction after load
        $this->assertEquals(false, DB::inTransaction(LoadTestModel::DB_ID));
    }

    public function testLoadWithMissingFieldWithoutException()
    {
        Config::init();

        $new_model_obj = new \Tests\LoadTestModel();
        $new_model_obj->save();

        $test_model_id = $new_model_obj->getId();
        $this->assertNotEmpty($test_model_id);

        // test disabled missing property exception

        ModelConfig::setIgnoreMissingPropertiesOnLoad(true);

        $loaded_model_obj = \Tests\LoadTestModel::factory($test_model_id);

        // ensure we have no active transaction after load
        $this->assertEquals(false, DB::inTransaction(LoadTestModel::DB_ID));
    }
}