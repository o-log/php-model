<?php

class ModelTest extends PHPUnit_Framework_TestCase
{
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
}