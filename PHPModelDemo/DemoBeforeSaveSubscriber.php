<?php

namespace PHPModelDemo;
use OLOG\Model\ModelBeforeSaveCallbackInterface;

class DemoBeforeSaveSubscriber implements ModelBeforeSaveCallbackInterface {
    public static function beforeSave($obj) {
        error_log("Perform before save callback ");
    }
}
