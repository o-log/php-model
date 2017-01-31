<?php

namespace OLOG\Model;

use ModelAfterSaveCallbackInterface;
use OLOG\Assert;

class ModelConfig {

    static protected $afterSaveSubscribers = [];

    public static function addAfterSaveSubscriber(string $model_class_name, string $after_save_callback_class_name) {
        $interfaces = class_implements($model_class_name);
        Assert::assert(isset($interfaces[ModelAfterSaveCallbackInterface::class]));
        if (!isset(self::$afterSaveSubscribers[$model_class_name])) {
            self::$afterSaveSubscribers[$model_class_name] = [];
        }

        self::$afterSaveSubscribers[$model_class_name][] = $after_save_callback_class_name;
    }

    public static function getAfterSaveSubscribersArr(string $model_class_name) {
        return self::$afterSaveSubscribers[$model_class_name];
    }
}