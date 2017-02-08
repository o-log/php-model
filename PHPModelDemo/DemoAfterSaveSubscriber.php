<?php
namespace PHPModelDemo;
use OLOG\Model\ModelAfterSaveCallbackInterface;

class DemoAfterSaveSubscriber implements ModelAfterSaveCallbackInterface {

    public static function afterSave($id) {
        $model = SomeModel::factory($id);
        error_log("Perform after save callback. id=" . $model->getId());
        $model->save();
    }
}