<?php

namespace OLOG\Model;

use OLOG\CheckClassInterfaces;

class ModelConfig {

    static protected $afterSaveSubscribers = [];
    static protected $beforeSaveSubscribers = [];

    public static function getBeforeSaveSubscribers(string $model_class_name) {
        return self::$beforeSaveSubscribers[$model_class_name] ?? [];
    }

    public static function addBeforeSaveSubscribers(string $model_class_name, string $before_save_callback_class_name) {
        CheckClassInterfaces::exceptionIfClassNotImplementsInterface($before_save_callback_class_name, ModelBeforeSaveCallbackInterface::class);
        if (!isset(self::$beforeSaveSubscribers[$model_class_name])) {
            self::$beforeSaveSubscribers[$model_class_name] = [];
        }

        self::$beforeSaveSubscribers[$model_class_name][] = $before_save_callback_class_name;
    }

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