<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace PHPModelDemo;
use OLOG\Model\ModelAfterSaveCallbackInterface;

class DemoAfterSaveSubscriber implements ModelAfterSaveCallbackInterface {
    public static function afterSave($id) {
        $model = CallbacksDemoModel::factory($id);
        error_log("Perform after save callback. id=" . $model->getId());
    }
}
