<?php

namespace OLOG\Model;

use OLOG\CheckClassInterfaces;

class ModelConfig {

    static protected $afterSaveSubscribers = [];

    public static function addAfterSaveSubscriber(string $model_class_name, string $after_save_callback_class_name) {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($after_save_callback_class_name, ModelAfterSaveCallbackInterface::class);
        if (!isset(self::$afterSaveSubscribers[$model_class_name])) {
            self::$afterSaveSubscribers[$model_class_name] = [];
        }

        self::$afterSaveSubscribers[$model_class_name][] = $after_save_callback_class_name;
    }

    public static function getAfterSaveSubscribersArr(string $model_class_name) {
        return self::$afterSaveSubscribers[$model_class_name] ?? [];
    }
}