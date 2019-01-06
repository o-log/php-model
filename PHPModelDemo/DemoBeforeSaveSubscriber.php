<?php
declare(strict_types=1);

/**
 * @author Oleg Loginov <olognv@gmail.com>
 */

namespace PHPModelDemo;
use OLOG\Model\ModelBeforeSaveCallbackInterface;

class DemoBeforeSaveSubscriber implements ModelBeforeSaveCallbackInterface {
    public static function beforeSave($obj) {
        error_log("Perform before save callback ");
    }
}
