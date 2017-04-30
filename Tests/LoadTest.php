<?php

namespace Tests;

use OLOG\Model\ModelConfig;

class LoadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Тест проверяет создание, сохранение, загрузку и удаление объекта через activeRecord и factory
     */
    public function testLoadWithException()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        $new_model = new \Tests\LoadTestModel();
        $new_model->save();

        $test_model_id = $new_model->getId();
        $this->assertNotEmpty($test_model_id);

        // test missing property exception

        ModelConfig::setIgnoreMissingPropertiesOnLoad(false);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing "extra_field" property in class "Tests\LoadTestModel" while property is present in DB table "tests_loadtestmodel"');

        $loaded_model_obj = \Tests\LoadTestModel::factory($test_model_id);
    }

    public function testLoadWithoutException()
    {
        \PHPModelDemo\ModelDemoConfig::init();

        $new_model = new \Tests\LoadTestModel();
        $new_model->save();

        $test_model_id = $new_model->getId();
        $this->assertNotEmpty($test_model_id);

        // test disabled missing property exception

        ModelConfig::setIgnoreMissingPropertiesOnLoad(true);

        $loaded_model_obj = \Tests\LoadTestModel::factory($test_model_id);
    }
}