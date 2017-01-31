<?php
namespace PHPModelDemo;
use OLOG\Model\ModelAfterSaveCallbackInterface;

class DemoAfterSaveSubscriber implements ModelAfterSaveCallbackInterface {
    public static function afterSave() {
        error_log("Perform after save callback 1");
    }
}