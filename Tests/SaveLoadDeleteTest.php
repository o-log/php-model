<?php

namespace Tests;

class SaveLoadDeleteTest extends \PHPUnit_Framework_TestCase
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
            \Tests\TestModel::DB_ID,
            'select id from ' . \Tests\TestModel::DB_TABLE_NAME . ' where id = ?',
            array($test_model_id)
        );

        $this->assertEquals(0, count($test_model_ids_arr)); // проверяем что записей с таким ИД в таблице нет
    }
}